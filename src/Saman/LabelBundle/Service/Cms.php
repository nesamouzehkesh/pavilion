<?php

namespace Saman\CmsBundle\Service;

use Saman\Library\Service\Helper;

class Cms 
{
    /**
     *
     * @var Helper $helper
     */
    private $helper;
    
    /**
     * 
     * @param \Saman\Library\Service\Helper $helper
     * @param type $parameters
     */
    public function __construct(
        Helper $helper, 
        $parameters
        ) 
    {
        $this->helper = $helper;
        $this->helper->setParametrs($parameters);
    }
    
    /**
     * 
     * @return type
     */
    public function displayPage()
    {
        return true;
    }
    
    /**
     * 
     * @return type
     */
    public function listPages()
    {
        return true;
    }
}