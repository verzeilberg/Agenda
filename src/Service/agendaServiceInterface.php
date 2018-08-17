<?php

namespace Agenda\Service;

interface agendaServiceInterface {

    /**
     *
     * Get array of agendas
     *
     * @return      array
     *
     */
    public function getAgendas();
    
    public function getAgendaFormById($id);
    
    public function deleteAgendaForm($agendaForm);

    /**
     *
     * Send mail
     * 
     * @param       agenda $agenda object
     * @return      void
     *
     */
    public function sendMail($agenda);

    /**
     *
     * Get base url
     * 
     * @return      string
     *
     */
    public function getBaseUrl();
}
