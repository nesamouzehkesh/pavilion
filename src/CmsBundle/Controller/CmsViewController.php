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
        
        return $this->render(
            sprintf('::web/%s.html.twig', $url === ''? 'index' : 'content'),
            array(
                'title' => $page['title'],
                'content' => $page['content']
                )
            );
    }
}