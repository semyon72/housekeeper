<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Scale
 *
 * @ORM\Table(name="scale", uniqueConstraints={@ORM\UniqueConstraint(name="uix_placeId_serviceId", columns={"place_id", "service_id"})}, indexes={@ORM\Index(name="service_id", columns={"service_id"}), @ORM\Index(name="IDX_EC462584DA6A219", columns={"place_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ScaleRepository")
 */
class Scale
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
     * @var integer
     *
     * @ORM\Column(name="total_scale", type="smallint", nullable=false)
     */
    private $totalScale = '3';

    /**
     * @var integer
     *
     * @ORM\Column(name="value_scale", type="smallint", nullable=false)
     */
    private $valueScale = '2';

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
     * @var \Service
     *
     * @ORM\ManyToOne(targetEntity="Service")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="service_id", referencedColumnName="id")
     * })
     */
    private $service;



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
     * Set totalScale
     *
     * @param integer $totalScale
     *
     * @return Scale
     */
    public function setTotalScale($totalScale)
    {
        $this->totalScale = $totalScale;

        return $this;
    }

    /**
     * Get totalScale
     *
     * @return integer
     */
    public function getTotalScale()
    {
        return $this->totalScale;
    }

    /**
     * Set valueScale
     *
     * @param integer $valueScale
     *
     * @return Scale
     */
    public function setValueScale($valueScale)
    {
        $this->valueScale = $valueScale;

        return $this;
    }

    /**
     * Get valueScale
     *
     * @return integer
     */
    public function getValueScale()
    {
        return $this->valueScale;
    }

    /**
     * Set place
     *
     * @param \AppBundle\Entity\Place $place
     *
     * @return Scale
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

    /**
     * Set service
     *
     * @param \AppBundle\Entity\Service $service
     *
     * @return Scale
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
}
