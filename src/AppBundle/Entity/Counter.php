<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Counter
 *
 * @ORM\Table(name="counter", indexes={@ORM\Index(name="place_id", columns={"place_id"}), @ORM\Index(name="service_id", columns={"service_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CounterRepository")
 */
class Counter
{
    /**
     * @var string
     *
     * @ORM\Column(name="value", type="decimal", precision=11, scale=4, nullable=false)
     */
    private $value;

    /**
     * @var string
     *
     * @ORM\Column(name="value_name", type="string", length=16, nullable=false)
     */
    private $valueName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="on_date", type="datetime", nullable=false)
     */
    private $onDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_c", type="datetime", nullable=false)
     */
    private $dateC;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Service
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Service")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="service_id", referencedColumnName="id")
     * })
     */
    private $service;

    /**
     * @var \AppBundle\Entity\Place
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Place")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="place_id", referencedColumnName="id")
     * })
     */
    private $place;


    public function __construct() {
        $this->setOnDate(new \DateTime())->setDateC(new \DateTime())->setValueName('by counter');
    }

    /**
     * Set value
     *
     * @param string $value
     *
     * @return Counter
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set valueName
     *
     * @param string $valueName
     *
     * @return Counter
     */
    public function setValueName($valueName)
    {
        $this->valueName = $valueName;

        return $this;
    }

    /**
     * Get valueName
     *
     * @return string
     */
    public function getValueName()
    {
        return $this->valueName;
    }

    /**
     * Set onDate
     *
     * @param \DateTime $onDate
     *
     * @return Counter
     */
    public function setOnDate($onDate)
    {
        $this->onDate = $onDate;

        return $this;
    }

    /**
     * Get onDate
     *
     * @return \DateTime
     */
    public function getOnDate()
    {
        return $this->onDate;
    }

    /**
     * Set dateC
     *
     * @param \DateTime $dateC
     *
     * @return Counter
     */
    public function setDateC($dateC)
    {
        $this->dateC = $dateC;

        return $this;
    }

    /**
     * Get dateC
     *
     * @return \DateTime
     */
    public function getDateC()
    {
        return $this->dateC;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set service
     *
     * @param \AppBundle\Entity\Service $service
     *
     * @return Counter
     */
    public function setService(\AppBundle\Entity\Service $service = null)
    {
        $this->service = $service;

        return $this;
    }

    /**
     * Get service
     *
     * @return \AppBundle\Entity\Service
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Set place
     *
     * @param \AppBundle\Entity\Place $place
     *
     * @return Counter
     */
    public function setPlace(\AppBundle\Entity\Place $place = null)
    {
        $this->place = $place;

        return $this;
    }

    /**
     * Get place
     *
     * @return \AppBundle\Entity\Place
     */
    public function getPlace()
    {
        return $this->place;
    }
}
