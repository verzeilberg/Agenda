<?php


namespace Agenda\Form;

use Zend\Form\Form;


class AgendaForm extends Form
{
    public function init()
    {
        $this->add([
            'name' => 'phone',
            'type' => 'phone',
        ]);
    }
}