<?php

namespace App\Ldap;

use App\Helper\DnHelper;
use App\Ldap\Schema\Schema;
use LdapRecord\LdapRecordException;
use LdapRecord\Models\Entry;
use Yiisoft\Form\FormModel;

class EntityForm extends FormModel
{
    private string $rdnAttribute = '';

    public function __construct(public Schema $schema, public Entry $entry, public bool $isNewRecord = false, public string $parentDn = '')
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


    public function load(array $data, ?string $formName = null): bool
    {
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
            foreach ($rawData['objectclass'] as $objectClassName) {
                $objectClass = $this->schema->getObjectClass($objectClassName);
                if ($objectClass !== null) {
                    $validAttributes = array_merge($validAttributes, $objectClass->getAttributeIds());
                }
            }
        }

        $rawData = array_filter($rawData, function ($_v, $k) use ($validAttributes) {
            return (in_array($k, $validAttributes));
        }, ARRAY_FILTER_USE_BOTH);

        return parent::load($rawData, '');
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

}
