<?php

namespace Library\Components;

class Media
{
    /**
     * @var string
     */
    private $name;
    
    /**
     * @var string
     */
    private $path;
    
    /**
     *
     * @var type 
     */
    private $thumbnails;
    
    /**
     * @var int
     */
    private $size;
    
    public function __construct()
    {
        $this->thumbnails = new ParameterBag();
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Media
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Get path
     *
     * @return string 
     */
    public function addThumbnail($thumbnailFilter, $thumbnailPath)
    {
        $this->thumbnails->set($thumbnailFilter, $thumbnailPath);
        
        return $this;
    }
    
    /**
     * Set path
     *
     * @param string $thumbnailFilter
     * @return Media
     */
    public function getThumbnail($thumbnailFilter)
    {
        return $this->thumbnails->get($thumbnailFilter);
    }
    
    /**
     * Set path
     *
     * @return string 
     */
    public function setPath($path)
    {
        $this->path = $path;
        
        return $this;
    }
    
    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }    
    
    /**
     * Set size
     *
     * @param int $size
     * @return Media
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return string 
     */
    public function getSize()
    {
        return $this->size;
    }    
    
    /**
     * 
     * @param type $rootDirectory
     * @return type
     */
    public function getFullPath($rootDirectory)
    {
        return $rootDirectory . '/' . $this->getPath();
    }
}