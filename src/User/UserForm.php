<?php

namespace Balemy\LdapCommander\User;

use Balemy\LdapCommander\ApplicationParameters;
use Balemy\LdapCommander\Ldap\LdapService;
use Balemy\LdapCommander\Schema\AttributeType;
use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RulesProviderInterface;


class UserForm extends FormModel implements RulesProviderInterface
{
    public User $user;
    private array $_attrs = [];

    private array $internalAttrs = ['parentDn'];
    private UserFormSchema $formSchema;

    private LdapService $ldapService;

    public function __construct(ApplicationParameters $applicationParameters, LdapService $ldapService)
    {
        $this->formSchema = new UserFormSchema($applicationParameters);
        $this->user = new User();
        $this->ldapService = $ldapService;
        parent::__construct();
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
        $this->loadByEntry();
    }

    public function setAttribute(string $name, mixed $value): void
    {
        $this->_attrs[$name] = $value;
    }

    public function getAttributeCastValue(string $attribute): mixed
    {
        return $this->_attrs[$attribute] ?? '';
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
        $res = $this->user->getAttributeValue($name);
        if (is_array($res)) {
            if (isset($res[0]) && is_string($res[0])) {
                return $res[0];
            }
        }
        return '';
    }

    public function getAttributeLabels(): array
    {
        /** @var string[] */
        return array_merge([
            'parentDn' => 'Organizational Unit'
        ], $this->formSchema->getFields());
    }

    /**
     * @inheritDoc
     * @return string[]
     */
    protected function collectAttributes(): array
    {
        $fields = ['parentDn' => 'string'];
        foreach (array_keys($this->formSchema->getFields()) as $attribute) {
            $fields[$attribute] = 'string';
        }
        return $fields;
    }

    public function updateEntry(): bool
    {
        /** @var string $attributeName */
        foreach (array_keys($this->collectAttributes()) as $attributeName) {
            if ($attributeName === 'userPassword') {
                $password = (string)$this->getAttributeValue('userPassword');
                if (!empty($password)) {
                    $salt = mt_rand(0, mt_getrandmax());
                    $value = '{SSHA}' . base64_encode(sha1($password . $salt, TRUE) . $salt);
                    $this->user->setFirstAttribute('userPassword', $value);
                }
            } elseif (!in_array($attributeName, $this->internalAttrs)) {
                $val = (string) $this->getAttributeValue($attributeName);
                if ($this->isNewRecord() && empty($val)) {
                    // On new record, don't set empty attribute values
                } else {
                    $this->user->setFirstAttribute($attributeName, $this->getAttributeValue($attributeName));
                }
            }
        }

        $moveToDn = null;

        $parentDn = (string)$this->getAttributeValue('parentDn');
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
                $this->setAttribute($attributeName,
                    $this->user->getFirstAttribute($attributeName));
            }
        }

        $this->setAttribute('parentDn', $this->user->getParentDn() ?? '');
    }

    public function isNewRecord(): bool
    {
        return !$this->user->exists;
    }

}
