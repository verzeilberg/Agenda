<?php

namespace Agenda\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Doctrine\Common\Collections\ArrayCollection;
use Application\Model\UnityOfWork;

/**
 * This class represents a agenda item.
 * @ORM\Entity(repositoryClass="Agenda\Repository\AgendaItemRepository")
 * @ORM\Table(name="agenda_item")
 */
class AgendaItem extends UnityOfWork {

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     * @Annotation\Options({
     * "label": "Titel",
     * "label_attributes": {"class": "col form-control-label", "for":"title"}
     * })
     * @Annotation\Attributes({"id":"title","class":"form-control","required":"required", "placeholder":"titel"})
     */
    protected $title;

    /**
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     * @Annotation\Options({
     * "label": "Omschrijving",
     * "label_attributes": {"class": "col form-control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"omschrijving"})
     */
    protected $description;

    /**
     * @ORM\Column(name="start_date", type="date", nullable=false)
     * @Annotation\Options({
     * "label": "Start date",
     * "label_attributes": {"class": "col"}
     * })
     * @Annotation\Attributes({"class":"form-control", "required":"required", "placeholder":"start date"})
     */
    protected $startDate;

    /**
     * @ORM\Column(name="end_date", type="date", nullable=false)
     * @Annotation\Options({
     * "label": "End date",
     * "label_attributes": {"class": "col form-control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "required":"required", "placeholder":"end date"})
     */
    protected $endDate;

    /**
     * @ORM\Column(name="start_time", type="time", nullable=true)
     * @Annotation\Options({
     * "label": "Start time",
     * "label_attributes": {"class": "col"}
     * })
     * @Annotation\Attributes({"id":"startTime","required":"required"})
     */
    protected $startTime;

    /**
     * @ORM\Column(name="end_time", type="time", nullable=false)
     * @Annotation\Options({
     * "label": "End time",
     * "label_attributes": {"class": "col form-control-label"}
     * })
     * @Annotation\Attributes({"id":"endTime","required":"required"})
     */
    protected $endTime;

    /**
     * @ORM\Column(name="whole_day", type="integer", length=1, nullable=true)
     * @Annotation\Type("Zend\Form\Element\Checkbox")
     * @Annotation\Options({
     * "label": "Gehele dag",
     * "label_attributes": {"class": "form-check-label", "for":"wholeDay"},
     * "value_options":{
     * "1":"Gehele dag"
     * }
     * })
     * @Annotation\Attributes({"id":"wholeDay","class":"form-check-input"})
     */
    protected $wholeDay;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return AgendaItem
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     * @return AgendaItem
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     * @return AgendaItem
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param mixed $startDate
     * @return AgendaItem
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param mixed $endDate
     * @return AgendaItem
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @param mixed $startTime
     * @return AgendaItem
     */
    public function setStartTime($startTime)
    {

        $startTime = new \DateTime($startTime);
        $this->startTime = $startTime;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * @param mixed $endTime
     * @return AgendaItem
     */
    public function setEndTime($endTime)
    {

        $endTime = new \DateTime($endTime);
        $this->endTime = $endTime;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getWholeDay()
    {
        return $this->wholeDay;
    }

    /**
     * @param mixed $wholeDay
     * @return AgendaItem
     */
    public function setWholeDay($wholeDay)
    {
        $this->wholeDay = $wholeDay;
        return $this;
    }




}
