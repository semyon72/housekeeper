<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Tariff
 *
 * @ORM\Table(name="tariff", indexes={@ORM\Index(name="place_id", columns={"place_id"}), @ORM\Index(name="service_id", columns={"service_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TariffRepository")
 */
class Tariff
{
    /**
     * @var string
     *
     * @ORM\Column(name="unit_name", type="string", length=16, nullable=false)
     */
    private $unitName;

    /**
     * @var string
     *
     * @ORM\Column(name="unit_value", type="decimal", precision=7, scale=4, nullable=false)
     */
    private $unitValue = '0.0000';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_b", type="datetime", nullable=false)
     */
    private $dateB;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_e", type="datetime", nullable=true)
     */
    private $dateE;

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
    
    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\TariffValue", mappedBy="tariff")
     */
    private $tariffValues;
    
    

    public function __construct() {
        
        $this->tariffValues = new ArrayCollection();
        
        $this->setDateB(new \DateTime() )->setDateC(new \DateTime());
    }

    /**
     * Set unitName
     *
     * @param string $unitName
     *
     * @return Tariff
     */
    public function setUnitName($unitName)
    {
        $this->unitName = $unitName;

        return $this;
    }

    /**
     * Get unitName
     *
     * @return string
     */
    public function getUnitName()
    {
        return $this->unitName;
    }

    /**
     * Set unitValue
     *
     * @param string $unitValue
     *
     * @return Tariff
     */
    public function setUnitValue($unitValue)
    {
        $this->unitValue = $unitValue;

        return $this;
    }

    /**
     * Get unitValue
     *
     * @return string
     */
    public function getUnitValue()
    {
        return $this->unitValue;
    }

    /**
     * Set dateB
     *
     * @param \DateTime $dateB
     *
     * @return Tariff
     */
    public function setDateB($dateB)
    {
        $this->dateB = $dateB;

        return $this;
    }

    /**
     * Get dateB
     *
     * @return \DateTime
     */
    public function getDateB()
    {
        return $this->dateB;
    }

    /**
     * Set dateE
     *
     * @param \DateTime $dateE
     *
     * @return Tariff
     */
    public function setDateE($dateE)
    {
        $this->dateE = $dateE;

        return $this;
    }

    /**
     * Get dateE
     *
     * @return \DateTime
     */
    public function getDateE()
    {
        return $this->dateE;
    }

    /**
     * Set dateC
     *
     * @param \DateTime $dateC
     *
     * @return Tariff
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
     * @return Tariff
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
     * @return Tariff
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
    
    
    public function getTariffValues(){
        return $this->tariffValues;;
    }
    

    /**
     * Add tariffValue
     *
     * @param \AppBundle\Entity\TariffValue $tariffValue
     *
     * @return Tariff
     */
    public function addTariffValue(\AppBundle\Entity\TariffValue $tariffValue)
    {
        $this->tariffValues[] = $tariffValue;

        return $this;
    }

    /**
     * Remove tariffValue
     *
     * @param \AppBundle\Entity\TariffValue $tariffValue
     */
    public function removeTariffValue(\AppBundle\Entity\TariffValue $tariffValue)
    {
        $this->tariffValues->removeElement($tariffValue);
    }
}
