<?php

namespace ProductBundle\Service;

use AppBundle\Service\AppService;
use ProductBundle\Entity\Product;
use ProductBundle\Entity\Category;

class ProductService
{
    /**
     *
     * @var type
     */
    protected $em;
    
    /**
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
        $this->em = $appService->getEntityManager();
    }
    
    /**
     * 
     * @param type $searchParam
     * @return Product[]
     */
    public function getProducts($searchParam = array())
    {
        return Product::getRepository($this->em)->getProducts($searchParam);
    }
    
    /**
     * 
     * @param type $searchParam
     * @return Product[]
     */
    public function getProductCategories()
    {
        return Category::getRepository($this->em)->getCategories();
    }
    
    /**
     * Get a product based on $productId or create a new one if $productId is null
     * 
     * In a good practice development we should not put these kind of functions 
     * in controller, we can create a product service to performance these kind of 
     * actions
     * 
     * @param type $productId
     * @return Product
     * @throws VisibleHttpException
     */
    public function getProduct($productId = null)
    {
        // If $productId is null it means that we want to create a new product object.
        // otherwise we find a product in DB based on this $productId
        if (null === $productId) {
            return new Product;
        }

        // Get Product repository
        $product = Product::getRepository($this->em)->getProduct($productId);

        // Check if $product is found
        if (!$product) {
            throw $this->appService->createVisibleHttpException('No product found for id ' . $productId);
        }
        
        // Return product object
        return $product;
    }    
}