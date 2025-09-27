<?php

declare(strict_types=1);

namespace Balemy\LdapCommander\Modules\Session;

use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\StringValue;
use Yiisoft\Validator\RulesProviderInterface;

final class LoginForm extends FormModel implements RulesProviderInterface
{
    private string $sessionId = '';
    private string $username = '';
    private string $password = '';


    public function __construct(public ConfiguredSessionList $sessionList)
    {

    }

    public function getPropertyLabels(): array
    {
        return [
            'sessionId' => 'Session',
            'username' => 'Username',
            'password' => 'Password',
        ];
    }

    public function getPropertyHints(): array
    {
        return [
            'username' => 'Leave empty for configured Admin DN',
        ];
    }

    /**
     * @return array<string, list<\Yiisoft\Validator\RuleInterface|callable>>
     */
    public function getRules(): array
    {
        return [
            'sessionId' => [new Required()],
            'username' => [new StringValue()],
            'password' => $this->passwordRules(),
        ];
    }

    /**
     * @return list<\Yiisoft\Validator\RuleInterface|callable>
     */
    private function passwordRules(): array
    {
        return [
            new Required(),
            new Callback(
                callback: function (): Result {
                    $result = new Result();

                    $configuredSession = $this->sessionList->getSessionById($this->sessionId);
                    if ($configuredSession) {
                        try {
                            if (!$configuredSession->login($this->username, $this->password)) {
                                $result->addError('Login failed!');
                            }
                        } catch (\Exception $ex) {
                            $result->addError("Login Error! " . $ex->getMessage());
                        }
                    } else {
                        $result->addError("Could not load selected session!");
                    }

                    return $result;
                },
                skipOnEmpty: true,
            ),
        ];
    }

    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    public function getFormName(): string
    {
        return 'LoginForm';
    }

    public function getSessionTitles(): array
    {
        $sessions = [];
        foreach ($this->sessionList->getAll() as $session) {
            $sessions[$session->getId()] = $session->getTitle();
        }
        return $sessions;
    }

}
