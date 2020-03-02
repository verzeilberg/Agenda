<?php

namespace Agenda\Controller;

use DateTime;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;
use Zend\View\HelperPluginManager;
use Agenda\Service\AgendaService;

class AgendaController extends AbstractActionController {

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
    ) {
        $this->viewhelpermanager = $vhm;
        $this->agendaService = $agendaService;
    }

    /**
     * We override the parent class' onDispatch() method to
     * set an alternative layout for all actions in this controller.
     */
    public function onDispatch(MvcEvent $e)
    {
        // Call the base class' onDispatch() first and grab the response
        $response = parent::onDispatch($e);

        // Set alternative layout
        $this->layout()->setTemplate('layout/beheer');

        // Return the response
        return $response;
    }

    public function indexAction() {
        $this->viewhelpermanager->get('headLink')->appendStylesheet('/css/agenda.css');
        $this->viewhelpermanager->get('headLink')->appendStylesheet('https://fonts.googleapis.com/css?family=Montserrat&display=swap');
        $this->viewhelpermanager->get('headLink')->appendStylesheet('/css/timeshift/timeshift-1.0.css');
        $this->viewhelpermanager->get('headLink')->appendStylesheet('/css/timeshift/dateshift-1.0.css');
        $this->viewhelpermanager->get('headScript')->appendFile('/js/timeshift/timeshift-1.0.js');
        $this->viewhelpermanager->get('headScript')->appendFile('/js/timeshift/dateshift-1.0.js');
        $this->viewhelpermanager->get('headScript')->appendFile('/js/agenda.js');
        $year = (int)$this->params()->fromQuery('year', null);
        $month = (int)$this->params()->fromQuery('month', null);

        $agendaItem = $this->agendaService->agendaItemRepository->createAgendaItem();
        $form = $this->agendaService->createAgendaItemForm($agendaItem);

        $currentDate = new DateTime();

        if ($year == null OR $month == null) {
            $selectedMonthYear =  clone $currentDate;
            $firstDayOfTheMonth = new DateTime('first day of this month');
        } else {
            $selectedMonthYear = new DateTime($year.'-'.$month.'-1');
            $firstDayOfTheMonth = new DateTime($year.'-'.$month.'-1');
        }
        $weeksInMonth = $this->agendaService->weeksInMonth(
            $selectedMonthYear->format('m'),
            $selectedMonthYear->format('Y')
        );
        $dayLabels = $this->agendaService->getDayLabels('nl', 'short');
        $navigationLinks = $this->agendaService->getNavigationParams(
            $selectedMonthYear->format('m'),
            $selectedMonthYear->format('Y')
        );

        $agendaItems = $this->agendaService->agendaItemRepository->getAgendaItemsByMonth($selectedMonthYear);
        $agendaItemsForCurrentDate = $this->agendaService->agendaItemRepository->getAgendaItemsByDay($currentDate);

        return new ViewModel([
            'month' => $month,
            'year' => $year,
            'currentDate' => $currentDate,
            'agendaItemsForCurrentDate' => $agendaItemsForCurrentDate,
            'firstDayOfTheMonth' => $firstDayOfTheMonth,
            'selectedMonthYear' => $selectedMonthYear,
            'weeksInMonth' => $weeksInMonth,
            'navigationLinks' => $navigationLinks,
            'dayLabels' => $dayLabels,
            'layout' => 'month',
            'form' => $form,
            'agendaItems' => $agendaItems
        ]);
    }

    public function dayAction()
    {
        $this->viewhelpermanager->get('headLink')->appendStylesheet('/css/agenda.css');
        $day = (int)$this->params()->fromRoute('day', null);

        if ($day == null){
            $date = new DateTime();
        } else {
            $date = $this->agendaService->checkDayDate($day);
        }

        $agendaItems = $this->agendaService->agendaItemRepository->getAgendaItemsByDay($date);



        $twentyFourHours = $this->agendaService->getTwentyFourHours();

        return new ViewModel([
            'date' => $date,
            'twentyFourHours' => $twentyFourHours,
            'agendaItems' => $agendaItems
        ]);

    }
    public function weekAction()
    {
        $weekNr = (int)$this->params()->fromRoute('day', null);
        return new ViewModel([

        ]);
    }

}
