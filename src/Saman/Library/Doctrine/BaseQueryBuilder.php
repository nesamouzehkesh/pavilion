<?php

namespace Saman\Library\Doctrine;

use Doctrine\ORM\QueryBuilder;
use Saman\Library\Service\BaseService;

class BaseQueryBuilder extends QueryBuilder
{
    const SEARCH_MAX_CHAR_TO_TRIGGER_SEARCH = 2;
    
    public function __construct(\Doctrine\ORM\EntityManager $em) {
        parent::__construct($em);
    }
    
    /**
     * Add filter on query
     * 
     * @param type $qb
     * @param type $item item to be filtered
     * @param type $filteredItemsId 
     * @return type
     */
    public function filter($item, $itemsId)
    {
        if (isset($itemsId)) {
            if (count($itemsId) == 0) {
                $this->andWhere('1 = 0');
            } else {
                $this->andWhere($item . ' IN (:itemsId)');
                $this->setParameter('itemsId', $itemsId);
            }
        }
        
        return $this;
    }
    
    /**
     * 
     * @param type $itemParam
     * @param type $param
     */
    public function search($itemParam, $param)
    {
        if (is_array($param) and array_key_exists(BaseService::PARAM_SEARCH_TEXT, $param)) {
            $searchText = $param[BaseService::PARAM_SEARCH_TEXT];
            if (null !== $searchText and '' !== $searchText) {
                $this->andWhere($itemParam . ' LIKE :searchText')
                    ->setParameter('searchText', '%'.$searchText.'%');
            }
        }
        
        return $this;
    }
}