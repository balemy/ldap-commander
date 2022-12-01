<?php

declare(strict_types=1);

namespace Balemy\LdapCommander;

final class ApplicationParameters
{
    private string $charset = 'UTF-8';
    private string $name = 'LDAP Commander';
    private string $version = '0.0.0';
    private array $userListColumns = ['cn' => 'Common Name', 'givenName' => 'First name', 'sn' => 'Last name', 'mail' => 'E-Mail'];
    private array $userEditFields = [
        ['uid' => 'Username', 'cn' => 'Common Name'],
        ['title' => 'Title', 'givenName' => 'First name', 'sn' => 'Last name'],
        ['mail' => 'E-Mail', 'telephoneNumber' => 'Telephone Number', 'mobile' => 'Mobile Number'],
        ['street' => 'Street Address'],
        ['postalCode' => 'Post code', 'l' => 'City', 'st' => 'State'],
        ['userPassword' => 'New Password']
    ];

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

    public function getUserListColumns(): array
    {
        return $this->userListColumns;
    }

    public function getUserEditFields(): array
    {
        return $this->userEditFields;
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

    /**
     * @param string[] $value
     */
    public function userListColumns(array $value): self
    {
        $new = clone $this;
        $new->userListColumns = $value;
        return $new;
    }

    /**
     * @param string[] $value
     */
    public function userEditFields(array $value): self
    {
        $new = clone $this;
        $new->userEditFields = $value;
        return $new;
    }
}
