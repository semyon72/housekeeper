<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Receipt
 *
 * @ORM\Table(name="receipt", indexes={@ORM\Index(name="tariff_id", columns={"tariff_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ReceiptRepository")
 */
class Receipt
{
    /**
     * @var string
     *
     * @ORM\Column(name="value_b", type="decimal", precision=11, scale=4, nullable=false)
     */
    private $valueB;

    /**
     * @var string
     *
     * @ORM\Column(name="value_e", type="decimal", precision=11, scale=4, nullable=false)
     */
    private $valueE;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="decimal", precision=11, scale=4, nullable=false)
     */
    private $value;

    /**
     * @var string
     *
     * @ORM\Column(name="total", type="decimal", precision=14, scale=4, nullable=false)
     */
    private $total;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_b", type="datetime", nullable=false)
     */
    private $dateB;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_e", type="datetime", nullable=false)
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
     * @var \AppBundle\Entity\Tariff
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Tariff")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tariff_id", referencedColumnName="id")
     * })
     */
    private $tariff;
    
    /**
     * 
     * @var ArrayCollection of \AppBundle\Entity\ReceiptAdjustment
     *
     * @ORM\OneToMany(targetEntity="ReceiptAdjustment", mappedBy="receipt")
     * 
     */
    private $adjustments;
    

    public function __construct() {
        $this->adjustments = new ArrayCollection();
        $this->setDateB((new \DateTime())->modify('first day of this month midnight'))
            ->setDateE((new \DateTime())->modify('last day of this month 23:59:59'))
            ->setDateC(new \DateTime())
            ->setValueB(0)
            ->setValueE(0);
    }

    /**
     * Set valueB
     *
     * @param string $valueB
     *
     * @return Receipt
     */
    public function setValueB($valueB)
    {
        $this->valueB = $valueB;

        return $this;
    }

    /**
     * Get valueB
     *
     * @return string
     */
    public function getValueB()
    {
        return $this->valueB;
    }

    /**
     * Set valueE
     *
     * @param string $valueE
     *
     * @return Receipt
     */
    public function setValueE($valueE)
    {
        $this->valueE = $valueE;

        return $this;
    }

    /**
     * Get valueE
     *
     * @return string
     */
    public function getValueE()
    {
        return $this->valueE;
    }

    /**
     * Set value
     *
     * @param string $value
     *
     * @return Receipt
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
     * Set total
     *
     * @param string $total
     *
     * @return Receipt
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total
     *
     * @return string
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set dateB
     *
     * @param \DateTime $dateB
     *
     * @return Receipt
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
     * @return Receipt
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
     * @return Receipt
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
     * Set tariff
     *
     * @param \AppBundle\Entity\Tariff $tariff
     *
     * @return Receipt
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
    
    /**
     * Get receipt's adjustments
     *
     * @return ArrayCollection Returns the ArrayCollection of ReceiptAdjustment-s
     */
    public function getAdjustments()
    {
        return $this->adjustments;
    }
    
}
