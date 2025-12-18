<?php

namespace Agenda\Controller\Factory;

use Agenda\Entity\Agenda;
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
        $agendaRepository = $entityManager->getRepository(Agenda::class);
        $agendaService = new AgendaService($entityManager, $agendaItemRepository, $agendaRepository,  $config);

        return new AgendaController(
            $vhm,
            $agendaService,
            $entityManager,
        );
    }

}
