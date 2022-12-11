<?php

declare(strict_types=1);

namespace Balemy\LdapCommander\Ldap;

use Balemy\LdapCommander\Timer;
use LdapRecord\Auth\BindException;
use phpDocumentor\Reflection\Types\Boolean;
use Yiisoft\Form\FormModel;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Required;

final class LoginForm extends FormModel
{
    private string $dsn = '';
    private string $baseDn = '';
    private string $adminDn = '';
    private string $adminPassword = '';
    private string $configUser = '';
    private string $configPassword = '';
    private bool $rememberMe = false;

    /**
     * @var string[]
     */
    private $fixedAttributes = [];

    public function getAttributeLabels(): array
    {
        return [
            'dsn' => 'LDAP Server DSN',
            'baseDn' => 'Base DN',
            'adminDn' => 'Admin DN',
            'adminPassword' => 'Password',
            'configUser' => 'Config User',
            'configPassword' => 'Config Password',
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
            'baseDn' => [new Required()],
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

                $ldapService = new LdapService(new Timer());
                try {
                    $ldapService->connect($this);
                } catch (BindException $exception) {
                    $result->addError($exception->getMessage());
                }

                return $result;
            },
        ];
    }

    public function loadConnectionDetails(ConnectionDetails $connectionDetails): void
    {
        if (!empty($connectionDetails->dsn)) {
            $this->fixedAttributes[] = 'dsn';
        }
        if (!empty($connectionDetails->baseDn)) {
            $this->fixedAttributes[] = 'baseDn';
        }
        if (!empty($connectionDetails->adminDn)) {
            $this->fixedAttributes[] = 'adminDn';
        }
        if (!empty($connectionDetails->adminPassword)) {
            $this->fixedAttributes[] = 'adminPassword';
        }
        if (!empty($connectionDetails->configUser)) {
            $this->fixedAttributes[] = 'configUser';
        }
        if (!empty($connectionDetails->configPassword)) {
            $this->fixedAttributes[] = 'configPassword';
        }

        $this->setAttribute('dsn', $connectionDetails->dsn);
        $this->setAttribute('baseDn', $connectionDetails->baseDn);
        $this->setAttribute('adminDn', $connectionDetails->adminDn);
        $this->setAttribute('adminPassword', $connectionDetails->adminPassword);
        $this->setAttribute('configUser', $connectionDetails->configUser);
        $this->setAttribute('configPassword', $connectionDetails->configPassword);
    }

    public function isAttributeFixed(string $attribute): bool
    {
        return in_array($attribute, $this->fixedAttributes);
    }


    public static function createFromSession(SessionInterface $session): ?LoginForm
    {
        /** @var LoginForm|null $loginForm */
        $loginForm = $session->get('Login');
        if ($loginForm instanceof LoginForm) {
            return $loginForm;
        }

        return null;
    }


    public function storeInSession(SessionInterface $session): void
    {
        $session->set('Login', $this);
    }


    public static function removeFromSession(SessionInterface $session): void
    {
        $session->remove('Login');
    }

    public function loadSafeAttributes(array $attributes) : bool
    {
        $scope = $this->getFormName();
        if (!isset($attributes[$scope]) || !is_array($attributes[$scope])) {
            return false;
        }

        /** @var array<string, string> $data */
        $data = $attributes[$scope];

        foreach ($data as $name => $value) {
            if (!$this->isAttributeFixed($name)) {
                $this->setAttribute($name, $value);
            }
        }

        return true;
    }

}
