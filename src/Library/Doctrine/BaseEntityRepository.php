<?php

namespace Library\Doctrine;

use Doctrine\ORM\EntityRepository;

class BaseEntityRepository extends EntityRepository
{
    /**
     * Get BaseQueryBuilder, an extention of QueryBuilder
     * 
     * @return \Library\Doctrine\BaseQueryBuilder
     */
    public function getQueryBuilder()
    {
        return new BaseQueryBuilder($this->getEntityManager());
    }
}