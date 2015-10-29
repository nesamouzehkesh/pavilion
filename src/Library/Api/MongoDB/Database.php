<?php

namespace Library\API\MongoDB;

/**
 * Wrapper for the MongoDB class.
 *
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 * @link        www.doctrine-project.org
 * @since       1.0
 * @author      Jonathan H. Wage <jonwage@gmail.com>
 * @author      Bulat Shakirzyanov <mallluhuct@gmail.com>
 */
class Database
{
    /**
     * The Connection instance to which this database belongs.
     *
     * @var Connection
     */
    protected $connection;

    /**
     * The MongoDB instance being wrapped.
     *
     * @var \MongoDB
     */
    protected $mongoDB;

    /**
     * Constructor.
     *
     * @param Connection      $connection Connection to which this database belongs
     * @param \MongoDB        $mongoDB    MongoDB instance being wrapped
     */
    public function __construct(Connection $connection, \MongoDB $mongoDB)
    {
        $this->connection = $connection;
        $this->mongoDB = $mongoDB;
    }

    /**
     * Return the MongoDB instance being wrapped.
     *
     * @return \MongoDB
     */
    public function getMongoDB()
    {
        return $this->mongoDB;
    }

    /**
     * Return the name of this database.
     *
     * @return string
     */
    public function getName()
    {
        return $this->mongoDB->__toString();
    }

    /**
     * Wrapper method for MongoDB::selectCollection().
     *
     * This method will dispatch preSelectCollection and postSelectCollection
     * events.
     *
     * @see http://php.net/manual/en/mongodb.selectcollection.php
     * @param string $name
     * @return Collection
     */
    public function selectCollection($name)
    {
        $mongoCollection = $this->mongoDB->selectCollection($name);

        return new Collection($this, $mongoCollection);
    }

    /**
     * Wrapper method for MongoDB::__get().
     *
     * @see http://php.net/manual/en/mongodb.get.php
     * @param string $name
     * @return \MongoCollection
     */
    public function __get($name)
    {
        return $this->mongoDB->__get($name);
    }

    /**
     * Wrapper method for MongoDB::__toString().
     *
     * @see http://www.php.net/manual/en/mongodb.--tostring.php
     * @return string
     */
    public function __toString()
    {
        return $this->mongoDB->__toString();
    }
}