<?php

namespace MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class Select2Controller extends Controller
{
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $class
     * @return type
     */
    public function select2DataAction(Request $request, $class)
    {
        return $this->getSelect2Service()->select2Data($request, $class);
    }
    
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $class
     * @return type
     */
    public function select2DataLookupAction(Request $request, $class)
    {
        return $this->getSelect2Service()->select2DataLookup($request, $class);
    }

    /**
     * 
     * @return type
     */
    protected function getSelect2Service()
    {
        return $this->get('saman_media.select2');
    }    
}