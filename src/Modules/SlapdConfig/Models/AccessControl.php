<?php

namespace Balemy\LdapCommander\Modules\SlapdConfig\Models;

use Balemy\LdapCommander\LDAP\LdapFormModel;
use Balemy\LdapCommander\Modules\GroupManager\Group;

final class AccessControl extends LdapFormModel
{
    public static array $requiredObjectClasses = ['olcDatabaseConfig'];

    protected array $arrayProperties = ['olcAccess'];

    public function getFormName(): string
    {
        return 'AccessControlForm';
    }


}
