<?php

namespace ProductBundle\Controller;

use Library\Components\FormField;
use Library\Base\BaseController;
use Library\Form\FormFieldType;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\SystemConfig;
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
        $specifications = $this->getAppService()
            ->getSystemConfig(SystemConfig::PRODUCT_SPECIFICATION_FIELD);
        
        // Render and return the view
        return $this->render(
            'ProductBundle:Product:configs.html.twig',
            array(
                'specifications' => $specifications,
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
    public function addEditCategoryAction(Request $request, $categoryId = null)
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
                $this->getAppService()->saveEntity($category);
                
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
                return $validationResponce;
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
     * Unset specification form field
     * 
     * @param int $fieldKey
     * @return type
     */
    public function deleteSpecificationFieldAction($fieldKey)
    {
        try {
            $specificationFormConfig = $this->getAppService()
                ->getSystemConfig(SystemConfig::PRODUCT_SPECIFICATION_FIELD);

            $specificationFormConfig->unSetOptions($fieldKey);
            $this->getAppService()->saveEntity($specificationFormConfig);
            
            return $this->getJsonResponse(true);
        } catch (\Exception $ex) {
            return $this->getExceptionResponse(
                'alert.error.canNotDeleteItem', 
                $ex
                );
        }        
    }

    /**
     * Add or edit a Category
     * 
     * @param Request $request
     * @param type $fieldKey
     * @return type
     */
    public function addEditSpecificationFieldAction(Request $request, $fieldKey = null)
    {
        try {
            $specificationFormConfig = $this->getAppService()
                ->getSystemConfig(SystemConfig::PRODUCT_SPECIFICATION_FIELD);
            
            // Create form field object 
            $formField = new FormField(
                $fieldKey, 
                $specificationFormConfig->getOption($fieldKey)
                );
            
            // Generate Product Form
            $formFieldForm = $this->createForm(
                new FormFieldType(), 
                $formField,
                array(
                    'action' => $request->getUri(),
                    'method' => 'post'
                    )
                );

            $formFieldForm->handleRequest($request);
            // If form is submited and it is valid then add or update this $product
            if ($formFieldForm->isValid()) {
                
                $specificationFormConfig->unSetOptions($fieldKey);
                $specificationFormConfig->setOption(
                    $formField->getKey(), 
                    $formField->getData()
                    );
                
                $this->getAppService()->saveEntity($specificationFormConfig);
                
                return $this->getJsonResponse(true);
            }

            $view = $this->renderView(
                'AppBundle:Components:/form/forField.html.twig', 
                array('form' => $formFieldForm->createView())
                );
            
            return $this->getJsonResponse(true, null, $view);
        } catch (\Exception $ex) {
            return $this->getExceptionResponse('Can not add or edit specification field', $ex);
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