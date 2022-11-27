<?php

namespace Balemy\LdapCommander\Group;

use Yiisoft\Form\FormModel;

class GroupForm extends FormModel
{
    protected ?string $title = null;
    protected ?string $description = null;

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


    public function getRules(): array
    {
        return [
            // Define validation rules here
        ];
    }

    public function loadGroup(Group $group): void
    {
        $this->title = $group->getTitle();
        $this->description = $group->getDescription();

    }
}
