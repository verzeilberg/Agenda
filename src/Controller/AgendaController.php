<?php

namespace Agenda\Controller;

use Agenda\Entity\Agenda;
use Agenda\Form\AgendaForm;
use Agenda\Form\AgendaImportForm;
use DateTime;
use Doctrine\ORM\EntityManager;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\MvcEvent;
use Laminas\View\Model\ViewModel;
use Laminas\View\HelperPluginManager;
use Agenda\Service\AgendaService;
use Symfony\Component\VarDumper\VarDumper;

class AgendaController extends AbstractActionController {

    /*
     * @var HelperPluginManager
     */
    protected $viewhelpermanager;

    /*
     * @var AgendaService
     */
    protected $agendaService;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(
        HelperPluginManager $vhm,
        AgendaService $agendaService,
        $entityManager
    ) {
        $this->viewhelpermanager    = $vhm;
        $this->agendaService        = $agendaService;
        $this->entityManager        = $entityManager;
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

    /**
     * Handles the index action for the agenda module. It retrieves a paginated
     * list of agendas and optionally filters them based on a search string
     * provided via a POST request.
     *
     * @return ViewModel A ViewModel instance containing the search string and
     *         the paginated list of agendas.
     */
    public function indexAction(): ViewModel
    {

        $page = $this->params()->fromQuery('page', 1);
        $query = $this->agendaService->getAgendas();

        $searchString = '';
        if ($this->getRequest()->isPost()) {
            $searchString = $this->getRequest()->getPost('search');
            $query = $this->agendaService->searchAgendas($searchString);
        }

        $agendas = $this->agendaService->getItemsForPagination($query, $page, 10);

        return new ViewModel([
            'searchString' => $searchString,
            'agendas' => $agendas,
        ]);
    }

    public function addAction(): ViewModel
    {
        $agenda = new Agenda();
        // Create the form and inject the EntityManager
        $form = new AgendaForm($this->entityManager);
        $form->bind($agenda);
        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $agenda->setCreatedBy($this->currentUser());
                $agenda->setDateCreated(new DateTime());
                $this->entityManager->persist($agenda);
                $this->entityManager->flush();
                $this->flashMessenger()->addSuccessMessage('Agenda opgeslagen');
                return $this->redirect()->toRoute('beheer/agenda', ['action' => 'index']);
            }
        }

        return new ViewModel([
            'form' => $form
        ]);
    }

    /**
     * @return ViewModel
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function editAction(): ViewModel
    {
        $id = (int) $this->params()->fromRoute('day', 0);
        if (empty($id)) {
            return $this->redirect()->toRoute('beheer/agenda');
        }
        $agenda = $this->agendaService->agendaRepository->find($id);
        if (empty($agenda)) {
            return $this->redirect()->toRoute('beheer/agenda');
        }

        // Create the form and inject the EntityManager
        $form = new AgendaForm($this->entityManager);
        $form->bind($agenda);
        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $agenda->setChangedBy($this->currentUser());
                $agenda->setDateChanged(new DateTime());
                $this->entityManager->persist($agenda);
                $this->entityManager->flush();
                $this->flashMessenger()->addSuccessMessage('Agenda aangepast');
            }
        }

        return new ViewModel([
            'form' => $form
        ]);
    }

    /**
     * Prepares the view for the agenda detail action by appending necessary
     * stylesheets and scripts, initializing the required form and fetching
     * agenda items for the current month and selected date.
     *
     * @return ViewModel The view model containing the agenda details and form.
     */
    public function detailAction(): ViewModel
    {
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

    Public function importAction()
    {
        // Create the form and inject the EntityManager
        $form = new AgendaImportForm($this->entityManager);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $url = $this->getRequest()->getPost('agenda-import')['url'];
                $this->agendaService->importAgendaItems($url);
            }
        }

        return new ViewModel([
            'form' => $form
        ]);
    }

}
