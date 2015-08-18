<?php

namespace Saman\AppearanceBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Saman\Library\Service\Helper;
use Saman\Library\Map\EntityMap;
use Saman\Library\Map\ViewMap;
use Saman\AppearanceBundle\Form\LinkForm;
use Saman\AppearanceBundle\Entity\Link;
use Saman\AppearanceBundle\Entity\Navigation;
use Saman\AppearanceBundle\Service\NavigationService;
use Saman\AppearanceBundle\Service\Interfaces\LinkServiceInterface;
use Saman\AppearanceBundle\Service\Interfaces\NavigationServiceInterface;

class LinkService implements LinkServiceInterface
{
    /**
     *
     * @var Helper $helper
     */
    protected $helper;
    
    /**
     *
     * @var NavigationService $navigationService
     */
    protected $navigationService = null;

    /**
     * 
     * @param \Saman\Library\Service\Helper $helper
     * @param type $parameters
     */
    public function __construct(
        Helper $helper,
        $parameters = array()
        ) 
    {
        $this->helper = $helper;
        $this->helper->setParametrs($parameters);
    }
    
    /**
     * 
     * @param NavigationServiceInterface $navigationService
     */
    public function setNavigationService(NavigationServiceInterface $navigationService)
    {
        $this->navigationService = $navigationService;
        
        return $this;
    }
    
    /**
     * 
     * @return type
     * @throws \Exception
     */
    public function getNavigationService()
    {
        if (null === $this->navigationService) {
            throw new \Exception('No Navigation Service is defined for this service');
        }
        
        return $this->navigationService;
    }    
    
    /**
     * Create a new Link
     */
    public function createNewLink()
    {
        $navigation = new Link();
        
        return $navigation; 
    }
    
    /**
     * Get Link based on its ID. If ID is null then create a new Link
     * 
     * @param type $navigationId
     * @return type
     */
    public function getLink($linkId = null)
    {
        $link = null;
        if (null === $linkId) {
            $link = $this->createNewLink();
        } else {
            $link = $this->getLinkRepository()->getLink(intval($linkId));
        }        
        
        return $link;
    }
    
    /**
     * Update Link
     * 
     * @param Link $link
     */
    public function updateLink(Link $link)
    {
        $link->setModifiedTime();
        $this->helper->saveEntity($link);
        
        return true;
    }
    
    /**
     * Delete an Link
     * 
     * @param int $linkId
     * @return type
     */
    public function deleteLink(Request $request, $linkId)
    {
        if ($request->isXmlHttpRequest() && $request->isMethod('POST')) {
            /** @var Link $link */
            $link = $this->getLink($linkId);
            
            // Check if this $link exist
            if (null === $link) {
                return $this->helper->getExceptionResponseNotFound($linkId);
            }

            if ($this->helper->deleteEntity($link)) {
                return $this->helper->getJsonResponse(
                    true,
                    array(
                        'alert.success.itemHasBeenRemoved', 
                        array('%id%' => $linkId))
                    );
            } else {
                return $this->helper->getExceptionResponse(
                    'alert.success.itemHasNotBeenRemoved', 
                    array('%id%' => $linkId)
                    );
            }
        }
    }
    
    /**
     * Add Edit link
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $navigation
     * @param type $linkId
     * @return type
     */
    public function addEditLink(Request $request, Navigation $navigation, $linkId = null)
    {
        /** @var Page */
        $link = $this->getLink($linkId);
        // Check if this $item exist
        if (null === $link) {
            return $this->helper->getExceptionResponseNotFound($linkId);
        }
        
        if ($link->isNew()) {
            $link->setNavigation($navigation);
            $link->setSort($navigation->getLastLinkSort() + 1);
        }
        
        $linlFormType = new LinkForm();
        /** @var PageForm */
        $linkForm = $this->helper->createForm($request, $linlFormType, $link);
        // Handling Form Submissions and validation
        $linkForm->handleRequest($request);
        if ($linkForm->isSubmitted()) {
            if ($linkForm->isValid()) {
                //true !== $errorResponce = $this->helper->validate($link) and
                
                $navigation->addLink($link);
                $this->updateLink($link);
                
                return $this->helper->getJsonResponse(true);
            }
        }
        
        $linkFormView = $this->helper->renderView(
            ViewMap::APPEARANCE_ADMIN_LINK_ADD_EDIT,
            array(
                'form' => $linkForm->createView(),
                'isLinkPage' => (null === $link->getUrl())
                )
            );
        
        return $this->helper->getJsonResponse(true, null, $linkFormView);        
    }    
        
    /**
     * Get Links
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $loadJustQuery
     * @return type
     */
    public function getLinksView(Navigation $navigation)
    {
        $links = $navigation->getLinks();
        
        $linksView = $this->helper->renderView(
            ViewMap::APPEARANCE_ADMIN_LINKS_VIEW,
            array(
                'links' => $links,
                'navigation' => $navigation
                )
            );
                
        return $linksView;
    }
    
    /**
     * 
     * @param type $request
     * @param type $navigation
     */
    public function sortLinks(Request $request, Navigation $navigation)
    {
        $sortIds = explode(",", $request->get('sortIds', ''));
        if (is_array($sortIds)) {
            foreach ($navigation->getLinks() as $link) {
                if (false !== $sortId = array_search($link->getId(), $sortIds)) {
                    $link->setSort($sortId);
                }
            }
            $this->helper->getEntityManager()->flush();
        }
        
        return $this->helper->getJsonResponse(true);
    }
    
    /**
     * Get Link Repository
     * 
     * @return type
     */
    private function getLinkRepository()
    {
        return $this->helper->getRepository(EntityMap::APPEARANCE_LINK);
    }    
}