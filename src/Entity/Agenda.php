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
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     * @Annotation\Options({
     * "label": "Description",
     * "label_attributes": {"class": "col-sm-1 col-md-1 col-lg-1 form-control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"title"})
     */
    protected $title;

    /**
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     * @Annotation\Options({
     * "label": "Description",
     * "label_attributes": {"class": "col-sm-1 col-md-1 col-lg-1 form-control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"description"})
     */
    protected $description;

    /**
     * @ORM\Column(name="date", type="date", nullable=false)
     * @Annotation\Options({
     * "label": "Date",
     * "label_attributes": {"class": "col-sm-1 col-md-1 col-lg-1 form-control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"date"})
     */
    protected $date;

    /**
     * @ORM\Column(name="time", type="time", nullable=true)
     * @Annotation\Options({
     * "label": "Date",
     * "label_attributes": {"class": "col-sm-1 col-md-1 col-lg-1 form-control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"date"})
     */
    protected $time;



}
