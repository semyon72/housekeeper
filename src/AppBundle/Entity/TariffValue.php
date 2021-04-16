<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TariffValue
 *
 * @ORM\Table(name="tariff_value", indexes={@ORM\Index(name="tariff_id", columns={"tariff_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TariffValueRepository")
 */
class TariffValue
{
    /**
     * @var string
     *
     * @ORM\Column(name="value_from", type="decimal", precision=11, scale=4, nullable=false)
     */
    private $valueFrom = '0.0000';

    /**
     * @var string
     *
     * @ORM\Column(name="value_to", type="decimal", precision=11, scale=4, nullable=true)
     */
    private $valueTo;

    /**
     * @var string
     *
     * @ORM\Column(name="unit_value", type="decimal", precision=7, scale=4, nullable=false)
     */
    private $unitValue = '0.0000';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Tariff
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Tariff", inversedBy="tariffValues")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tariff_id", referencedColumnName="id")
     * })
     */
    private $tariff;



    /**
     * Set valueFrom
     *
     * @param string $valueFrom
     *
     * @return TariffValue
     */
    public function setValueFrom($valueFrom)
    {
        $this->valueFrom = $valueFrom;

        return $this;
    }

    /**
     * Get valueFrom
     *
     * @return string
     */
    public function getValueFrom()
    {
        return $this->valueFrom;
    }

    /**
     * Set valueTo
     *
     * @param string $valueTo
     *
     * @return TariffValue
     */
    public function setValueTo($valueTo)
    {
        $this->valueTo = $valueTo;

        return $this;
    }

    /**
     * Get valueTo
     *
     * @return string
     */
    public function getValueTo()
    {
        return $this->valueTo;
    }

    /**
     * Set unitValue
     *
     * @param string $unitValue
     *
     * @return TariffValue
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set tariff
     *
     * @param \AppBundle\Entity\Tariff $tariff
     *
     * @return TariffValue
     */
    public function setTariff(\AppBundle\Entity\Tariff $tariff = null)
    {
        $this->tariff = $tariff;

        return $this;
    }

    /**
     * Get tariff
     *
     * @return \AppBundle\Entity\Tariff
     */
    public function getTariff()
    {
        return $this->tariff;
    }
}
