<?php

declare(strict_types=1);

namespace Balemy\LdapCommander;

final class ApplicationParameters
{
    private string $charset = 'UTF-8';
    private string $name = 'LDAP Commander';
    private string $version = '0.0.0';

    public function getCharset(): string
    {
        return $this->charset;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function charset(string $value): self
    {
        $new = clone $this;
        $new->charset = $value;
        return $new;
    }

    public function name(string $value): self
    {
        $new = clone $this;
        $new->name = $value;
        return $new;
    }

    public function version(string $value): self
    {
        $new = clone $this;
        $new->version = $value;
        return $new;
    }
}
