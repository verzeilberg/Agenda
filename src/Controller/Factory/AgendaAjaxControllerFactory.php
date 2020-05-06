<?php

namespace Agenda\Controller\Factory;

use Agenda\Entity\AgendaItem;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Agenda\Controller\AgendaAjaxController;
use Agenda\Service\AgendaService;

/**
 * This is the factory for AgendaController. Its purpose is to instantiate the controller
 * and inject dependencies into its constructor.
 */
class AgendaAjaxControllerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $vhm = $container->get('ViewHelperManager');
        $agendaItemRepository = $entityManager->getRepository(AgendaItem::class);
        $agendaService = new AgendaService($agendaItemRepository);
        return new AgendaAjaxController(
            $vhm,
            $agendaService
        );
    }

}
