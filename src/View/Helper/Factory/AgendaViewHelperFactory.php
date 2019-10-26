<?php
namespace Agenda\View\Helper\Factory;

use Agenda\Entity\AgendaItem;
use Agenda\Service\AgendaService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Agenda\View\Helper\AgendaHelper;

/**
 * This is the factory for Menu view helper. Its purpose is to instantiate the
 * helper and init menu items.
 */
class AgendaViewHelperFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $agendaItemRepository = $entityManager->getRepository(AgendaItem::class);
        $agendaService = new AgendaService($agendaItemRepository);

        // Instantiate the helper.
        return new AgendaHelper($agendaService);
    }
}
