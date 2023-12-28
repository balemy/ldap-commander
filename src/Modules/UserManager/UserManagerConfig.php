<?php

namespace Balemy\LdapCommander\Modules\UserManager;

class UserManagerConfig
{

    public function __construct(
        public readonly bool $enabled = false,
    )
    {
        ;
    }

    public static function fromArray(array $config): static
    {
        return new static(
            enabled: $config['enabled'] ?? false,
        );
    }
}
