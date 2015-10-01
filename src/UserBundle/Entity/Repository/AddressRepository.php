<?php

namespace UserBundle\Entity\Repository;

use Doctrine\ORM\Query;
use Library\Doctrine\BaseEntityRepository;
use UserBundle\Entity\User;

class AddressRepository extends BaseEntityRepository 
{
    /**
     * Get user address
     * 
     * @param User $user
     * @param type $id
     * @return type
     */
    public function findUserAddress(User $user, $id)
    {
        $qb = $this->getQueryBuilder();
        
        $qb->select('a')
            ->from('UserBundle:Address', 'a')
            ->leftJoin('a.user', 'u', 'WITH', 'u.deleted = 0')
            ->where('a.deleted = 0 AND a.id = :id AND a.user = :user')
            ->setParameter('user', $user)
            ->setParameter('id', $id);
        
        return $qb->getQuery()->getOneOrNullResult();
    }
    
    
}