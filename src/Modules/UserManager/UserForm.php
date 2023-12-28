<?php

namespace Balemy\LdapCommander\Modules\UserManager;

use Balemy\LdapCommander\ApplicationParameters;
use Balemy\LdapCommander\LDAP\LdapService;
use Balemy\LdapCommander\LDAP\Schema\AttributeType;
use Yiisoft\FormModel\Exception\PropertyNotSupportNestedValuesException;
use Yiisoft\FormModel\Exception\UndefinedArrayElementException;
use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RulesProviderInterface;


class UserForm extends FormModel implements RulesProviderInterface, DataSetInterface
{
    public User $user;
    private array $_attrs = [];

    private array $internalAttrs = ['parentDn'];
    private UserFormSchema $formSchema;

    private LdapService $ldapService;

    private ?array $collectedAttributes = null;


    public function __construct(ApplicationParameters $applicationParameters, LdapService $ldapService)
    {
        $this->formSchema = new UserFormSchema($applicationParameters);
        $this->user = new User();
        $this->ldapService = $ldapService;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
        $this->loadByEntry();
    }

    public function load(array $attributes): bool
    {
        /** @var array<string, string> $attributes */
        foreach ($attributes as $name => $value) {
            $this->setProperty($name, $value);
        }

        return true;
    }

    private function setProperty(string $name, mixed $value): void
    {
        if (!$this->hasProperty($name)) {
            throw new \Exception('Invalid ' . $name . ' exception!');
        }
        $this->_attrs[$name] = $value;
    }

    public function hasProperty(string $property): bool
    {
        return (array_key_exists($property, $this->collectAttributes()));
    }

    public function getPropertyValue(string $property): mixed
    {
        try {
            return $this->_attrs[$property] ?? '';
        } catch (PropertyNotSupportNestedValuesException $exception) {
            return $exception->getValue() === null
                ? null
                : throw $exception;
        } catch (UndefinedArrayElementException) {
            return null;
        }
    }

    /**
     * @return UserFormSchema
     */
    public function getFormSchema(): UserFormSchema
    {
        return $this->formSchema;
    }


    public function getRules(): array
    {
        /** @var AttributeType[] $requiredAttributes */
        $requiredAttributes = [];
        /** @var string $objectClassName */
        foreach ($this->user->getObjectClasses() as $objectClassName) {
            $oc = $this->ldapService->getSchema()->getObjectClass($objectClassName);
            if ($oc !== null) {
                $requiredAttributes = array_merge($requiredAttributes, $oc->getMustAttributes());
            }
        }

        $rules = [];
        foreach (array_keys($this->collectAttributes()) as $attribute) {
            foreach ($requiredAttributes as $requiredAttribute) {
                if (in_array($attribute, $requiredAttribute->names)) {
                    $rules[$attribute] = [new Required()];
                }
            }
        }
        return $rules;
    }

    private function getEntryValue(string $name): string
    {
        /**
         * @psalm-suppress MixedAssignment
         */
        $res = $this->user->getPropertyValue($name);
        if (is_array($res)) {
            if (isset($res[0]) && is_string($res[0])) {
                return $res[0];
            }
        }
        return '';
    }

    /**
     * @psalm-suppress LessSpecificImplementedReturnType
     * @psalm-suppress MixedReturnTypeCoercion
     */
    public function getPropertyLabels(): array
    {
        return array_merge([
            'parentDn' => 'Organizational Unit'
        ], $this->formSchema->getFields());
    }

    private function collectAttributes(): array
    {
        if (!$this->collectedAttributes) {
            $this->collectedAttributes = ['parentDn' => 'string'];
            foreach (array_keys($this->formSchema->getFields()) as $attribute) {
                $this->collectedAttributes[$attribute] = 'string';
            }
        }
        return $this->collectedAttributes;
    }

    public function updateEntry(): bool
    {
        /** @var string $attributeName */
        foreach (array_keys($this->collectAttributes()) as $attributeName) {
            if ($attributeName === 'userPassword') {
                $password = (string)$this->getPropertyValue('userPassword');
                if (!empty($password)) {
                    $salt = mt_rand(0, mt_getrandmax());
                    $value = '{SSHA}' . base64_encode(sha1($password . $salt, TRUE) . $salt);
                    $this->user->setFirstAttribute('userPassword', $value);
                }
            } elseif (!in_array($attributeName, $this->internalAttrs)) {
                $val = (string)$this->getPropertyValue($attributeName);
                if ($this->isNewRecord() && empty($val)) {
                    // On new record, don't set empty attribute values
                } else {
                    $this->user->setFirstAttribute($attributeName, $this->getPropertyValue($attributeName));
                }
            }
        }

        $moveToDn = null;

        $parentDn = (string)$this->getPropertyValue('parentDn');
        if ($this->isNewRecord()) {
            $this->user->inside($parentDn);
        } else {
            if ($this->user->getParentDn() !== $parentDn) {
                $moveToDn = $parentDn;
            }

            $head = $this->user->getHead();
            if ($head !== null && $this->user->isDirty($head)) {
                $this->user->rename((string)$this->user->getFirstAttribute($head));
                $this->user->refresh();
            }
        }

        $this->user->save();

        if ($moveToDn !== null) {
            $this->user->move($moveToDn);
            $this->user->refresh();
        }
        return true;
    }


    private function loadByEntry(): void
    {
        /** @var string $attributeName */
        foreach (array_keys($this->collectAttributes()) as $attributeName) {
            if (!in_array($attributeName, $this->internalAttrs) && $attributeName !== 'userPassword') {
                $this->setProperty($attributeName,
                    $this->user->getFirstAttribute($attributeName));
            }
        }

        $this->setProperty('parentDn', $this->user->getParentDn() ?? '');
    }

    public function isNewRecord(): bool
    {
        return !$this->user->exists;
    }

    public function getData(): ?array
    {
        return $this->_attrs;
    }

    public function getAttributeValue(string $attribute): mixed
    {
        return $this->getPropertyValue($attribute);
    }

    public function hasAttribute(string $attribute): bool
    {
        return $this->hasProperty($attribute);
    }
}
