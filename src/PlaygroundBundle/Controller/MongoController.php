<?php

namespace PlaygroundBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use PlaygroundBundle\Document\Product;
use PlaygroundBundle\Document\Feature;

class MongoController extends Controller
{
    public function indexAction()
    {
        $dm = $this->getMongoDbManager();
        $products = Product::getRepository($dm)->findAllOrderedByName();
        
        return $this->render(
            '::Playground/Mongo/index.html.twig',
            array('products' => $products)
            );
    }
    
    /**
     * 
     * @return type
     */
    public function addAction()
    {
        $feature1 = new Feature();
        $feature1->setName('Feature 1');
        
        $feature2 = new Feature();
        $feature2->setName('Feature 2');
        
        $feature3 = new Feature();
        $feature3->setName('Feature 3');
        
        $product = new Product();
        $product->setName('A Foo Bar');
        $product->setPrice('19.99');
        $product->addFeature($feature1);
        $product->addFeature($feature2);
        $product->addFeature($feature3);

        $dm = $this->getMongoDbManager();
        $dm->persist($feature1);
        $dm->persist($feature2);
        $dm->persist($feature3);
        $dm->persist($product); 
        $dm->flush();
        
        return $this->redirect($this->generateUrl('saman_pg_mongo_index'), 301);
    }  
    
    /**
     * 
     * @param type $id
     * @return type
     * @throws type
     */
    public function deleteAction($id)
    {
        $dm = $this->getMongoDbManager();
        $product = Product::getRepository($dm)->find($id);
        if (!$product) {
            throw $this->createNotFoundException('No product found for id '.$id);
        }
        
        $dm->remove($product);
        $dm->flush();
        
        return $this->redirect($this->generateUrl('saman_pg_mongo_index'), 301);
    }  
    
    /*
    public function getProductsAction()
    {
        $moCo = new \Library\API\MongoDB\Connection();
        $moDb = $moCo->selectDatabase('symfony-exploration');
        $moProductColl = $moDb->selectCollection('Product');
        var_dump($moProductColl->count());
        $newProducts = $moProductColl->find();
        var_dump($newProducts);
    }
    */
    
    /**
     * 
     * @return \Doctrine\Bundle\MongoDBBundle\ManagerRegistry
     */
    private function getMongoDbManager()
    {
        return $this->get('doctrine_mongodb')->getManager();
    }
}