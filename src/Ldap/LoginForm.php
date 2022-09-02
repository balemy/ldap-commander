<?php

declare(strict_types=1);

namespace App\Ldap;

use LdapRecord\Auth\BindException;
use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Required;

final class LoginForm extends FormModel
{
    private string $dsn = '';
    private string $baseDn = '';
    private string $adminDn = '';
    private string $adminPassword = '';
    private bool $rememberMe = false;

    private LdapService $ldapService;

    public function __construct(LdapService $ldapService)
    {
        $this->ldapService = $ldapService;

        parent::__construct();
    }

    public function getAttributeLabels(): array
    {
        return [
            'dsn' => 'LDAP Server DSN',
            'baseDn' => 'Base DN',
            'adminDn' => 'Admin DN',
            'adminPassword' => 'Password',
            'rememberMe' => 'Remember me',
        ];
    }

    public function getFormName(): string
    {
        return 'Login';
    }

    public function getRules(): array
    {
        return [
            'dsn' => [new Required()],
            'adminDn' => [new Required()],
            'adminPassword' => $this->passwordRules(),
        ];
    }

    private function passwordRules(): array
    {
        return [
            new Required(),
            function (): Result {
                $result = new Result();

                try {
                    $this->ldapService->connect($this->getConnectionDetails());
                } catch (BindException $exception) {
                    $result->addError($exception->getMessage());
                }

                return $result;
            },
        ];
    }

    public function loadConnectionDetails(ConnectionDetails $connectionDetails): void
    {
        $this->setAttribute('dsn', $connectionDetails->dsn);
        $this->setAttribute('baseDn', $connectionDetails->baseDn);
        $this->setAttribute('adminDn', $connectionDetails->adminDn);
        $this->setAttribute('adminPassword', $connectionDetails->adminPassword);
    }

    public function isAttributeFixed(string $attribute): bool
    {
        /*
        if (isset(ConnectionDetails::ENV_CONFIG_MAP[$attribute]) &&
            !empty($_ENV[ConnectionDetails::ENV_CONFIG_MAP[$attribute]])
        ) {
            return true;
        }
        */

        return false;
    }

    public function getConnectionDetails(): ConnectionDetails
    {
        return new ConnectionDetails(
            (string)$this->getAttributeValue('dsn'),
            (string)$this->getAttributeValue('baseDn'),
            (string)$this->getAttributeValue('adminDn'),
            (string)$this->getAttributeValue('adminPassword'),
        );
    }
}
