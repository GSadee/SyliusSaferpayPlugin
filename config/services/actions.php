<?php

declare(strict_types=1);

use CommerceWeavers\SyliusSaferpayPlugin\Controller\Action\AssertAction;
use CommerceWeavers\SyliusSaferpayPlugin\Controller\Action\PrepareAssertAction;
use CommerceWeavers\SyliusSaferpayPlugin\Controller\Action\PrepareCaptureAction;
use CommerceWeavers\SyliusSaferpayPlugin\Payum\Factory\AssertFactoryInterface;
use CommerceWeavers\SyliusSaferpayPlugin\Provider\PaymentProviderInterface;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\inline_service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services
        ->set(PrepareAssertAction::class)
        ->args([
            service('sylius.resource_controller.request_configuration_factory'),
            inline_service(MetadataInterface::class)
                ->factory([service('sylius.resource_registry'), 'get'])
                ->args(['sylius.order']),
            service(PaymentProviderInterface::class),
            service('payum'),
        ])
        ->tag('controller.service_arguments')
    ;

    $services
        ->set(AssertAction::class)
        ->args([
            service('payum'),
            service('sylius.factory.payum_get_status_action'),
            service('sylius.factory.payum_resolve_next_route'),
            service(AssertFactoryInterface::class),
            service('router'),
        ])
        ->tag('controller.service_arguments')
    ;

    $services
        ->set(PrepareCaptureAction::class)
        ->args([
            service('sylius.resource_controller.request_configuration_factory'),
            inline_service(MetadataInterface::class)
                ->factory([service('sylius.resource_registry'), 'get'])
                ->args(['sylius.order']),
            service(PaymentProviderInterface::class),
            service('payum'),
        ])
        ->tag('controller.service_arguments')
    ;
};