<?php

namespace Balemy\LdapCommander\LDAP;

use Balemy\LdapCommander\LDAP\Services\SchemaService;
use LdapRecord\Models\Entry;
use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RulesProviderInterface;

class LdapFormModel extends FormModel implements RulesProviderInterface, DataSetInterface
{
    private Entry $lrEntry;

    public bool $isNewRecord = false;

    /**
     * @var string[] Properties loaded for modification
     */
    private array $loadedProperties = [];

    /**
     * @var string[] Properties which are not synced with the LR Entry object
     */
    protected array $noEntryProperties = ['parentDn'];

    /**
     * @var string[] Required object classes to be valid. Added for new Entries
     */
    protected array $requiredObjectClasses = [];

    public function __construct(private ?string $dn, private SchemaService $schemaService)
    {
        if ($dn !== null) {
            /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
            $this->lrEntry = Entry::query()->addSelect(['*', '+'])->findOrFail($dn);
            // ToDo: Loading implementes required ObjectClass
        } else {
            $this->lrEntry = new Entry();
            $this->lrEntry->setFirstAttribute('objectclass', $this->requiredObjectClasses);

            $this->isNewRecord = true;
        }
    }

    /**
     * @inheritDoc
     */
    public function getRules(): iterable
    {
        $rules = [];
        foreach ($this->schemaService->getMustAttributes($this->getObjectClasses()) as $name) {
            $rules[$name] = [new Required()];
        }
        $rules['parentDn'] = [new Required()];

        return $rules;
    }

    public function load($data): bool
    {
        if (is_array($data[$this->getFormName()])) {
            foreach ($data[$this->getFormName()] as $name => $value) {
                if ($this->hasProperty($name)) {
                    $this->loadedProperties[$name] = $value;
                } else {
                    print "no such attribute " . $name;
                    die();
                }
            }
            return true;
        }
        return false;
    }

    public function save(): bool
    {
        if ($this->isNewRecord) {
            $this->lrEntry->inside($this->getPropertyValue('parentDn'));
        }

        foreach ($this->loadedProperties as $name => $value) {
            if (in_array($name, $this->noEntryProperties)) {
                continue;
            }
            if ($this->isNewRecord && empty($value)) {
                continue; // Skip empty attributes on insert
            }
            $this->lrEntry->setFirstAttribute($name, $value);
        }
        $this->lrEntry->save();
        $this->lrEntry->refresh();
        $this->dn = $this->lrEntry->getDn();

        return true;
    }

    public function getDn(): string
    {
        return $this->dn ?? '';
    }


    public function getPropertyValue(string $property): mixed
    {
        //ToDo: Check hasAttribute, use loadedAttributes?
        if (isset($this->loadedProperties[$property])) {
            return $this->loadedProperties[$property];
        }

        return $this->lrEntry->getFirstAttribute($property);
    }

    public function getPropertyLabels(): array
    {
        return [];
    }

    public function getPropertyHints(): array
    {
        return [];
    }

    public function hasProperty(string $property): bool
    {
        if (in_array($property, ['parentDn'])) {
            return true;
        }

        if ($this->schemaService->hasAttribute($property, $this->getObjectClasses())) {
            return true;
        }

        return false;
    }


    // Required by DataSetInterface
    public function getAttributeValue(string $attribute): mixed
    {
        return $this->getPropertyValue($attribute);
    }

    // Required by DataSetInterface
    public function getData(): ?array
    {
        return $this->loadedProperties;
    }

    // Required by DataSetInterface
    public function hasAttribute(string $attribute): bool
    {
        return $this->hasProperty($attribute);
    }

    /**
     * @return string[]
     */
    private function getObjectClasses(): array
    {
        return (array)$this->lrEntry->getAttribute('objectclass');
    }
}
