<?php

namespace Balemy\LdapCommander\Group;

use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RulesProviderInterface;

class GroupAddForm extends GroupForm implements RulesProviderInterface
{
    /**
     * @var string[]
     */
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
        return ArrayHelper::merge(parent::getRules(), [
            'initialMembers' => [new Required()],
        ]);
    }

}
