<?php


namespace Agenda\Form;

use Laminas\Form\Form;


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