<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PlaceService
 *
 * @ORM\Table(name="place_service", uniqueConstraints={@ORM\UniqueConstraint(name="uix_placeId_serviceId", columns={"place_id", "service_id"})}, indexes={@ORM\Index(name="service_id", columns={"service_id"}), @ORM\Index(name="payment_info", columns={"payment_info"}), @ORM\Index(name="IDX_D5518D29DA6A219", columns={"place_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PlaceServiceRepository")
 */
class PlaceService
{
    /**
     * @var \PaymentInfo
     *
     * @ORM\ManyToOne(targetEntity="PaymentInfo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="payment_info", referencedColumnName="id")
     * })
     */
    private $paymentInfo;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Service
     *
     * @ORM\ManyToOne(targetEntity="Service")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="service_id", referencedColumnName="id")
     * })
     */
    private $service;

    /**
     * @var \Place
     *
     * @ORM\ManyToOne(targetEntity="Place")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="place_id", referencedColumnName="id")
     * })
     */
    private $place;



    /**
     * Set paymentInfo
     *
     * @param \AppBundle\Entity\PaymentInfo $paymentInfo
     *
     * @return PlaceService
     */
    public function setPaymentInfo(\AppBundle\Entity\PaymentInfo $paymentInfo = null)
    {
        $this->paymentInfo = $paymentInfo;

        return $this;
    }

    /**
     * Get paymentInfo
     *
     * @return \AppBundle\Entity\PaymentInfo
     */
    public function getPaymentInfo()
    {
        return $this->paymentInfo;
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
     * @return PlaceService
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
     * @return PlaceService
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
