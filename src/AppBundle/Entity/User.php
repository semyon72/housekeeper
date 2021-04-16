<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * User
 *
 * @ORM\Table(name="user", uniqueConstraints={@ORM\UniqueConstraint(name="unique_ix_email", columns={"email"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * 
 * @ORM\HasLifecycleCallbacks()
 */
class User implements UserInterface, EquatableInterface
{
    /**
     * @var string
     *
     * @ORM\Column(name="pass", type="string", length=255, nullable=false)
     */
    private $pass = '';

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     */
    private $email = '';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_c", type="datetime", nullable=false)
     */
    private $dateC;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_u", type="datetime", nullable=false)
     */
    private $dateU;

    /**
     * @var string
     *
     * @ORM\Column(name="pui", type="string", length=32, nullable=false)
     */
    private $pui = '';

    /**
     * @var boolean
     *
     * @ORM\Column(name="email_is_confirmed", type="boolean", nullable=false)
     */
    private $emailIsConfirmed = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="one_time_pass", type="string", length=64, nullable=false)
     */
    private $oneTimePass = '';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="otp_date_exp", type="datetime", nullable=true)
     */
    private $otpDateExp;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;


    /**
     * One User has Many places.
     * 
     * @var ArrayCollection ArrayCollection of AppBundle\Entity\Place -s
     *  
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Place")
     * @ORM\JoinTable(name="user_place",
     *  joinColumns = { @ORM\JoinColumn(name="user_id", referencedColumnName="id") },
     *  inverseJoinColumns = { @ORM\JoinColumn(name="place_id", referencedColumnName="id", unique=true) } 
     * )
    */
    private $places;
    
    
    public function __construct() {
        $this->places = new ArrayCollection();
        $curDate = new \DateTime();
        $this->setDateC($curDate)->setDateU(clone $curDate);
    }
    
    /**
     * Returns object ArrayCollection that has method "add" for adding new place
     * 
     * @return ArrayCollection Returns object ArrayCollection that has method "add" for adding new place
     */
    public function getPlaces(){
        return $this->places;
    }
    
    /**
     * Set pass
     *
     * @param string $pass
     *
     * @return User
     */
    public function setPass($pass)
    {
        $this->pass = $pass;

        return $this;
    }

    /**
     * Get pass
     *
     * @return string
     */
    public function getPass()
    {
        return $this->pass;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set dateC
     *
     * @param \DateTime $dateC
     *
     * @return User
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
     * Set dateU
     *
     * @param \DateTime $dateU
     *
     * @return User
     */
    public function setDateU($dateU)
    {
        $this->dateU = $dateU;

        return $this;
    }

    /**
     * Get dateU
     *
     * @return \DateTime
     */
    public function getDateU()
    {
        return $this->dateU;
    }

    
    /**
     * 
     *  private user identifier - md5( hashed(pass)+email+date_c('Y-m-d H:i:s') )
     * 
     *  @ORM\PrePersist
     */
    public function onPuiPrePersist(LifecycleEventArgs $event)
    {
        if ( $this->getId() === null || empty($this->getPui()) ){
            //md5( hashed(pass)+email+date_c('Y-m-d H:i:s')
            $pui = md5( $this->getPass().$this->getEmail().$this->getDateC()->format('Y-m-d H:i:s') );
            $this->setPui( $pui );
        }
    }    
    
    
    /**
     * Set pui
     *
     * @param string $pui
     *
     * @return User
     */
    public function setPui($pui)
    {
        $this->pui = $pui;

        return $this;
    }

    /**
     * Get pui
     *
     * @return string
     */
    public function getPui()
    {
        return $this->pui;
    }

    /**
     * Set emailIsConfirmed
     *
     * @param boolean $emailIsConfirmed
     *
     * @return User
     */
    public function setEmailIsConfirmed($emailIsConfirmed)
    {
        $this->emailIsConfirmed = $emailIsConfirmed;

        return $this;
    }

    /**
     * Get emailIsConfirmed
     *
     * @return boolean
     */
    public function getEmailIsConfirmed()
    {
        return $this->emailIsConfirmed;
    }
    
    /**
     * Set oneTimePass
     *
     * @param string $oneTimePass
     *
     * @return User
     */
    public function setOneTimePass($oneTimePass)
    {
        $this->oneTimePass = $oneTimePass;
        return $this;
    }

    /**
     * Get oneTimePass
     *
     * @return string
     */
    public function getOneTimePass()
    {
        return $this->oneTimePass;
    }

    /**
     * Set otpDateExp
     *
     * @param \DateTime $otpDateExp
     *
     * @return User
     */
    public function setOtpDateExp($otpDateExp)
    {
        $this->otpDateExp = $otpDateExp;

        return $this;
    }

    /**
     * Get otpDateExp
     *
     * @return \DateTime
     */
    public function getOtpDateExp()
    {
        return $this->otpDateExp;
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
     * Stub for form appropriate behavior
     *
     * @return self
     */
    public function setId()
    {
        return $this;
    }

    
    public function eraseCredentials() {
        return null;
    }

    public function getPassword() {
        return $this->getPass();
    }

    public function getRoles() {
        return ['ROLE_USER'];
    }

    public function getSalt() {
        //Very important - don't change this value because you will need to regenerate
        //all passwords in column `pass` and `pui`
        return "(*YejdnSdjwKB Sjyb)29@883L!fn8c\r\n3 dmnsx0q8e9w";
    }

    public function getUsername() {
        return $this->getEmail();
    }

    public function isEqualTo(UserInterface $user) {
        
        if ( get_class($user) !== self::class ) {
            return false;
        }
        
        if ( $this->getId() === $user->getId() ){
            return true;
        }

        if ($this->getPassword() !== $user->getPassword()) {
            return false;
        }

        if ($this->getSalt() !== $user->getSalt()) {
            return false;
        }

        if ($this->getUsername() !== $user->getUsername()) {
            return false;
        }

        return true;
    }

}
