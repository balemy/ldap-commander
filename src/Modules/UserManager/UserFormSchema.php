<?php

namespace Balemy\LdapCommander\Modules\UserManager;

use Balemy\LdapCommander\ApplicationParameters;
use Balemy\LdapCommander\Modules\Session\Session;


final class UserFormSchema
{
    private array $_fields = [];

    private array $_rows = [];

    public function __construct()
    {
        $session = Session::getCurrentSession();
        $this->_rows = $session->userManager->editFields;

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
