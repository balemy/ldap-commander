<?php

declare(strict_types=1);

namespace App\Ldap;

use App\Timer;
use LdapRecord\Auth\BindException;
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

        $this->setAttribute('dsn', $connectionDetails->dsn);
        $this->setAttribute('baseDn', $connectionDetails->baseDn);
        $this->setAttribute('adminDn', $connectionDetails->adminDn);
        $this->setAttribute('adminPassword', $connectionDetails->adminPassword);
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

}
