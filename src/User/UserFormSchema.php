<?php

namespace Balemy\LdapCommander\User;

use Balemy\LdapCommander\ApplicationParameters;


final class UserFormSchema
{
    private array $_fields = [];

    private array $_rows;

    public function __construct(private readonly ApplicationParameters $applicationParameters)
    {
        $this->_rows = $this->applicationParameters->getUserEditFields();

        /** @var string[] $row */
        foreach ($this->_rows as $row) {
            foreach ($row as $fieldKey => $fieldLabel) {
                $this->_fields[$fieldKey] = $fieldLabel;
            }
        }
    }

    public function getRows(): array
    {
        return $this->_rows;
    }

    /**
     * @psalm-return array<string,string>
     */
    public function getFields(): array
    {
        return $this->_fields;
    }
}
