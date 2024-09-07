<?php

namespace Balemy\LdapCommander\Modules\EntityBrowser;

use Balemy\LdapCommander\LDAP\Helper\DnHelper;
use Balemy\LdapCommander\LDAP\Schema\Schema;
use LdapRecord\LdapRecordException;
use LdapRecord\Models\Entry;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RulesProviderInterface;

class EntityForm extends FormModel implements RulesProviderInterface, DataSetInterface
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
        ;
    }

    public function hasProperty(string $property): bool
    {
        return (array_key_exists($property, $this->collectAttributes()));
    }

    public function getPropertyValue(string $property): mixed
    {
        if ($property === 'rdnAttribute') {
            return $this->getRdnAttributeId();
        } elseif ($property === 'objectclass' && is_array($this->entry->getAttribute($property))) {

            // We're working only with lowerclass objectclass names.
            // Schema returns  objectclass keys as lc
            /** @var string[] $val */
            $val = $this->entry->$property;
            return array_map('strtolower', $val);
        }

        return $this->entry->$property;
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

    public function preloadAttributesFromEntry(Entry $duplicate): void
    {
        $this->entry->setRawAttributes($duplicate->getAttributes());

        $dn = $duplicate->getDn();
        if ($dn !== null) {
            $rdns = DnHelper::getRdns($dn);
            $this->rdnAttribute = DnHelper::getRdnAttributeName($rdns[0]);
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
        $val = $this->getPropertyValue($attribute);

        if (is_array($val)) {
            return $val;
        } elseif (empty($val)) {
            return [];
        } else {
            return [$val];
        }
    }

    public function getPropertyLabel(string $property): string
    {
        if (!isset($this->schema->attributeTypes[$property])) {
            return '';
        }

        $attributeType = $this->schema->attributeTypes[$property];

        return implode(', ', $attributeType->names);
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

        /** @var string $v */
        foreach ($rawData as $k => $v) {
            /** @var string $k */
            $this->setAttribute($k, $v);
        }

        return true;
    }

    private function getValidAttributes(array $objectClasses = []): array
    {
        $validAttributes = ['rdnAttribute', 'objectclass'];
        /** @var string $objectClassName */
        foreach ($objectClasses as $objectClassName) {
            $objectClass = $this->schema->getObjectClass($objectClassName);
            $validAttributes = array_merge($validAttributes, $objectClass->getAttributeIds(true));
        }

        return $validAttributes;
    }

    /**
     * @throws LdapRecordException
     *
     */
    public function save(): void
    {
        if ($this->isNewRecord) {
            $rdnAttribute = (string)$this->getPropertyValue('rdnAttribute');
            $rdnAttributeValue = (string)$this->getAttributeValueAsArray($rdnAttribute)[0];

            $rdn = $this->entry->getCreatableRdn($rdnAttributeValue, $rdnAttribute);
            $newDn = $rdn . ',' . $this->parentDn;
            $this->entry->setDn($newDn);

            $this->entry->insert($newDn, $this->getEntryAttributesAsArray());
        } else {
            // We need to add the RDN attribute value manually, since the input is disabled
            $this->entry->addAttributeValue($this->getRdnAttributeId(), $this->getRdnAttributeValue());

            // It's not possible to modify/add/del binary data. So make sure to delete and recreate it.
            foreach (array_keys($this->schema->getBinaryAttributes()) as $attribute) {
                assert(is_string($attribute));
                if ($this->entry->isDirty($attribute) && !empty($this->entry->getOriginal()[$attribute])) {
                    /** @var string $binaryData */
                    $binaryData = $this->entry->getPropertyValue($attribute);
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

    public function getRdnAttributeId(): string
    {
        if (!empty($this->rdnAttribute)) {
            return $this->rdnAttribute;
        }

        $dn = $this->entry->getDn();
        if ($dn !== null) {
            $rdns = DnHelper::getRdns($dn);
            return DnHelper::getRdnAttributeName($rdns[0]);
        }

        return '';
    }

    public function getRdnAttributeValue(): string
    {
        $dn = $this->entry->getDn();
        if ($dn !== null) {
            $rdns = DnHelper::getRdns($dn);
            return DnHelper::getRdnAttributeValue($rdns[0]);
        }

        return '';
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
            $requiredAttributes = ArrayHelper::merge($requiredAttributes, $objectClass->mustAttributes);
        }

        foreach ($this->schema->attributeTypes as $attribute => $attributeType) {
            $r = [];
            if (in_array($attribute, $validAttributes)) {
                if ($attributeType->syntax === '1.3.6.1.4.1.1466.115.121.1.27') {
                    $r[] = new Number(skipOnEmpty: true);
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

    public function getData(): ?array
    {
        return $this->getEntryAttributesAsArray();
    }

    public function processValidationResult(Result $result): void
    {
        foreach ($result->getErrorMessagesIndexedByPath() as $name => $errors) {
            if (str_contains($name, '.')) {
                $res = explode('.', $name, 2);
                if (count($res) === 2) {
                    $this->formValidationErrorsIndexed[$res[0] . '[' . $res[1] . ']'] = $errors;
                }
            }
        }

        parent::processValidationResult($result);
    }


    public function getAttributeValue(string $attribute): mixed
    {
        return $this->getPropertyValue($attribute);
    }

    /*
    public function hasAttribute(string $attribute): bool
    {
        return $this->hasProperty($attribute);
    }
    */
}
