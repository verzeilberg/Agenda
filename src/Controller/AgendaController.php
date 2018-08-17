<?php

namespace Agenda\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AgendaController extends AbstractActionController {

    protected $vhm;
    protected $em;
    protected $agendaService;

    public function __construct($vhm, $em, $agendaService) {
        $this->vhm = $vhm;
        $this->em = $em;
        $this->agendaService = $agendaService;
    }

    public function indexAction() {
        $this->layout('layout/beheer');
        $agendaForms = $this->agendaService->getAgendas();
        return new ViewModel(
                array(
            'agendaForms' => $agendaForms,
                )
        );
    }

}
