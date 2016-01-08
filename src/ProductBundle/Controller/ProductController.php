<?php

namespace ProductBundle\Controller;

use Library\Base\BaseController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\SystemConfig;

class ProductController extends BaseController
{
    /**
     * Display all Products in the Product main page
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function displayProductsAction(Request $request)
    {
        // Get search parameters from HTTP request
        $searchParam = $this->getAppService()
            ->getSearchParam($request, array('categoryId'));
        
        // Get all Products
        $products = $this->getProductService()->getProducts($searchParam);
        // Get pagination
        $productsPagination = $this->getAppService()->paginate($request, $products);
        // Get all Categories
        $categories = $this->getProductService()->getProductCategories();

        $this->getSession()->set(
            'pageParams', 
            array('page' => $productsPagination->getCurrentPageNumber())
            );

        return $this->render(
            '::web/product/products.html.twig',
            array(
                'shoppingCartProductIds' => $this->getShoppingService()->getShoppingCartListIds(),
                'productsPagination' => $productsPagination,
                'categories' => $categories,
                )
            );
    }
    
    /**
     * 
     * @param int $productId
     * @return type
     * @throws type
     */
    public function displayProductAction($productId)
    {
        // Get a product
        $product = $this->getProductService()->getProduct($productId);
        
        // Get product specification fields
        $specificationFields = $this
            ->getAppService()
            ->getSystemConfigOptions(SystemConfig::PRODUCT_SPECIFICATION_FIELD);
        
        return $this->render(
            '::web/product/product.html.twig',
            array(
                'shoppingCartProductIds' => $this->getShoppingService()->getShoppingCartListIds(),
                'pageParams' => $this->getSession()->get('pageParams'),
                'specificationFields' => $specificationFields,
                'product' => $product
            ));
    }
    
    /**
     * Get Order service
     * 
     * @return \ProductBundle\Service\ProductService
     */
    private function getProductService()
    {
        return $this->getService('saman_product.product');
    } 
    
    /**
     * Get Order service
     * 
     * @return \ShoppingBundle\Service\ShoppingService
     */
    private function getShoppingService()
    {
        return $this->getService('saman_shopping.shopping');
    }      
}