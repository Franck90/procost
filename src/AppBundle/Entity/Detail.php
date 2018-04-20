<?php
//src/AppBundle/Entity/Detail.php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Detail
 *
 * @ORM\Table(name="detail")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DetailRepository")
 */
class Detail
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\Range(
     *      min = 1,
     *      minMessage = "La durée doit être d'au moins 1 jour",
     * )
     * @ORM\Column(name="duration", type="integer")
     */
    private $duration;

    /**
     * @var \DateTime
     * @Assert\NotBlank()
     * @Assert\Date()
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="Employee", inversedBy="detail")
     */
    private $employee;

    /**
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="detail")
     */
    private $project;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->date = new \DateTime("now");
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set duration
     *
     * @param integer $duration
     *
     * @return Detail
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Detail
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set employee
     *
     * @param \AppBundle\Entity\Employee $employee
     *
     * @return Detail
     */
    public function setEmployee(\AppBundle\Entity\Employee $employee = null)
    {
        $this->employee = $employee;

        return $this;
    }

    /**
     * Get employee
     *
     * @return \AppBundle\Entity\Employee
     */
    public function getEmployee()
    {
        return $this->employee;
    }

    /**
     * Set project
     *
     * @param \AppBundle\Entity\Project $project
     *
     * @return Detail
     */
    public function setProject(\AppBundle\Entity\Project $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return \AppBundle\Entity\Project
     */
    public function getProject()
    {
        return $this->project;
    }
}
