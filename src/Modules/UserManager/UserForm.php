<?php

namespace Balemy\LdapCommander\Modules\UserManager;

use Balemy\LdapCommander\LDAP\LdapFormModel;


class UserForm extends LdapFormModel
{
    protected array $requiredObjectClasses = ['inetorgperson'];
    protected string $headAttribute = 'uid';

    public function load(array $data): bool
    {
        // Auto set CN based on givenName and SN
        $givenName = $data[$this->getFormName()]['givenName'] ?? '';
        $sn = $data[$this->getFormName()]['sn'] ?? '';
        $data[$this->getFormName()]['cn'] = $givenName . ' ' . $sn;

        return parent::load($data);
    }


    public function save(): bool
    {
        // Auto Hash Password
        $password = $this->loadedProperties['userPassword'];
        if (!empty($password) && !str_starts_with($password, '{SSHA}')) {
            $salt = mt_rand(0, mt_getrandmax());
            $this->loadedProperties['userPassword'] =
                '{SSHA}' . base64_encode(sha1($password . $salt, TRUE) . $salt);
        }

        return parent::save();
    }

    public function getFormName(): string
    {
        return 'UserForm';
    }
}
