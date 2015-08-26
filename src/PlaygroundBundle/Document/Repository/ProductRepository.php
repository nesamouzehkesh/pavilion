<?php

namespace PlaygroundBundle\Document\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;

class ProductRepository extends DocumentRepository
{
    public function findAllOrderedByName()
    {
        return $this->createQueryBuilder()
            ->sort('name', 'ASC')
            ->getQuery()
            ->execute();
    }
}