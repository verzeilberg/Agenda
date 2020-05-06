<?php

namespace Agenda\Controller\Factory;

use Agenda\Entity\AgendaItem;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Agenda\Controller\AgendaController;
use Agenda\Service\AgendaService;

/**
 * This is the factory for AgendaController. Its purpose is to instantiate the controller
 * and inject dependencies into its constructor.
 */
class AgendaControllerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {


        $config = $container->get('config');
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $vhm = $container->get('ViewHelperManager');
        $agendaItemRepository = $entityManager->getRepository(AgendaItem::class);
        $agendaService = new AgendaService($agendaItemRepository, $config);

        return new AgendaController(
            $vhm,
            $agendaService
        );
    }

}
