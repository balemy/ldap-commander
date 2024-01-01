<?php

namespace Balemy\LdapCommander\Modules\RawQuery;

use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RulesProviderInterface;


class QueryForm extends FormModel implements RulesProviderInterface
{
    public string $query = '';

    /**
     * @inheritDoc
     */
    public function getRules(): iterable
    {
        $rules = [];
        $rules['query'] = [new Required()];
        return $rules;
    }

    public function getFormName(): string
    {
        return 'RawQueryForm';
    }
}
