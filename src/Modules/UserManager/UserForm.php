<?php

namespace Balemy\LdapCommander\Modules\UserManager;

use Balemy\LdapCommander\LDAP\LdapFormModel;


class UserForm extends LdapFormModel
{
    protected array $requiredObjectClasses = ['inetorgperson'];


    public function getFormName(): string
    {
        return 'UserForm';
    }
}
