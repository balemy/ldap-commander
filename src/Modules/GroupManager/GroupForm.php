<?php

namespace Balemy\LdapCommander\Modules\GroupManager;

use Balemy\LdapCommander\LDAP\LdapFormModel;

class GroupForm extends LdapFormModel
{
    public static array $requiredObjectClasses = ['groupOfUniqueNames'];

    public function load(array $data): bool
    {
        /** @var array<array-key, string> $formData */
        $formData = $data[$this->getFormName()];

        if (empty($formData['uniqueMember'])) {
            $formData['uniqueMember'] = [];
        }

        return parent::load($data); // TODO: Change the autogenerated stub
    }

    public function getFormName(): string
    {
        return 'GroupForm';
    }

}
