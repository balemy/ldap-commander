<?php

namespace Balemy\LdapCommander\Modules\UserManager;

final class UserManagerConfig
{

    final public function __construct(
        public readonly bool $enabled = false,
        public array         $listColumns = ['uid' => 'UsernameL', 'givenName' => 'First name', 'sn' => 'Last name', 'mail' => 'E-Mail'],

        public array         $editFields = [
            ['uid' => 'Username', 'userPassword' => 'Password'],
            ['givenName' => 'First name', 'sn' => 'Last name'],
            ['title' => 'Title'],
            ['mail' => 'E-Mail', 'telephoneNumber' => 'Telephone Number', 'mobile' => 'Mobile Number'],
            ['street' => 'Street Address'],
            ['postalCode' => 'Post code', 'l' => 'City', 'st' => 'State'],
        ],
    )
    {
        ;
    }

    public static function fromArray(array $config): self
    {
        return new static(
            enabled: is_bool($config['enabled']) ? $config['enabled'] : false,
        );
    }
}
