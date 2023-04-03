<?php

namespace Balemy\LdapCommander\ServerConfig\ReferentialIntegrity;

use Yiisoft\Form\FormModel;
use Yiisoft\Validator\RulesProviderInterface;

class RefForm extends FormModel implements RulesProviderInterface
{

    private ?bool $enabled = null;
    private string $refintAttribute = '';
    private string $refintNothing = '';

    public function getAttributeLabels(): array
    {
        return [
            'enabled' => 'Load module',
            'refintAttribute' => 'Maintained Attributes',
            'refintNothing' => 'Nothing Fallback DN'
        ];
    }

    public function getAttributeHints(): array
    {
        return [
            'refintAttribute' => 'This parameter specifies a space separated list of attributes which will have the referential integrity maintained.',
            'refintNothing' => 'Some times, while trying to maintain the referential integrity, the server has to remove the last attribute of its kind from an entry. This may be prohibited by the schema: for example, the groupOfNames object class requires at least one member. In these cases, the server will add the attribute value specified in refint_nothing to the entry.'
        ];
    }

    public function getRules(): array
    {
        return [];
    }

}
