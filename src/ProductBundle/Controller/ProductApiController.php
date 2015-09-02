<?php

namespace ProductBundle\Controller;

use Library\Base\BaseController;
use Library\Components\MediaHandler;
use ProductBundle\Entity\Product;

class ProductApiController extends BaseController
{
    /**
     * @\Nelmio\ApiDocBundle\Annotation\ApiDoc(
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
     * @\Nelmio\ApiDocBundle\Annotation\ApiDoc(
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
}