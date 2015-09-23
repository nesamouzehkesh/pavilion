<?php

namespace ShoppingBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use UserBundle\Entity\User;

/**
 * PaymentRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PaymentRepository extends EntityRepository
{
    /**
     * 
     * @param type $paymentId
     * @param User $user
     * @return type
     */
    public function getPayment($paymentId, User $user = null)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        
        $qb->select('op')
            ->from('ShoppingBundle:OrderPayment', 'op')
            ->where('op.id = :id AND op.deleted = 0')
            ->setParameter('id', $paymentId);
        
        if ($user instanceof User) {
            $qb->andWhere('op.user = :user');
            $qb->setParameter('user', $user);
        }
        
        $query = $qb->getQuery();
        
        return $query->getOneOrNullResult();
    } 
}