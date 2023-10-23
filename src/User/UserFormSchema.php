<?php

namespace Balemy\LdapCommander\User;

use Balemy\LdapCommander\ApplicationParameters;


final class UserFormSchema
{
    private array $_fields = [];

    /**
     * @var array|array[]
     */
    private array $_rows;

    public function __construct(private readonly ApplicationParameters $applicationParameters)
    {
        $this->_rows = $this->applicationParameters->getUserEditFields();

        /** @var array $row */
        foreach ($this->_rows as $row) {
            /** @var string $fieldLabel */
            foreach ($row as $fieldKey => $fieldLabel) {
                $this->_fields[$fieldKey] = $fieldLabel;
            }
        }
    }

    public function getRows(): array
    {
        return $this->_rows;
    }

    public function getFields(): array
    {
        return $this->_fields;
    }
}
