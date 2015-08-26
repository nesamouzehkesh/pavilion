<?php

namespace PlaygroundBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use PlaygroundBundle\Library\Compiler\DashboardWidgetTagCompilerPass;

class PlaygroundBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new DashboardWidgetTagCompilerPass());
    }    
}
