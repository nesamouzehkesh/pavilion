<?php

namespace Saman\CmsBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Saman\AppBundle\Service\AppService;
use Saman\CmsBundle\Entity\Page;
use Saman\CmsBundle\Form\PageFormType;

class CmsAdminService
{
    /**
     *
     * @var AppService $appService
     */
    protected $appService;
    
    /**
     * 
     * @param AppService $appService
     * @param type $parameters
     */
    public function __construct(
        AppService $appService, 
        $parameters
        ) 
    {
        $this->appService = $appService;
        $this->appService->setParametrs($parameters);
    }
    
    /**
     * Get Item based on its ID. If ID is null then create a new Item
     * 
     * @param int $pageId
     * @return type
     */
    public function getPage($pageId = null)
    {
        if (null === $pageId) {
            return new Page();
        }
        
        // Get Page form repository
        $page = Page::getRepository($this->appService->getEntityManager())
            ->getPage($pageId);

        // Check if page is found
        if (!$page instanceof Page) {
            throw $this->appService
                ->createVisibleHttpException('alert.error.noItemFound');
        }

        return $page;
    }
    
    /**
     * 
     * @param type $param
     * @param type $loadJustQuery
     * @return type
     */
    public function getPages($param = null, $loadJustQuery = true)
    {
        return Page::getRepository($this->appService->getEntityManager())
            ->getPages($param, $loadJustQuery);
    }
    
    /**
     * Add or Edit Item
     * NOTE: it is not a good practice to inject $request to this service function
     * 
     * @param Request $request
     * @param Page $page
     * @return type
     */
    public function addEditItem(Request $request, Page $page)
    {
        /** @var PageForm */
        $pageFormType = new PageFormType(
            $page,
            $this->appService->getParameter('page')
            );
        
        $pageForm = $this->appService->createForm($request, $pageFormType, $page);
        
        // Handling Form Submissions and validation
        $pageForm->handleRequest($request);
        if ($pageForm->isValid()) {
            $page->setModifiedTime();
            $this->appService->saveEntity($page);

            return $this->appService->getJsonResponse(true);
        }
        
        $itemFormView = $this->appService->renderView(
            'SamanCmsBundle:CmsAdmin:form/addEditPage.html.twig',
            array(
                'form' => $pageForm->createView(),
                'page' => $page
                )
            );
        
        return $this->appService->getJsonResponse(true, null, $itemFormView);        
    }
}