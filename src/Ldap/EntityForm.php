<?php

namespace App\Ldap;

use App\Helper\DnHelper;
use App\Ldap\Schema\Schema;
use LdapRecord\LdapRecordException;
use LdapRecord\Models\Entry;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;

class EntityForm extends FormModel
{
    private string $rdnAttribute = '';

    /**
     * @var array<string, array<array-key, string>>
     */
    public array $formValidationErrorsIndexed = [];

    public function __construct(
        public Schema $schema,
        public Entry  $entry,
        public bool   $isNewRecord = false,
        public string $parentDn = '')
    {
        parent::__construct();
    }

    public function getAttributeCastValue(string $attribute): mixed
    {
        if ($attribute === 'rdnAttribute') {
            if ($this->isNewRecord) {
                return '';
            } else {
                $dn = $this->entry->getDn();
                if ($dn !== null) {
                    $rdns = DnHelper::getRdns($dn);
                    return DnHelper::getRdnAttributeName($rdns[0]);
                }

                return '';
            }
        } elseif ($attribute === 'objectclass' && is_array($this->entry->getAttribute($attribute))) {

            // We're working only with lowerclass objectclass names.
            // Schema returns  objectclass keys as lc
            /** @var string[] $val */
            $val = $this->entry->$attribute;
            return array_map('strtolower', $val);
        }

        return $this->entry->$attribute;
    }


    /**
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function setAttribute(string $name, mixed $value): void
    {
        if ($name === 'distinguishedname') {
#            $this->entry->dn = $value[0];
        } elseif ($name === 'rdnAttribute') {
            if (is_array($value)) {
                /** @var array<array-key, string> $value */
                $this->rdnAttribute = $value[0];
            } elseif (is_string($value)) {
                $this->rdnAttribute = $value;
            }
        } elseif ($this->isBinaryAttribute($name) && is_array($value)) {
            $currentValue = $this->entry->getAttributeValue($name);
            assert(is_array($currentValue));

            /** @var array<int, string> $value */
            foreach ($value as $i => $v) {
                if (empty($v)) {
                    unset($currentValue[$i]);
                } else {
                    $currentValue[$i] = $v;
                }
            }
            $this->entry->$name = $currentValue;
        } else {
            $this->entry->$name = $value;
        }

    }

    /**
     * {@inheritDoc}
     */
    protected function collectAttributes(): array
    {
        $attributes = [];
        $attributes['rdnAttribute'] = 'cn';

        foreach (array_keys($this->schema->attributeTypes) as $key) {
            $attributes[$key] = '';
        }

        return $attributes;
    }

    public function getAttributeValueAsArray(string $attribute): array
    {
        /** @var array|string $val */
        $val = $this->getAttributeValue($attribute);

        if (is_array($val)) {
            return $val;
        } elseif (empty($val)) {
            return [];
        } else {
            return [$val];
        }
    }

    public function getAttributeLabel(string $attribute): string
    {
        if (!isset($this->schema->attributeTypes[$attribute])) {
            return '';
        }

        $attributeType = $this->schema->attributeTypes[$attribute];

        return implode(', ', $attributeType->names);
    }

    /**
     * @return false
     */
    public function isAttributeEmpty(string $attribute): bool
    {
        return false;
    }


    public function load(object|array|null $data, ?string $formName = null): bool
    {
        if (!is_array($data)) {
            return false;
        }

        $scope = $formName ?? $this->getFormName();

        $rawData = [];
        if ($scope === '' && !empty($data)) {
            $rawData = $data;
        } elseif (isset($data[$scope])) {
            /** @var array<string, array> */
            $rawData = $data[$scope];
        }

        $validAttributes = ['rdnAttribute', 'objectclass'];
        if (is_array($rawData['objectclass'])) {
            /** @var string $objectClassName */
            $validAttributes = $this->getValidAttributes($rawData['objectclass']);
        }

        $rawData = array_filter($rawData, function ($_v, $k) use ($validAttributes) {
            return (in_array($k, $validAttributes));
        }, ARRAY_FILTER_USE_BOTH);

        return parent::load($rawData, '');
    }

    private function getValidAttributes(array $objectClasses = []): array
    {
        $validAttributes = ['rdnAttribute', 'objectclass'];
        /** @var string $objectClassName */
        foreach ($objectClasses as $objectClassName) {
            $objectClass = $this->schema->getObjectClass($objectClassName);
            if ($objectClass !== null) {
                $validAttributes = array_merge($validAttributes, $objectClass->getAttributeIds(true));
            }
        }

        return $validAttributes;
    }

    /**
     * @throws LdapRecordException
     *
     */
    public function save(): void
    {
        $rdnAttribute = (string)$this->getAttributeValue('rdnAttribute');
        $rdnAttributeValue = (string)$this->getAttributeValueAsArray($rdnAttribute)[0];
        $rdn = $this->entry->getCreatableRdn($rdnAttributeValue, $rdnAttribute);

        if ($this->isNewRecord) {
            $newDn = $rdn . ',' . $this->parentDn;

            $this->entry->setDn($newDn);
            $this->entry->insert($newDn, $this->getEntryAttributesAsArray());
        } else {
            // It's not possible to modify/add/del binary data. So make sure to delete and recreate it.
            foreach (array_keys($this->schema->getBinaryAttributes()) as $attribute) {
                assert(is_string($attribute));
                if ($this->entry->isDirty($attribute) && !empty($this->entry->getOriginal()[$attribute])) {
                    /** @var string $binaryData */
                    $binaryData = $this->entry->getAttributeValue($attribute);
                    $this->entry->deleteAttribute($attribute);
                    $this->entry->addAttributeValue($attribute, $binaryData);
                }
            }

            $this->entry->save();
        }
    }

    /**
     * @param $attributeId
     * @return bool
     */
    public function isMultiValueAttribute(string $attributeId): bool
    {
        $attribute = $this->schema->getAttribute($attributeId);
        if ($attribute !== null) {
            if ($attribute->isSingleValue) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param $attributeId
     * @return bool
     */
    public function isBinaryAttribute(string $attributeId): bool
    {
        return in_array($attributeId, array_keys($this->schema->getBinaryAttributes()));
    }

    /**
     * @return string|null
     */
    public function getDn(): string|null
    {
        return $this->entry->getDn();
    }


    private function getEntryAttributesAsArray(): array
    {
        $attr = [];
        foreach ($this->entry->getAttributes() as $k => $v) {
            assert(is_string($k));
            assert(is_array($v));

            if (empty($v[0])) {
                continue;
            }

            $attr[strtolower($k)] = $v;
        }
        return $attr;
    }


    public function getRules(): array
    {
        //TODO: Add more rules
        //TODO: Check Single Value

        $rules = [];

        $validAttributes = $this->getValidAttributes($this->getAttributeValueAsArray('objectclass'));
        $requiredAttributes = [];
        /** @var string $objectClassName */
        foreach ($this->getAttributeValueAsArray('objectclass') as $objectClassName) {
            $objectClass = $this->schema->getObjectClass($objectClassName);
            if ($objectClass !== null) {
                $requiredAttributes = ArrayHelper::merge($requiredAttributes, $objectClass->mustAttributes);
            }
        }

        foreach ($this->schema->attributeTypes as $attribute => $attributeType) {
            $r = [];
            if (in_array($attribute, $validAttributes)) {
                if ($attributeType->syntax === '1.3.6.1.4.1.1466.115.121.1.27') {
                    $r[] = new Number(asInteger: true, skipOnEmpty: true);
                }
                if (in_array($attribute, $requiredAttributes)) {
                    $r[] = new Required();
                }
            }
            if (!empty($r)) {
                $rules[$attribute] = [new Each($r)];
            }
        }

        return $rules;

    }

    public function processValidationResult(Result $result): void
    {
        foreach ($result->getErrorMessagesIndexedByPath() as $name => $errors) {
            if (str_contains($name, '.')) {
                list($attributeName, $attributeIndex) = explode('.', $name, 2);
                $this->formValidationErrorsIndexed[$attributeName . '[' . $attributeIndex . ']'] = $errors;
            }
        }

        parent::processValidationResult($result);
    }
}
