<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ReceiptAdjustment
 *
 * @ORM\Table(name="receipt_adjustment", indexes={@ORM\Index(name="tariff_id", columns={"tariff_id"}), @ORM\Index(name="receipt_id", columns={"receipt_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ReceiptAdjustmentRepository")
 */
class ReceiptAdjustment
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="value_b", type="decimal", precision=11, scale=4, nullable=false)
     */
    private $valueB = '0.0000';

    /**
     * @var string
     *
     * @ORM\Column(name="value_e", type="decimal", precision=11, scale=4, nullable=false)
     */
    private $valueE = '0.0000';

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="decimal", precision=11, scale=4, nullable=false)
     */
    private $value = '0.0000';

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
     * @var string
     *
     * @ORM\Column(name="note", type="string", length=255, nullable=false)
     */
    private $note = '';

    /**
     * @var \Tariff
     *
     * @ORM\ManyToOne(targetEntity="Tariff")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tariff_id", referencedColumnName="id")
     * })
     */
    private $tariff;

    /**
     * @var \Receipt
     *
     * @ORM\ManyToOne(targetEntity="Receipt", inversedBy="adjustments")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="receipt_id", referencedColumnName="id")
     * })
     */
    private $receipt;


    public function __construct() {
        $this->setDateB((new \DateTime())->modify('first day of this month midnight'))
            ->setDateE((new \DateTime())->modify('last day of this month 23:59:59'))
            ->setDateC(new \DateTime());
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
     * Set valueB
     *
     * @param string $valueB
     *
     * @return ReceiptAdjustment
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
     * @return ReceiptAdjustment
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
     * @return ReceiptAdjustment
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
     * @return ReceiptAdjustment
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
     * @return ReceiptAdjustment
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
     * @return ReceiptAdjustment
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
     * @return ReceiptAdjustment
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
     * Set note
     *
     * @param string $note
     *
     * @return ReceiptAdjustment
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set tariff
     *
     * @param \AppBundle\Entity\Tariff $tariff
     *
     * @return ReceiptAdjustment
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
     * Set receipt
     *
     * @param \AppBundle\Entity\Receipt $receipt
     *
     * @return ReceiptAdjustment
     */
    public function setReceipt(\AppBundle\Entity\Receipt $receipt)
    {
        $this->receipt = $receipt;

        return $this;
    }

    /**
     * Get receipt
     *
     * @return \AppBundle\Entity\Receipt
     */
    public function getReceipt()
    {
        return $this->receipt;
    }
}
