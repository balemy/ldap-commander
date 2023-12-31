<?php

namespace Balemy\LdapCommander\Modules\Session;

use Balemy\LdapCommander\LDAP\ConnectionDetails;
use Balemy\LdapCommander\Modules\UserManager\UserManagerConfig;

final class ConfiguredSessionList
{
    /**
     * @var ConfiguredSession[]
     */
    private $configuredSessions = [];


    public function __construct(array $params)
    {
        /** @var array<array> $sessionConfig */
        foreach ($params as $sessionConfig) {
            $connectionDetails = ConnectionDetails::fromArray($sessionConfig['LDAP']);
            $userManagerConfig = UserManagerConfig::fromArray($sessionConfig['UserManager']);
            $this->configuredSessions[] = new ConfiguredSession($connectionDetails, $userManagerConfig);
        }
    }

    public function getSessionById(string $id): ?ConfiguredSession
    {
        foreach ($this->configuredSessions as $session) {
            if ($session->getId() === $id) {
                return $session;
            }
        }
        return null;
    }

    public function getSessionByHttpSession(\Yiisoft\Session\SessionInterface $session): ?ConfiguredSession
    {

        $sessionId = (string)$session->get('SessionId');
        if (!empty($sessionId)) {
            foreach ($this->configuredSessions as $session) {
                if ($session->getId() === $sessionId) {
                    return $session;
                }
            }
        }

        return null;
    }

    /**
     * @return ConfiguredSession[]
     */
    public function getAll(): array
    {
        return $this->configuredSessions;
    }

}
