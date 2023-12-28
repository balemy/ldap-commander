<?php

namespace Balemy\LdapCommander\Modules\Session;

use Balemy\LdapCommander\LDAP\ConnectionDetails;
use Balemy\LdapCommander\Modules\UserManager\UserManagerConfig;

class SessionList
{
    /**
     * @var Session[]
     */
    private $sessions = [];


    public function __construct(array $params)
    {
        foreach ($params as $sessionConfig) {

            $connectionDetails = ConnectionDetails::fromArray($sessionConfig['LDAP']);
            $userManagerConfig = UserManagerConfig::fromArray($sessionConfig['UserManager']);

            $this->sessions[] = new Session($connectionDetails, $userManagerConfig);
        }
    }

    public function getSessionById(string $id): ?Session
    {
        foreach ($this->sessions as $session) {
            if ($session->getId() === $id) {
                return $session;
            }
        }
        return null;
    }

    public function getSessionByHttpSession(\Yiisoft\Session\SessionInterface $session): ?Session
    {

        $sessionId = (string)$session->get('SessionId');
        if (!empty($sessionId)) {
            foreach ($this->sessions as $session) {
                if ($session->getId() === $sessionId) {
                    return $session;
                }
            }
        }

        return null;
    }

    public function getAll()
    {
        return $this->sessions;
    }

}
