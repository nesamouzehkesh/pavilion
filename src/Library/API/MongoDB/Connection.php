<?php

namespace Library\API\MongoDB;

use MongoClient;

/**
 * Wrapper for the MongoClient class.
 *
 * @since  1.0
 * @author Jonathan H. Wage <jonwage@gmail.com>
 */
class Connection
{
    /**
     * The PHP MongoClient instance being wrapped.
     *
     * @var \MongoClient
     */
    protected $mongoClient;

    /**
     * Server string used to construct the MongoClient instance (optional).
     *
     * @var string
     */
    protected $server;

    /**
     * Constructor.
     *
     * If $server is an existing MongoClient instance, the $options parameter
     * will not be used.
     *
     * @param string|\MongoClient $server  Server string or MongoClient instance
     * @param array               $options MongoClient constructor options
     * @param Configuration       $config  Configuration instance
     * @param EventManager        $evm     EventManager instance
     */
    public function __construct($server = null)
    {
        if ($server instanceof \MongoClient || $server instanceof \Mongo) {
            $this->mongoClient = $server;
        } else {
            $this->server = $server;
        }
    }

    /**
     * Wrapper method for MongoClient::close().
     *
     * @see http://php.net/manual/en/mongoclient.close.php
     * @return boolean
     */
    public function close()
    {
        $this->initialize();
        return $this->mongoClient->close();
    }

    /**
     * Wrapper method for MongoClient::connect().
     *
     * @see http://php.net/manual/en/mongoclient.connect.php
     * @return boolean
     */
    public function connect()
    {
        $this->initialize();

        $mongoClient = $this->mongoClient;
        return $mongoClient->connect();
    }

    /**
     * Wrapper method for MongoClient::dropDB().
     *
     * This method will dispatch preDropDatabase and postDropDatabase events.
     *
     * @see http://php.net/manual/en/mongoclient.dropdb.php
     * @param string $database
     * @return array
     */
    public function dropDatabase($database)
    {
        $this->initialize();
        $result = $this->mongoClient->dropDB($database);

        return $result;
    }

    /**
     * Get the MongoClient instance being wrapped.
     *
     * @deprecated 1.1 Replaced by getMongoClient(); will be removed for 2.0
     * @return \MongoClient
     */
    public function getMongo()
    {
        return $this->getMongoClient();
    }

    /**
     * Set the MongoClient instance to wrap.
     *
     * @deprecated 1.1 Will be removed for 2.0
     * @param \MongoClient $mongoClient
     */
    public function setMongo($mongoClient)
    {
        if (!($mongoClient instanceof \MongoClient || $mongoClient instanceof \Mongo)) {
            throw new \InvalidArgumentException('MongoClient or Mongo instance required');
        }

        $this->mongoClient = $mongoClient;
    }

    /**
     * Get the MongoClient instance being wrapped.
     *
     * @return \MongoClient
     */
    public function getMongoClient()
    {
        $this->initialize();
        return $this->mongoClient;
    }

    /**
     * Wrapper method for MongoClient::getReadPreference().
     *
     * For driver versions between 1.3.0 and 1.3.3, the return value will be
     * converted for consistency with {@link Connection::setReadPreference()}.
     *
     * @see http://php.net/manual/en/mongoclient.getreadpreference.php
     * @return array
     */
    public function getReadPreference()
    {
        $this->initialize();
        return $this->mongoClient->getReadPreference();
    }

    /**
     * Wrapper method for MongoClient::setReadPreference().
     *
     * @see http://php.net/manual/en/mongoclient.setreadpreference.php
     * @param string $readPreference
     * @param array  $tags
     * @return boolean
     */
    public function setReadPreference($readPreference, array $tags = null)
    {
        $this->initialize();
        if (isset($tags)) {
            return $this->mongoClient->setReadPreference($readPreference, $tags);
        }

        return $this->mongoClient->setReadPreference($readPreference);
    }

    /**
     * Get the server string.
     *
     * @return string|null
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * Gets the $status property of the wrapped MongoClient instance.
     *
     * @deprecated 1.1 No longer used in driver; Will be removed for 1.2
     * @return string
     */
    public function getStatus()
    {
        $this->initialize();
        if ( ! ($this->mongoClient instanceof \MongoClient || $this->mongoClient instanceof \Mongo)) {
            return null;
        }

        return $this->mongoClient->status;
    }

    /**
     * Construct the wrapped MongoClient instance if necessary.
     *
     * This method will dispatch preConnect and postConnect events.
     */
    public function initialize()
    {
        if ($this->mongoClient !== null) {
            return;
        }

        $this->mongoClient =  new MongoClient();
            
    }

    /**
     * Checks whether the connection is initialized and connected.
     *
     * @return boolean
     */
    public function isConnected()
    {
        if (!($this->mongoClient instanceof \MongoClient || $this->mongoClient instanceof \Mongo)) {
            return false;
        }

        /* MongoClient::$connected is deprecated in 1.5.0+, so count the list of
         * connected hosts instead.
         */
        return version_compare(phpversion('mongo'), '1.5.0', '<')
            ? $this->mongoClient->connected
            : count($this->mongoClient->getHosts()) > 0;
    }

    /**
     * Wrapper method for MongoClient::listDBs().
     *
     * @see http://php.net/manual/en/mongoclient.listdbs.php
     * @return array
     */
    public function listDatabases()
    {
        $this->initialize();
        return $this->mongoClient->listDBs();
    }

    /**
     * Log something using the configured logger callable (if available).
     *
     * @param array $log
     */
    public function log(array $log)
    {
        if (null !== $this->config->getLoggerCallable()) {
            call_user_func_array($this->config->getLoggerCallable(), array($log));
        }
    }

    /**
     * Wrapper method for MongoClient::selectCollection().
     *
     * @see http://php.net/manual/en/mongoclient.selectcollection.php
     * @param string $db
     * @param string $collection
     * @return Collection
     */
    public function selectCollection($db, $collection)
    {
        $this->initialize();
        return $this->selectDatabase($db)->selectCollection($collection);
    }

    /**
     * Wrapper method for MongoClient::selectDatabase().
     *
     * This method will dispatch preSelectDatabase and postSelectDatabase
     * events.
     *
     * @see http://php.net/manual/en/mongoclient.selectdatabase.php
     * @param string $name
     * @return Database
     */
    public function selectDatabase($name)
    {
        $this->initialize();
        $database = $this->doSelectDatabase($name);

        return $database;
    }

    /**
     * Wrapper method for MongoClient::__get().
     *
     * @see http://php.net/manual/en/mongoclient.get.php
     * @param string $database
     * @return \MongoDB
     */
    public function __get($database)
    {
        $this->initialize();
        return $this->mongoClient->__get($database);
    }

    /**
     * Wrapper method for MongoClient::__toString().
     *
     * @see http://php.net/manual/en/mongoclient.tostring.php
     * @return string
     */
    public function __toString()
    {
        $this->initialize();
        return $this->mongoClient->__toString();
    }

    /**
     * Return a new Database instance.
     *
     * If a logger callable was defined, a LoggableDatabase will be returned.
     *
     * @see Connection::selectDatabase()
     * @param string $name
     * @return Database
     */
    protected function doSelectDatabase($name)
    {
        $mongoDB = $this->mongoClient->selectDB($name);

        return new Database($this, $mongoDB);
    }
}
