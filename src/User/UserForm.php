<?php

namespace Balemy\LdapCommander\User;

use Balemy\LdapCommander\ApplicationParameters;
use Yiisoft\Form\FormModel;


class UserForm extends FormModel
{
    public User $user;
    private array $_attrs = [];

    private array $internalAttrs = ['parentDn'];


    public function __construct(private ApplicationParameters $applicationParameters)
    {
        parent::__construct();
        $this->user = new User();
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
        return [
            'parentDn' => 'Organizational Unit'
        ];
    }

    /**
     * @inheritDoc
     * @return string[]
     */
    protected function collectAttributes(): array
    {
        $fields = ['parentDn' => 'string'];
        $rows = $this->applicationParameters->getUserEditFields();
        /** @var string[] $row */
        foreach ($rows as $row) {
            foreach (array_keys($row) as $fieldKey) {
                $fields[$fieldKey] = 'string';
            }
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
                $this->user->setFirstAttribute($attributeName, $this->getAttributeValue($attributeName));
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
