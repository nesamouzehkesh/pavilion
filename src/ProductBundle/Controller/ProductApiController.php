<?php

namespace ProductBundle\Controller;

use Library\Base\BaseController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use ProductBundle\Entity\Product;

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
     * @Rest\View()
     */
    public function getProductsAction()
    {
        try {
            $products = Product::getRepository($this->getDoctrine()->getEntityManager())
                ->getProductsListForView();
            
            return $this->getApiResponse(true, array('products' => $products));
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
            $product = $this->getDoctrine()->getRepository('ProductBundle:Product')
                ->find($productId);
            
            return $this->getApiResponse(true, array('product' => $product));
        } catch (\Exception $ex) {
            return $this->getApiExceptionResponse('alert.error.canNotDisplayItem', $ex);
        }            
    }    
}