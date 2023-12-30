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
    protected Entry $lrEntry;

    public bool $isNewRecord = false;

    /**
     * @var string[] Properties loaded for modification
     */
    protected array $loadedProperties = [];

    /**
     * @var string[] Properties which are not synced with the LR Entry object
     */
    protected array $noEntryProperties = ['parentDn'];

    /**
     * @var string[] Custom Properties
     */
    protected array $customProperties = [];

    /**
     * @var string[] Required object classes to be valid. Added for new Entries
     */
    protected array $requiredObjectClasses = [];

    /**
     * @var string The current or head attribute for new entries.
     */
    protected string $headAttribute = 'cn';


    public function __construct(private ?string $dn, private SchemaService $schemaService)
    {
        if ($dn !== null) {
            /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
            $this->lrEntry = Entry::query()->addSelect(['*', '+'])->findOrFail($dn);
            $this->headAttribute = (string)$this->lrEntry->getHead();
            $this->loadedProperties['parentDn'] = $this->lrEntry->getParentDn();
            // ToDo: Loading only entries which implements required ObjectClass
        } else {
            $this->lrEntry = new Entry();
            $this->lrEntry->setAttribute('objectclass', $this->requiredObjectClasses);
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
        $rules[$this->headAttribute] = [new Required()];

        return $rules;
    }

    public function load(array $data): bool
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
        foreach ($this->loadedProperties as $name => $value) {
            if (in_array($name, $this->noEntryProperties) || in_array($name, $this->customProperties)) {
                continue; // Skip not LrEntry Related Properties (e.g. parentDn)
            }
            if ($this->isNewRecord && empty($value)) {
                continue; // Skip empty attributes on insert
            }
            if (!$this->isNewRecord && $name === $this->headAttribute) {
                continue; // Skip head attribute. Rename required (below).
            }
            $this->lrEntry->setFirstAttribute($name, $value);
        }

        $headRdn = $this->headAttribute . '=' . (string)$this->loadedProperties[$this->headAttribute];
        if ($this->isNewRecord) {
            $parentDn = $this->getPropertyValue('parentDn');
            $this->lrEntry->inside($parentDn);
            $this->lrEntry->setDn($headRdn . ',' . $parentDn);
        } else {
            if ($this->isHeadAttributeChanged()) {
                $this->lrEntry->rename($headRdn);
            }
        }

        $this->lrEntry->save();

        if (!$this->isNewRecord && $this->lrEntry->getParentDn() !== $this->loadedProperties['parentDn']) {
            $this->lrEntry->move($this->loadedProperties['parentDn']);
        }

        $this->lrEntry->refresh();
        $this->dn = $this->lrEntry->getDn();

        return true;
    }

    private function isHeadAttributeChanged()
    {
        if ($this->isNewRecord) {
            return false;
        }
        $head = $this->headAttribute;
        return ($this->lrEntry->getFirstAttribute($head) !== $this->loadedProperties[$head]);

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
        return [
            'parentDn' => 'Organizational Unit'
        ];
    }

    public function getPropertyHints(): array
    {
        return [];
    }

    public function hasProperty(string $property): bool
    {
        if (in_array($property, ['parentDn']) || in_array($property, $this->customProperties)) {
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
