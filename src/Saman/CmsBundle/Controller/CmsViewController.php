<?php

namespace Saman\CmsBundle\Controller;

use Saman\Library\Base\BaseController;

class CmsViewController extends BaseController
{
    /**
     * 
     * @param type $url
     * @return type
     */
    public function indexAction($url)
    {
        // Get the page if the page is exist
        $page = $this->getCmsViewService()->getPage($url);
        
        // Get all page links
        $pages = $this->getCmsViewService()->getPages();
        
        return $this->render(
            'SamanCmsBundle:CmsView:index.html.twig',
            array(
                'page' => $page,
                'pages' => $pages,
                'url' => $url
                )
            );
    }
    
    /**
     * 
     * @return \Saman\CmsBundle\Service\CmsViewService
     */
    private function getCmsViewService()
    {
        return $this->getService('saman_cms.cms.view');
    }    
}