<?php

namespace ProductBundle\Controller;

use Library\Base\BaseController;
use Symfony\Component\HttpFoundation\Request;
use ProductBundle\Entity\Product;
use ProductBundle\Form\ProductType;

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
        // Get ObjectManager
        $em = $this->getDoctrine()->getManager();
        
        // Get all Products
        $products = Product::getRepository($em)->getProducts();

        // Get pagination
        $productsPagination = $this->getAppService()
            ->paginate($request, $products);
        
        // Render and return the view
        return $this->render(
            'ProductBundle:Product:products.html.twig',
            array(
                'productsPagination' => $productsPagination
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
        try {
            // Get ObjectManager
            $em = $this->getDoctrine()->getManager();
        
            // Get all Products
            $product = Product::getRepository($em)->getProduct($productId);
            
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
            $product = $this->getProductEntity($productId);

            // Generate Product Form
            $productForm = $this->createForm(
                new ProductType(), 
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
                array(
                    'form' => $productForm->createView(),
                    )
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
            $product = $this->getProductEntity($productId);
            
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
}