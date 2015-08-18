<?php

namespace Saman\AppearanceBundle\Repository;

use Doctrine\ORM\Query;
use Saman\Library\Doctrine\BaseEntityRepository;
use Saman\Library\Map\EntityMap;
use Saman\AppearanceBundle\Entity\Theme;

/**
 * ThemeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ThemeRepository extends BaseEntityRepository
{
    /**
     * General get item function, by default it just gets one or null object.
     * 
     * @param type $url
     * @param type $readOnly
     * @return type
     */
    public function getTheme($value, $loadTheme = false, $key = 'id')
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        
        if ($loadTheme) {
            $qb->select('theme, widget')
                ->from(EntityMap::APPEARANCE_THEME, 'theme')
                ->leftJoin('theme.widgets', 'widget', 'WITH', 'widget.deleted = 0')    
                ->where(sprintf('theme.%s = :%s AND theme.deleted = 0', $key, $key))
                ->setParameter($key, $value);
        } else {
            $qb->select('theme')
                ->from(EntityMap::APPEARANCE_THEME, 'theme')
                ->where(sprintf('theme.%s = :%s AND theme.deleted = 0', $key, $key))
                ->setParameter($key, $value);
        }

        $query = $qb->getQuery();
        $result = $query->getOneOrNullResult();
        
        return $result;
    }    
    
    /**
     * Get all items
     * 
     * @param type $order
     * @param type $readOnly
     * @return type
     */
    public function getThemes($param = array(), $justQuery = true, $order = 'theme.id', $readOnly = true)
    {
        $hydrationMode = $readOnly? Query::HYDRATE_ARRAY : null;
        $qb = $this->getQueryBuilder();
        
        $qb->select('theme')
            ->from(EntityMap::APPEARANCE_THEME, 'theme')
            ->where('theme.deleted = 0')
            ->search('theme.title', $param)
            ->orderBy($order);
        
        $query = $qb->getQuery();
        if ($justQuery) {
            return $query;
        } else {
            $result = $query->getResult($hydrationMode);
            return $result;
        }
    }
    
    /**
     * Get all items
     * 
     * @param type $order
     * @param type $readOnly
     * @return type
     */
    public function loadAllThemes($readOnly = false)
    {
        $hydrationMode = $readOnly? Query::HYDRATE_ARRAY : null;
        $qb = $this->getQueryBuilder();
        
        $qb->select('theme, widget')
            ->from(EntityMap::APPEARANCE_THEME, 'theme')
            ->leftJoin('theme.widgets', 'widget', 'WITH', 'widget.deleted = 0')    
            ->where('theme.deleted = 0');
        
        return $qb->getQuery()->getResult($hydrationMode);
    }
    
    /**
     * 
     * @param type $user
     * @param type $configDefaultParameters
     */
    public function updateThemeSettings($item, $themeSettingOptions, $templateParameters)
    {
        $em = $this->getEntityManager();
        
        $setSettings = Theme::generateThemeSettings($themeSettingOptions, $templateParameters);
        $item->setSettings($setSettings);
        $em->persist($item);
        
        $em->flush();
    }
}