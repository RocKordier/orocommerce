<?php

namespace Oro\Bundle\TaxBundle\Tests\Unit\DependencyInjection\CompilerPass;

use Oro\Bundle\TaxBundle\DependencyInjection\CompilerPass\TaxProviderPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class TaxProviderPassTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var TaxProviderPass
     */
    protected $compilerPass;

    /**
     * @var ContainerBuilder|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $containerBuilder;

    public function setUp()
    {
        $this->containerBuilder = $this
            ->getMockBuilder('Symfony\Component\DependencyInjection\ContainerBuilder')
            ->getMock();

        $this->compilerPass = new TaxProviderPass();
    }

    public function tearDown()
    {
        unset($this->compilerPass, $this->containerBuilder);
    }

    public function testProcessRegistryDoesNotExist()
    {
        $this->containerBuilder
            ->expects($this->once())
            ->method('hasDefinition')
            ->with(TaxProviderPass::REGISTRY_SERVICE)
            ->willReturn(false);

        $this->containerBuilder
            ->expects($this->never())
            ->method('getDefinition');

        $this->containerBuilder
            ->expects($this->never())
            ->method('findTaggedServiceIds');

        $this->compilerPass->process($this->containerBuilder);
    }

    public function testProcessNoTaggedServicesFound()
    {
        $this->containerBuilder
            ->expects($this->once())
            ->method('hasDefinition')
            ->with(TaxProviderPass::REGISTRY_SERVICE)
            ->willReturn(true);

        $this->containerBuilder
            ->expects($this->once())
            ->method('findTaggedServiceIds')
            ->willReturn([]);

        $this->containerBuilder
            ->expects($this->never())
            ->method('getDefinition');

        $this->compilerPass->process($this->containerBuilder);
    }

    public function testProcessWithTaggedServices()
    {
        $this->containerBuilder
            ->expects($this->once())
            ->method('hasDefinition')
            ->with(TaxProviderPass::REGISTRY_SERVICE)
            ->willReturn(true);

        $registryServiceDefinition = $this->createMock('Symfony\Component\DependencyInjection\Definition');

        $this->containerBuilder
            ->expects($this->once())
            ->method('getDefinition')
            ->with(TaxProviderPass::REGISTRY_SERVICE)
            ->willReturn($registryServiceDefinition);

        $taggedServices = [
            'service.name.1' => [['priority' => 1]],
            'service.name.2' => [[]],
            'service.name.3' => [['priority' => -255]],
            'service.name.4' => [['priority' => 255]],
        ];

        $this->containerBuilder
            ->expects($this->once())
            ->method('findTaggedServiceIds')
            ->willReturn($taggedServices);

        $registryServiceDefinition
            ->expects($this->exactly(4))
            ->method('addMethodCall')
            ->withConsecutive(
                ['addProvider', [new Reference('service.name.4')]],
                ['addProvider', [new Reference('service.name.1')]],
                ['addProvider', [new Reference('service.name.2')]],
                ['addProvider', [new Reference('service.name.3')]]
            );

        $this->compilerPass->process($this->containerBuilder);
    }
}
