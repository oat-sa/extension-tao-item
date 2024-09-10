<?php

namespace oat\taoItems\model\Translation\ServiceProvider;

use oat\generis\model\DependencyInjection\ContainerServiceProviderInterface;
use oat\tao\model\Translation\Form\Modifier\TranslationFormModifier as TaoTranslationFormModifier;
use oat\taoItems\model\Translation\Form\Modifier\TranslationFormModifierProxy;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

class TranslationServiceProvider implements ContainerServiceProviderInterface
{
    public function __invoke(ContainerConfigurator $configurator): void
    {
        $services = $configurator->services();

        $services
            ->set(TranslationFormModifierProxy::class, TranslationFormModifierProxy::class)
            ->public();

        $services
            ->get(TranslationFormModifierProxy::class)
            ->call(
                'addModifier',
                [
                    service(TaoTranslationFormModifier::class),
                ]
            );
    }
}
