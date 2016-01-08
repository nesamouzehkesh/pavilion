<?php

namespace ProductBundle\Controller;

use Library\Base\BaseController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\SystemConfig;
use ProductBundle\Form\ProductType;

class ProductAdminController extends BaseController
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
                ->getSearchParam($request, array('categoryId'));
            
            // Get all Products
            $products = $this->getProductService()->getProducts($searchParam);
            
            // Get pagination
            $productsPagination = $this->getAppService()->paginate($request, $products);

            // Render and return the view
            $productsView = $this->renderView(
                'ProductBundle:Product:element/products.html.twig',
                array(
                    'productsPagination' => $productsPagination
                    )
                );
            
            // If user use the pagination to view other pages then we just return the 
            // $pagesView as a jason response array
            if ($request->get('headless')) {
                return $this->getAppService()->getJsonResponse(true, null, $productsView);
            } 
            
            // Get all Categories
            $categories = $this->getProductService()->getProductCategories();
            
            return $this->render(
                'ProductBundle:Product:index.html.twig',
                array(
                    'productsView' => $productsView,
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
            // Get Product
            $product = $this->getProductService()->getProduct($productId);
            
            // Generate the view for this page
            $pageView = $this->renderView(
                'ProductBundle:Product:product.html.twig',
                array('product' => $product)
                );
            
            // Generate final jason responce
            return $this->getJsonResponse(true, null, $pageView);
        } catch (\Exception $ex) {
            return $this->getExceptionResponse(
                'alert.error.canNotDisplayItem', 
                $ex
                );
        }
    }       
    
    /**
     * Display and handel add edit product action
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function addEditProductAction(Request $request, $productId = null)
    {
        try {
            // Get product object
            $product = $this->getProductService()->getProduct($productId);
            $specificationFields = $this->getAppService()
                ->getSystemConfigOptions(SystemConfig::PRODUCT_SPECIFICATION_FIELD);
            
            // Generate Product Form
            $productForm = $this->createForm(
                new ProductType($specificationFields), 
                $product,
                array(
                    'action' => $request->getUri(),
                    'method' => 'post'
                    )
                );

            $productForm->handleRequest($request);
            // If form is submited and it is valid then add or update this $product
            if ($productForm->isValid()) {
                
                $mediaService = $this->getService('saman_media.media');
                $this->getAppService()
                    ->setMediaService($mediaService)
                    ->saveMedia($product);
                
                return $this->getJsonResponse(true);
            }

            $view = $this->renderView(
                'ProductBundle:Product:/form/product.html.twig', 
                array('form' => $productForm->createView())
                );
            
            return $this->getJsonResponse(true, null, $view);
        } catch (\Exception $ex) {
            return $this->getExceptionResponse('Can not add or edit product', $ex);
        }         
    }
    
    /**
     * Delete a product
     * 
     * @param type $productId
     * @return type
     */
    public function deleteProductAction($productId)
    {
        try {
            // Get Product
            $product = $this->getProductService()->getProduct($productId);
            
            // Soft-deleting an entity
            $this->getAppService()->deleteEntity($product);

            return $this->getJsonResponse(true);
        } catch (\Exception $ex) {
            return $this->getExceptionResponse(
                'alert.error.canNotDeleteItem', 
                $ex
                );
        }        
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
}