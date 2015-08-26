<?php

namespace PlaygroundBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use PlaygroundBundle\Document\Product;
use PlaygroundBundle\Document\Feature;
use Library\Helpers\LoremIpsumGenerator;

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
        $loremIpsum = new LoremIpsumGenerator();
        $dm = $this->getMongoDbManager();
        
        $product = new Product();
        $product->setName($loremIpsum->getTitle());
        $product->setPrice(rand(100 , 1000));
        $dm->persist($product); 
        
        for ($j = 0; $j < rand(3 , 6); $j++) {
            $feature = new Feature();
            $feature->setName($loremIpsum->getTitle());
            $product->addFeature($feature);
            $dm->persist($feature);
        }        

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