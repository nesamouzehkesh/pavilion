<?php

namespace ProductBundle\Controller;

use Library\Base\BaseController;
use Symfony\Component\HttpFoundation\Request;
use ProductBundle\Entity\Product;
use ProductBundle\Entity\Category;
use ProductBundle\Form\CategoryType;

class ProductAdminConfigController extends BaseController
{
    public function configProductsAction()
    {
        // Get ObjectManager
        $em = $this->getDoctrine()->getManager();
        
        // Get all Products
        $categories = Category::getRepository($em)->getCategories();
        
        // Render and return the view
        return $this->render(
            'ProductBundle:Product:configs.html.twig',
            array(
                'categories' => $categories
                )
            );
    }
    
    /**
     * Add or edit a Category
     * 
     * @param Request $request
     * @param type $categoryId
     * @return type
     */
    public function addEditCategory(Request $request, $categoryId = null)
    {
        try {
            // Get category object
            $category = $this->getProductCategoryEntity($categoryId);

            // Generate Product Form
            $categoryForm = $this->createForm(
                new CategoryType(), 
                $category,
                array(
                    'action' => $request->getUri(),
                    'method' => 'post'
                    )
                );

            $categoryForm->handleRequest($request);
            // If form is submited and it is valid then add or update this $product
            if ($categoryForm->isValid()) {
                $this->getAppService()->persistEntity($category);
                
                return $this->getJsonResponse(true);
            }

            $view = $this->renderView(
                'ProductBundle:Product:/form/category.html.twig', 
                array('form' => $categoryForm->createView())
                );
            
            return $this->getJsonResponse(true, null, $view);
        } catch (\Exception $ex) {
            return $this->getExceptionResponse('Can not add or edit category', $ex);
        }         
    }
    
    /**
     * Delete a product
     * 
     * @param type $categoryId
     * @return type
     */
    public function deleteCategoryAction($categoryId)
    {
        try {
            // Get Category
            $category = $this->getProductCategoryEntity($categoryId);
            
            // Validate if this category can be deleted
            $validationResponce = $this->getAppService()->validate($category, 'delete');
            if (true !== $validationResponce) {
                return $this->getJsonResponse(false, $validationResponce);
            }
            
            // Soft-deleting an entity
            $this->getAppService()->deleteEntity($category);

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
     * @param type $categoryId
     * @return Product
     * @throws NotFoundHttpException
     */
    private function getProductCategoryEntity($categoryId = null)
    {
        // If $productId is null it means that we want to create a new product object.
        // otherwise we find a product in DB based on this $productId
        if (null === $categoryId) {
            $category = new Category();
        } else {
            // Get ObjectManager
            $em = $this->getDoctrine()->getManager();
            
            // Get Product repository
            $category = Category::getRepository($em)->getCategory($categoryId);
            
            // Check if $product is found
            if (!$category instanceof Category) {
                throw $this->createNotFoundException('No product found for id ' . $categoryId);
            }
        }
        
        // Return product object
        return $category;
    }
}