<?php

namespace Balemy\LdapCommander\Modules\Session;

use Balemy\LdapCommander\LDAP\ConnectionDetails;
use Balemy\LdapCommander\LDAP\Helper\DSN;
use Balemy\LdapCommander\LDAP\Schema\Schema;
use Balemy\LdapCommander\LDAP\Services\SchemaService;
use Balemy\LdapCommander\Modules\UserManager\UserManagerConfig;
use LdapRecord\Connection;
use LdapRecord\Container;

class Session
{
    public Connection $lrConnection;
    public Schema $schema;
    public string $baseDn;
    public ConnectionDetails $connectionDetails;
    public UserManagerConfig $userManager;

    public function __construct(ConfiguredSession $configuredSession)
    {
        $this->connectionDetails = $configuredSession->connectionDetails;
        $this->userManager = $configuredSession->userManager;

        $this->initConnection();
    }

    private function initConnection(): void
    {
        $dsn = new DSN($this->connectionDetails->dsn);

        $config = [
            'hosts' => [$dsn->getHost()],
            'port' => $dsn->getPort(),
            'use_ssl' => $dsn->getIsSSL(),
            'username' => $this->connectionDetails->adminDn,
            'password' => $this->connectionDetails->adminPassword,
            'base_dn' => $this->connectionDetails->baseDn
        ];

        $this->baseDn = $this->connectionDetails->baseDn;

        $this->lrConnection = new Connection($config);
        $this->lrConnection->connect();

        Container::addConnection($this->lrConnection, 'default');
        Container::setDefaultConnection('default');

        $this->schema = new Schema($this);
        $this->schema->populate($this->lrConnection);
    }



    public function getSchemaService(): SchemaService
    {
        return new SchemaService($this->schema);
    }

    /**
     * @return Session
     * @psalm-suppress MixedInferredReturnType, MixedInferredReturnType
     * @todo Find a better approach
     */
    public static function getCurrentSession(): Session
    {
        if (SessionLoaderMiddleware::$currentSession === null) {
            throw new \Exception("No active session!");
        }

        /** @var Session $session */
        $session = SessionLoaderMiddleware::$currentSession;
        return $session;
    }


}
