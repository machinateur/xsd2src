<?php


namespace App\DependencyInjection\Compiler;

use App\Service\DataService;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class NodeHandlePass
 * @package App\DependencyInjection\Compiler
 */
final class NodeHandlePass implements CompilerPassInterface
{
    /**
     * @var string
     */
    public const TAG_NAME = 'app.node_handle';

    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->has(DataService::class)) {
            $definition = $container->findDefinition(DataService::class);

            $taggedServices = $container->findTaggedServiceIds(self::TAG_NAME);
            foreach ($taggedServices as $serviceId => $tags) {
                $definition->addMethodCall('addNodeHandle', [
                    new Reference($serviceId),
                ]);
            }
        }
    }
}
