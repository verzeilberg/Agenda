<?php

namespace Agenda\Controller;

use DateTime;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend\Validator\Date;
use Zend\View\Model\JsonModel;
use Zend\View\HelperPluginManager;
use Agenda\Service\AgendaService;

class AgendaAjaxController extends AbstractActionController
{

    /*
     * @var HelperPluginManager
     */
    protected $viewhelpermanager;

    /*
     * @var AgendaService
     */
    protected $agendaService;

    public function __construct(
        HelperPluginManager $vhm,
        AgendaService $agendaService
    )
    {
        $this->viewhelpermanager = $vhm;
        $this->agendaService = $agendaService;
    }

    public function getDateDataAction()
    {
        $success = true;
        $errorMessage = null;
        $dateLink = $this->params()->fromPost('date', null);
        $date = substr($dateLink, strrpos($dateLink, '/') + 1);
        $date = new DateTime($date);
        $dateData = [
            'day' => $date->format('j'),
            'dayName' => $this->agendaService->getDayLabels('nl', 'long')[$date->format('N')],
            'month' => $this->agendaService->getMonthLabels('nl', $date->format('n')),
            'year' => $date->format('Y')
        ];


        return new JsonModel([
            'errorMessage' => $errorMessage,
            'success' => $success,
            'dateData' => $dateData
        ]);

    }

    public function addAgendaItemAction()
    {
        $success = true;
        $errorMessage = null;
        $data = $this->params()->fromPost('data', null);

        $agendaItem = $this->agendaService->agendaItemRepository->createAgendaItem();

        foreach($data as $item) {
            $setter = 'set' . ucfirst($item['name']);
            $agendaItem->$setter($item['value']);
        }

        $agendaItem->setStartDate(new DateTime());
        $agendaItem->setEndDate(new DateTime());

        $agendaItem->setDateCreated(new DateTime("now"));
        $agendaItem->setCreatedBy(null);
        $success = $this->agendaService->agendaItemRepository->storeAgendaItem($agendaItem);

        return new JsonModel([
            'errorMessage' => $errorMessage,
            'data' => $data,
            'success' => $success
        ]);
    }

}
