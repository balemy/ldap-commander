<?php

namespace Balemy\LdapCommander\Modules\GroupManager;

use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RulesProviderInterface;

class GroupForm extends FormModel implements RulesProviderInterface
{
    protected ?string $title = null;
    protected ?string $description = null;
    protected string $parentDn = '';

    public function getPropertyLabels(): array
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
