<?php

namespace Agenda\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Doctrine\Common\Collections\ArrayCollection;
use Application\Model\UnityOfWork;

/**
 * This class represents a agenda item.
 * @ORM\Entity()
 * @ORM\Table(name="agenda")
 */
class Agenda extends UnityOfWork {

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="salutation", type="integer", length=1, nullable=true)
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Attributes({"class":"form-control"})
     * @Annotation\Options({
     * "label": "Aanhef", 
     * "label_attributes": {"class": "col-sm-1 col-md-1 col-lg-1 form-control-label"},
     * "value_options":{
     * "1":"Dhr.",
     * "2":"Mvr."
     * }
     * })
     */
    protected $salutation;

    /**
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     * @Annotation\Options({
     * "label": "Naam",
     * "label_attributes": {"class": "col-sm-1 col-md-1 col-lg-1 form-control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Naam"})
     */
    protected $name;
    
        /**
     * @ORM\Column(name="agenda", type="string", length=255, nullable=false)
     * @Annotation\Options({
     * "label": "E-mail",
     * "label_attributes": {"class": "col-sm-1 col-md-1 col-lg-1 form-control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"E-mail"})
     */
    protected $agenda;

    /**
     * @ORM\Column(name="subject", type="string", length=255, nullable=false)
     * @Annotation\Options({
     * "label": "Onderwerp",
     * "label_attributes": {"class": "col-sm-1 col-md-1 col-lg-1 form-control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Onderwerp"})
     */
    protected $subject;

    /**
     * @ORM\Column(name="message", type="text", nullable=false)
     * @Annotation\Options({
     * "label": "Bericht",
     * "label_attributes": {"class": "col-sm-1 col-md-1 col-lg-1 form-control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control"})
     */
    protected $message;

    
    function getId() {
        return $this->id;
    }

    function getSalutation() {
        
        if($this->salutation == 1){
            return 'De heer';
        } else if ($this->salutation == 2){
            return 'Mevrouw';
        } else {
            return 'Onbekend';
        }
    }

    function getName() {
        return $this->name;
    }

    function getEmail() {
        return $this->agenda;
    }

    function getSubject() {
        return $this->subject;
    }

    function getMessage() {
        return $this->message;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setSalutation($salutation) {
        $this->salutation = $salutation;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setEmail($agenda) {
        $this->agenda = $agenda;
    }

    function setSubject($subject) {
        $this->subject = $subject;
    }

    function setMessage($message) {
        $this->message = $message;
    }


}
