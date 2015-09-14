<?php

namespace CmsBundle\Controller;

use Library\Base\BaseController;
use CmsBundle\Entity\Page;

class CmsViewController extends BaseController
{
    /**
     * 
     * @param type $url
     * @return type
     */
    public function indexAction($url)
    {
        // Get ObjectManager
        $em = $this->getDoctrine()->getManager();
        
        $page = Page::getRepository($em)->getPageForView($url);
        // Check if this $item exist
        if (null === $page) {
            throw new \Exception('Page does not exist');
        }
        
        $pages = Page::getRepository($em)->getPagesListForView();
        $theme = sprintf(
            'CmsBundle:CmsView:%s.html.twig', 
            ($url === '')? 'index' : 'second'
            );
            
        return $this->render(
            $theme,
            array(
                'page' => $page,
                'pages' => $pages,
                'url' => $url
                )
            );
    }
}