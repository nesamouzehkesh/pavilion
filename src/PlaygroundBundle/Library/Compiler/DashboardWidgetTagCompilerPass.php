<?php

namespace PlaygroundBundle\Library\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class DashboardWidgetTagCompilerPass implements CompilerPassInterface
{
    /** @const Dashboard Widget tag */
    const DASHBOARD_WIDGET_TAG = 'saman_dashboard.widget';
    const DASHBOARD_WIDGET_SERVICE = 'saman_dashboard.widget_service';

    /**
     * Add tagged dashboard widget services to the primary dashboard widget service
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        // Find dashboard widget primary service
        if (!$container->has(self::DASHBOARD_WIDGET_SERVICE)) {
            return;
        }
        $definition = $container->findDefinition(
            self::DASHBOARD_WIDGET_SERVICE
        );

        // Find tagged services
        $taggedServices = $container->findTaggedServiceIds(
            self::DASHBOARD_WIDGET_TAG
        );

        // Add tagged services to primary service
        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                $definition->addMethodCall(
                    'addWidgetService',
                    array(new Reference($id), $attributes["alias"])
                );
            }
        }
    }
}