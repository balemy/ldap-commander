<?php

namespace Balemy\LdapCommander\Group;

use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Rule\Required;

class GroupForm extends FormModel
{
    protected ?string $title = null;
    protected ?string $description = null;
    protected string $parentDn = '';

    public function getAttributeLabels(): array
    {
        return [
            'parentDn' => 'Organizational Unit'
        ];
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }


    public function getParentDn(): string
    {
        return $this->parentDn;
    }

    public function getRules(): array
    {
        return [
            'title' => [new Required()],
            'parentDn' => [new Required()],
        ];
    }

    public function loadGroup(Group $group): void
    {
        $this->title = $group->getTitle();
        $this->description = $group->getDescription();
        $this->parentDn = $group->getParentDn();

    }
}
