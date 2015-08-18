<?php

namespace Saman\ConfigBundle\Service;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Saman\Library\Map\EntityMap;
use Saman\Library\Service\Helper;
use Saman\Library\Session\BaseSession;
use Saman\UserBundle\Entity\User;
use Saman\Library\Map\ViewMap;
use Saman\ConfigBundle\Form\ConfigForm;
use Saman\ConfigBundle\Entity\Config;

class ConfigService
{
    /**
     *  
     * @var Helper $helper
     */
    protected $helper;
    
    /**
     *
     * @var User $user
     */
    protected $user;
    
    /**
     *
     * @var type 
     */
    protected $defaultConfig;
    
    /**
     *
     * @var type 
     */
    protected $userConfigs = null;

    /**
     * 
     * @param \Saman\Library\Service\Helper $helper
     * @param type $defaultConfig
     */
    public function __construct(
        Helper $helper,
        $defaultConfig
        ) 
    {
        $this->helper = $helper;
        $this->defaultConfig = $defaultConfig;
    }
    
    /**
     * 
     * @param type $user
     * @return \Saman\MediaBundle\Service\MediaService
     */
    public function setUser(User $user)
    {
        $this->user = $user;
        
        return $this;
    }
    
    /**
     * Main getter function in this service. It accept a config key and returns
     * its value. 
     * 
     * @param type $configKey
     */
    public function getConfig($configKey, User $user = null)
    {
        // Get use config options from session, memory or DB
        $userConfigs = $this->getUserConfigs($user);
        if (array_key_exists($configKey, $userConfigs)) {
            return $userConfigs[$configKey];
        }
        
        // If no config is defined for this key for this user then try to
        // get it from yml default config options values
        $defaultConfigOptions = $this->getDefaultConfigOptions();
        if (!array_key_exists($configKey, $defaultConfigOptions)) {
            // If this key is not exist even in the default config data then
            // throw exception
            throw new \Exception(sprintf(
                'This config option (%s) is not defined', 
                $configKey
                ));
        }
            
        return $defaultConfigOptions[$configKey];
    }
    
    /**
     * Get all public and private default config options from yml file
     * 
     * @return type
     */
    public function getDefaultConfigOptions()
    {
        return Config::generateConfigOptions(
            $this->defaultConfig, 
            array('public', 'private')
            );
    }
    
    /**
     * Get Config from config repository
     * 
     * @param type $id
     * @return type
     */
    public function getUserConfigs(User $user = null)
    {
        $user = (null === $user)? $this->user : $user;
        if (null === $user) {
            throw new \Exception('No user is set for this service');
        }
        
        // Attempt to load $userConfigs in this object
        if (null !== $this->userConfigs) {
            return $this->userConfigs;
        }
        
        // Attempt to load $userConfigs from session
        $userConfigs = $this->getUserConfigsFromSession();
        
        if (null === $userConfigs) {
            // Finally load $userConfigs from session. If no $userConfigs
            // is found then it will create new one based on the default values. 
            $userConfigs = $this->getConfigRepository()->getUserConfigs(
                $user,
                $this->defaultConfig
                );
            
            // Put this $userConfigs in to session
            $this->setUserConfigsToSession($userConfigs);
        }
        
        return ($this->userConfigs = $userConfigs);
    }
    
    /**
     * Update config
     * 
     * @return type
     */
    public function updateUserConfigs($userConfigs)
    {
        if (null === $this->user) {
            throw new \Exception('No user is set for this service');
        }

        // Put this $userConfigs in to session
        $this->setUserConfigsToSession($userConfigs);
        
        return $this->getConfigRepository()->updateUserConfigs(
            $this->user,
            $userConfigs,
            $this->defaultConfig    
            );
    }
    
    /**
     * 
     * @param Request $request
     */
    public function addEditConfigs(Request $request)
    {
        $userConfigs = $this->getUserConfigs();
        
        /** @var ConfigForm $configFormType */
        $configFormType = new ConfigForm(
            $this->defaultConfig, 
            $this->helper->getEntityManager(), 
            $userConfigs
            );
        
        /** @var Symfony\Component\Form\Form $configForm */
        $configForm = $this->helper->createForm($request, $configFormType);
        
        // Handling Form Submissions and validation
        $configForm->handleRequest($request);
        if ($configForm->isSubmitted()) {
            if ($configForm->isValid()) {
                // Get user config form values from POST request
                $userConfigs = $request->request->get($configFormType->getName());
                
                // Update user config
                $this->updateUserConfigs($userConfigs);
                
                // Generate reload URL based on the current route
                $reloadUrl = $this->helper->generateUrl($request->get('_route'));
                
                return $this->helper->getJsonResponse(
                    true, 
                    null, 
                    null, 
                    array('url' => $reloadUrl)
                    );
            }
        }    
        
        return $this->helper->render(
            ViewMap::CONFIG_ADMIN_CONFIG_ITEM_ADD_EDIT,
            array(
                'form' => $configForm->createView(),
                'defaultConfig' => $this->defaultConfig
                )
            );
    }
    
    /**
     * Get Config Repository
     * 
     * @return type
     */
    private function getConfigRepository()
    {
        return $this->helper->getRepository(EntityMap::CONFIG_CONFIG);
    }
    
    /**
     * Get user config options from session
     * 
     * @return type
     */
    private function getUserConfigsFromSession()
    {
        $userConfigs = null;
        $session = new BaseSession($this->user);
        $userSessionConfigOptions = $session->read('configs');
        
        if (null !== $userSessionConfigOptions) {
            $userConfigs = json_decode($userSessionConfigOptions, true);
        }
        
        return $userConfigs;
    }
    
    /**
     * Set user config options into session
     * 
     * @param type $userConfigs
     * @return \Saman\ConfigBundle\Service\ConfigService
     */
    private function setUserConfigsToSession($userConfigs)
    {
        $session = new BaseSession($this->user);
        $userConfigs = json_encode($userConfigs);
        $session->write('configs', $userConfigs);
        
        return $this;
    }
}