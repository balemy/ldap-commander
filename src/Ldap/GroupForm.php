<?php

namespace App\Ldap;

use Yiisoft\Form\FormModel;

class GroupForm extends FormModel
{
    private ?string $title = null;
    private ?string $description = null;

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