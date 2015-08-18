<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function __construct($environment, $debug)
    {
        date_default_timezone_set('Australia/Sydney');
        parent::__construct($environment, $debug);
    }
    
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            // Saman instaled bundles
            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
            new Knp\Bundle\GaufretteBundle\KnpGaufretteBundle(),
            new Ivory\CKEditorBundle\IvoryCKEditorBundle(),
            new Liip\ImagineBundle\LiipImagineBundle(),
            // Saman Bubdles
            new Saman\UserBundle\SamanUserBundle(),
            new Saman\SecurityBundle\SamanSecurityBundle(),
            new Saman\CmsBundle\SamanCmsBundle(),
            new Saman\LabelBundle\SamanLabelBundle(),
            new Saman\DashboardBundle\SamanDashboardBundle(),
            new Saman\MediaBundle\SamanMediaBundle(),
            new Saman\ShoppingBundle\SamanShoppingBundle(),
            new Saman\ConfigBundle\SamanConfigBundle(),
            new Saman\AppearanceBundle\SamanAppearanceBundle(),
            new Saman\CronBundle\SamanCronBundle(),
            new Saman\PlayGroundBundle\SamanPlayGroundBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
