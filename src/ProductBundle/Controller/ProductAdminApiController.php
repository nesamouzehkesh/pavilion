<?php

namespace ProductBundle\Controller;

use Library\Base\BaseController;
use Library\Serializer\FormSerializer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ProductBundle\Entity\Product;
use ProductBundle\Form\ProductApiType;
use FOS\RestBundle\View\View;

/**
 * http://williamdurand.fr/2012/08/02/rest-apis-with-symfony2-the-right-way/
 * http://williamdurand.fr/2013/08/07/ddd-with-symfony2-folder-structure-and-code-first/
 * http://obtao.com/blog/2013/05/create-rest-api-in-a-symfony-application/
 * 
 * 
 * curl -v -H "Accept: application/json" -H "Content-type: application/json" -X POST -d '{"user":{"username":"foo", "email": "foo@example.org", "password":"hahaha"}}' http://symfony-basic.dev/api/product
 * 
 */
class ProductAdminApiController extends BaseController
{
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
     * @\FOS\RestBundle\Controller\Annotations\View()
     */    
    public function getProductAction($productId)
    {
        return array('product' => $this->getProduct($productId));
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
     * @return type
     * @\FOS\RestBundle\Controller\Annotations\View()
     */    
    public function addProductAction(Request $request)
    {
        return $this->processForm($this->getProduct());
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
     * @return type
     * @\FOS\RestBundle\Controller\Annotations\View()
     */     
    public function getProductFormAction($productId)
    {
        $product = $this->getProduct($productId);
        $productForm = $this->createForm(new ProductApiType(), $product);
        $productForm->handleRequest($this->getRequest());
        
        $formSerializer = new FormSerializer();
        $serializedForm = $formSerializer->serialize($productForm);
        
        return array('form' => $serializedForm->getContent());
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
     * @\FOS\RestBundle\Controller\Annotations\View()
     */    
    public function editProductAction($productId)
    {
        return $this->processForm($this->getProduct($productId));
    }
    
    /**
     * 
     * @param Product $product
     * @return Response
     */
    private function processForm(Product $product)
    {
        try {
            $statusCode = $product->isNew() ? 201 : 204;

            $productForm = $this->createForm(new ProductApiType(), $product);
            $productForm->handleRequest($this->getRequest());
            if ($productForm->isValid()) {
                // Get ObjectManager
                $em = $this->getDoctrine()->getManager();

                $em->persist($product);
                $em->flush();

                $response = new Response();
                $response->setStatusCode($statusCode);

                // set the `Location` header only when creating new resources
                if (201 === $statusCode) {
                    $response->headers->set('Location',
                        $this->generateUrl(
                            'api_product_show_product', array('productId' => $product->getId()),
                            true // absolute
                        )
                    );
                }

                return $response;
            }
            
            $formSerializer = new FormSerializer();
            $serializedForm = $formSerializer->serialize($productForm);

            return View::create(array('form' => $serializedForm->getContent()), 400);
            //return array('form' => $serializedForm->getContent());
        } catch (Exception $ex) {

        }
    }
    
    /**
     * 
     * @param type $productId
     * @return Product
     */
    private function getProduct($productId = null)
    {
        if (null === $productId) {
            return new Product();
        }
        
        // Get ObjectManager
        $em = $this->getDoctrine()->getManager();
        
        // Get all Products
        $product = Product::getRepository($em)->getProduct($productId);
        if (!$product instanceof Product) {
            throw new NotFoundHttpException('User not found');
        }
        
        return $product;
    }
}