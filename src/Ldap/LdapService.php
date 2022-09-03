<?php

namespace App\Ldap;

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
     * @param ConnectionDetails $connectionDetails
     * @return void
     * @throws \LdapRecord\Auth\BindException
     * @throws \LdapRecord\LdapRecordException
     */
    public function connect(ConnectionDetails $connectionDetails)
    {

        $config = [
            'hosts' => [$connectionDetails->getHost()],
            'port' => $connectionDetails->getPort(),
            'username' => $connectionDetails->adminDn,
            'password' => $connectionDetails->adminPassword,
            'base_dn' => $connectionDetails->baseDn
        ];

        $this->baseDn = $connectionDetails->baseDn;

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
