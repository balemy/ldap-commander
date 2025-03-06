<?php

namespace Balemy\LdapCommander\Modules\SlapdConfig\Models;

use Balemy\LdapCommander\LDAP\LdapFormModel;
use Balemy\LdapCommander\Modules\GroupManager\Group;

final class BindUser extends LdapFormModel
{
    public static array $requiredObjectClasses = ['simpleSecurityObject', 'organizationalRole'];

    public function getFormName(): string
    {
        return 'BindUserForm';
    }

    public function save(): bool
    {
        // Auto Hash Password
        $password = $this->loadedProperties['userPassword'];
        if (!empty($password) && is_string($password) && !preg_match("/^\{\w{1,10}\}/", $password)) {
            $salt = mt_rand(0, mt_getrandmax());
            $this->loadedProperties['userPassword'] =
                '{SSHA}' . base64_encode(sha1($password . $salt, TRUE) . $salt);
        }

        return parent::save();
    }

}
