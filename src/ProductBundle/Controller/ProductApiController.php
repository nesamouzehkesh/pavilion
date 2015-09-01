<?php

namespace ProductBundle\Controller;

use Library\Base\BaseController;
use Library\Components\MediaHandler;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use ProductBundle\Entity\Product;
use ProductBundle\Form\ProductApiType;
use FOS\RestBundle\View\View;

class ProductApiController extends BaseController
{
    /**
     * @ApiDoc(
     *   resource = true,
     *   description = "Get all products",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     403 = "Returned when the product is not authorized to say hello",
     *     404 = {
     *       "Returned when the product is not found",
     *       "Returned when something else is not found"
     *     }
     *   }
     * )
     * 
     * @return array
     */
    public function getProductsAction()
    {
        try {
            $cacheManager = $this->get('liip_imagine.cache.manager');
            $mediaHandler = new MediaHandler($cacheManager);
            
            $products = Product::getRepository($this->getDoctrine()->getEntityManager())
                ->getProductsListForView($mediaHandler);
            
            return $this->getApiResponse(true, array(
                'products' => $products,
                'numberOfProducts' => 12,
                ));
        } catch (\Exception $ex) {
            return $this->getApiExceptionResponse('alert.error.canNotDisplayItems', $ex);
        }            
    }
    
    /**
     * @ApiDoc(
     *   resource = true,
     *   description = "Get one product by its ID",
     *   requirements={
     *     {
     *       "name"="limit",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="how many objects to return"
     *     }
     *   },
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     403 = "Returned when the product is not authorized to say hello",
     *     404 = {
     *       "Returned when the product is not found",
     *       "Returned when something else is not found"
     *     }
     *   }
     * )
     * 
     * @param int $productId
     * @return type
     * @Rest\View()
     */
    public function getProductAction($productId)
    {
        try {
            $cacheManager = $this->get('liip_imagine.cache.manager');
            $mediaHandler = new MediaHandler($cacheManager);
            
            $product = Product::getRepository($this->getDoctrine()->getEntityManager())
                ->getProductForView($productId, $mediaHandler);
            
            return $this->getApiResponse(true, array('product' => $product));
        } catch (\Exception $ex) {
            return $this->getApiExceptionResponse('alert.error.canNotDisplayItem', $ex);
        }            
    } 
    
    /**
     * @ApiDoc(
     *   resource = true,
     *   description = "Get one product by its ID",
     *   requirements={
     *     {
     *       "name"="limit",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="how many objects to return"
     *     }
     *   },
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     403 = "Returned when the product is not authorized to say hello",
     *     404 = {
     *       "Returned when the product is not found",
     *       "Returned when something else is not found"
     *     }
     *   }
     * )
     * 
     * @param int $productId
     * @return type
     * @Rest\View()
     */    
    public function addProductAction()
    {
        $product = Product::getRepository($this->getDoctrine()->getEntityManager())
            ->getProduct(2);
        
        return $this->processForm($product);
    }
    
    private function processForm(Product $product)
    {
        $statusCode = $product->isNew() ? 201 : 204;

        $form = $this->createForm(new ProductApiType(), $product);
        $form->handleRequest($this->getRequest());

        if ($form->isValid()) {
            
            $response = new Response();
            $response->setStatusCode($statusCode);

            // set the `Location` header only when creating new resources
            if (201 === $statusCode) {
                $response->headers->set('Location',
                    $this->generateUrl(
                        'acme_demo_user_get', array('id' => 111111),
                        true // absolute
                    )
                );
            }

            return $response;
        }

        return View::create($form, 400);
    }    
}