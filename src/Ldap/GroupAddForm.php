<?php

namespace App\Ldap;

use Yiisoft\Validator\Rule\Required;

class GroupAddForm extends GroupForm
{

    protected array $initialMembers = [];

    /**
     * @return string[]
     */
    public function getInitialMembers(): array
    {
        return $this->initialMembers;
    }

    public function getRules(): array
    {
        return [
            'title' => [new Required()],
            'initialMembers' => [new Required()],
        ];
    }

}
