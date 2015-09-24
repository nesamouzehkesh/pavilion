<?php

namespace Library\Doctrine;

use Doctrine\ORM\QueryBuilder;
use AppBundle\Service\AppService;

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
    public function search($itemParam, $param, $searchType = 'text')
    {
        if (is_array($param) and array_key_exists(AppService::PARAM_SEARCH_TEXT, $param)) {
            $searchText = $param[AppService::PARAM_SEARCH_TEXT];
            if (null !== $searchText and '' !== $searchText) {
                if (array_key_exists(AppService::PARAM_SEARCH_TARGET, $param)) {
                    $searchTarget = $param[AppService::PARAM_SEARCH_TARGET];
                    if (array_key_exists(AppService::PARAM_SEARCH_TYPE, $param)) {
                        $searchType = $param[AppService::PARAM_SEARCH_TYPE];
                    }
                    
                    $this->searchParam($searchTarget, $searchText, $searchType);
                } else {
                    $this->searchParam($itemParam, $searchText, $searchType);
                }
            }
        }
        
        return $this;
    }
    
    /**
     * 
     * @param type $searchTarget
     * @param type $searchText
     * @param type $searchType
     */
    private function searchParam($searchTarget, $searchText, $searchType)
    {
        switch ($searchType) {
            case 'text':
                $this->andWhere($searchTarget . ' LIKE :searchText')
                    ->setParameter('searchText', '%'.$searchText.'%');
                break;
            case 'int':
                $this->andWhere($searchTarget . ' = :searchText')
                    ->setParameter('searchText', intval($searchText));
                break;
        }
        
        return $this;
    }
}