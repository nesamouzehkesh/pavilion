<?php

namespace ProductBundle\Controller;

use Library\Base\BaseController;
use Symfony\Component\HttpFoundation\Request;
use ProductBundle\Entity\Product;
use ProductBundle\Entity\Category;

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
        try {
            // Get search parameters from HTTP request
            $searchParam = $this->getAppService()
                ->getSearchParam($request);
            $searchParam['categoryId'] = $request->get('categoryId', null);
            
            // Get ObjectManager
            $em = $this->getDoctrine()->getManager();
            // Get all Products
            $products = Product::getRepository($em)->getProducts($searchParam);
            // Get pagination
            $productsPagination = $this->getAppService()
                ->paginate($request, $products);
            // Get all Categories
            $categories = Category::getRepository($em)->getCategories();
            
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
        } catch (Exception $ex) {
            return $this->getExceptionResponse('There is a problem in displaying your orders', $ex);
        }            
    }
    
    /**
     * 
     * @param int $productId
     * @return type
     * @throws type
     */
    public function displayProductAction($productId)
    {
        try {
            // Get ObjectManager
            $em = $this->getDoctrine()->getManager();
            // Get all Products
            $product = Product::getRepository($em)->getProduct($productId);
            
            return $this->render(
                '::web/product/product.html.twig',
                array(
                    'shoppingCartProductIds' => $this->getShoppingService()->getShoppingCartListIds(),
                    'pageParams' => $this->getSession()->get('pageParams'),
                    'product' => $product
                ));
        } catch (\Exception $ex) {
            return $this->getExceptionResponse('alert.error.canNotDisplayItem', $ex);
        }
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
     * @throws NotFoundHttpException
     */
    private function getProductEntity($productId = null)
    {
        // If $productId is null it means that we want to create a new product object.
        // otherwise we find a product in DB based on this $productId
        if (null === $productId) {
            $product = new Product();
        } else {
            // Get ObjectManager
            $em = $this->getDoctrine()->getManager();
            
            // Get Product repository
            $product = Product::getRepository($em)->getProduct($productId);
            
            // Check if $product is found
            if (!$product) {
                throw $this->createNotFoundException('No product found for id ' . $productId);
            }
        }
        
        // Return product object
        return $product;
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