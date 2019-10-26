<?php

namespace Agenda\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Doctrine\Common\Collections\ArrayCollection;
use Application\Model\UnityOfWork;

/**
 * This class represents a agenda.
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
     * "label_attributes": {"class": "col form-control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"title"})
     */
    protected $title;

    /**
     * @ORM\Column(name="whole_day", type="integer", length=1,  nullable=true)
     * @Annotation\Type("Zend\Form\Element\Checkbox")
     * @Annotation\Options({
     * "label": "Gehele dag",
     * "label_attributes": {"class": "col-sm-1 col-md-1 col-lg-1 form-control-label"},
     * "value_options":{
     * "1":"Publiek."
     * }
     * })
     * @Annotation\Attributes({"class":"form-control"})
     */
    protected $public;

    /**
     * Many Users have Many Groups.
     * @ORM\ManyToMany(targetEntity="User\Entity\User")
     * @ORM\JoinTable(name="agendas_users",
     *      joinColumns={@ORM\JoinColumn(name="agenda_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}
     *      )
     */
    private $users;

    public function __construct() {
        $this->users = new ArrayCollection();
    }
}