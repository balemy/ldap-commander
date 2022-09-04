<?php

namespace App\Ldap;

use App\Helper\DSN;
use App\Ldap\Schema\Schema;
use App\Timer;
use LdapRecord\Connection;
use LdapRecord\Container;

class LdapService
{
    /**
     * @var Connection
     */
    public $connection;

    /**
     * @var Schema
     */
    private $schema;

    /**
     * @var string
     */
    public $baseDn = '';

    public function __construct(public Timer $timer)
    {
        $this->connection = new Connection();
        $this->schema = new Schema($this, $timer);
    }

    /**
     * @param LoginForm $login
     * @return void
     * @throws \LdapRecord\Auth\BindException
     * @throws \LdapRecord\LdapRecordException
     */
    public function connect(LoginForm $login)
    {
        $dsn = new DSN((string)$login->getAttributeValue('dsn'));

        $config = [
            'hosts' => [$dsn->getHost()],
            'port' => $dsn->getPort(),
            'use_ssl' => $dsn->getIsSSL(),
            'username' => (string)$login->getAttributeValue('adminDn'),
            'password' => (string)$login->getAttributeValue('adminPassword'),
            'base_dn' => (string)$login->getAttributeValue('baseDn')
        ];

        $this->baseDn = $config['base_dn'];

        $this->connection = new Connection($config);
        $this->connection->connect();

        Container::addConnection($this->connection, 'default');
        Container::setDefaultConnection('default');

        $this->schema->populate($this->connection);
    }


    public function getSchema(): Schema
    {
        return $this->schema;

    }

    public function getChildrenCount(string $dn): int
    {
        $query = $this->connection->query();
        $query->select(['cn', 'dn'])->setDn($dn)->listing();

        $results = $query->paginate();
        return count($results);

        #return Yii::$app->cache->getOrSet('oc_' . $dn, function () use ($dn) {
        #});
    }
}
