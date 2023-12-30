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
     * @psalm-var array<string, string|array>
     * @var string[] Properties loaded for modification
     */
    protected array $loadedProperties = [];

    /**
     * @var string[] Properties which are not synced with the LR Entry object
     */
    protected array $noEntryProperties = ['parentDn'];

    protected array $customProperties = [];

    public array $requiredObjectClasses = [];

    /**
     * @var string The current or head attribute for new entries.
     */
    protected string $headAttribute = 'cn';


    /**
     * @psalm-suppress PropertyTypeCoercion
     */
    public function __construct(private ?string $dn, private SchemaService $schemaService)
    {
        if ($dn !== null) {
            /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
            $this->lrEntry = Entry::query()->addSelect(['*', '+'])->findOrFail($dn);
            $this->headAttribute = (string)$this->lrEntry->getHead();
            $this->loadedProperties['parentDn'] = (string)$this->lrEntry->getParentDn();
            // ToDo: Loading only entries which implements required ObjectClass
        } else {
            $this->lrEntry = new Entry();
            $this->lrEntry->setAttribute('objectclass', $this->requiredObjectClasses);
            $this->isNewRecord = true;
        }

        $this->init();
    }

    protected function init(): void
    {

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
            /** @var array<string,string> $formData */
            $formData = $data[$this->getFormName()];
            foreach ($formData as $name => $value) {
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

        $headRdn = $this->headAttribute . '=' . $this->getLoadedPropertyValueAsString($this->headAttribute);
        if ($this->isNewRecord) {
            $parentDn = (string)$this->getPropertyValue('parentDn');
            $this->lrEntry->inside($parentDn);
            $this->lrEntry->setDn($headRdn . ',' . $parentDn);
        } else {
            if ($this->isHeadAttributeChanged()) {
                $this->lrEntry->rename($headRdn);
            }
        }

        $this->lrEntry->save();

        if (!$this->isNewRecord && $this->lrEntry->getParentDn() !== $this->loadedProperties['parentDn']) {
            $this->lrEntry->move($this->getLoadedPropertyValueAsString('parentDn'));
        }

        $this->lrEntry->refresh();
        $this->dn = $this->lrEntry->getDn();

        return true;
    }

    private function isHeadAttributeChanged(): bool
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

    private function getLoadedPropertyValueAsString(string $property): string
    {
        if (is_array($this->loadedProperties[$property])) {
            return '';
        }
        return $this->loadedProperties[$property];
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
     * @psalm-suppress MixedReturnTypeCoercion
     * @return string[]
     */
    private function getObjectClasses(): array
    {
        return (array)$this->lrEntry->getAttribute('objectclass');
    }


}
