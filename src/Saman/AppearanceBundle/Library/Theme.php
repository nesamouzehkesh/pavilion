<?php

namespace Saman\AppearanceBundle\Library;

use Saman\AppearanceBundle\Entity\Theme as ThemeEntity;

class Theme
{
    /**
     *
     * @var type 
     */
    private $theme;
    
    /**
     *
     * @var type 
     */
    private $themeDefultSettings;
    
    /**
     * 
     */
    public function __construct(ThemeEntity $theme = null, $themeDefultSettings = array())
    {
        if (null === $theme) {
            $theme = new ThemeEntity();
        }
        
        $this->theme = $theme;
        $this->themeDefultSettings = $themeDefultSettings;
    }
    
    /**
     * 
     * @return Saman\AppearanceBundle\Entity\Theme
     */
    public function getTheme()
    {
        return $this->theme;
    }
    
    /**
     * 
     * @return type
     */
    public function getThemeDefultSettings()
    {
        return $this->themeDefultSettings;
    }
}