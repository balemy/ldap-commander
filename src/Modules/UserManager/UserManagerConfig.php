<?php

namespace Balemy\LdapCommander\Modules\UserManager;

final class UserManagerConfig
{

    final public function __construct(
        public readonly bool $enabled = false,
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
