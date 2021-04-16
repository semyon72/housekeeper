<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PaymentInfo
 *
 * @ORM\Table(name="payment_info", indexes={@ORM\Index(name="ix_user_id", columns={"user_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PaymentInfoRepository")
 */
class PaymentInfo
{
    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=128, nullable=false)
     */
    private $firstName = '';

    /**
     * @var string
     *
     * @ORM\Column(name="second_name", type="string", length=128, nullable=false)
     */
    private $secondName = '';

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=128, nullable=false)
     */
    private $lastName = '';

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=29, nullable=false)
     */
    private $code = '';

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=2, nullable=false)
     */
    private $country = '';

    /**
     * @var string
     *
     * @ORM\Column(name="region", type="string", length=48, nullable=false)
     */
    private $region = '';

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=48, nullable=false)
     */
    private $city = '';

    /**
     * @var string
     *
     * @ORM\Column(name="street", type="string", length=128, nullable=false)
     */
    private $street = '';

    /**
     * @var string
     *
     * @ORM\Column(name="house", type="string", length=96, nullable=false)
     */
    private $house = '';

    /**
     * @var string
     *
     * @ORM\Column(name="apartment", type="string", length=96, nullable=false)
     */
    private $apartment = '';

    /**
     * @var string
     *
     * @ORM\Column(name="iban", type="string", length=29, nullable=false)
     */
    private $iban;

    /**
     * @var string
     *
     * @ORM\Column(name="sub_iban", type="string", length=29, nullable=false)
     */
    private $subIban = '';

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="string", length=384, nullable=false)
     */
    private $note = '';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_c", type="datetime", nullable=false)
     */
    private $dateC = null;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_u", type="datetime", nullable=false)
     */
    private $dateU = null;
    
    /**
     * @var \integer Smallint in mySQL - 0 to  
     *
     * @ORM\Column(name="priority", type="smallint", nullable=false )
     */
    private $priority = 0;
    

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\User
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    
    /**
     * @var \AppBundle\Entity\User
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Place")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="place_id", referencedColumnName="id")
     * })
     */
    private $place;
   
    
    //Because each 3 bytes of plain text will transform to 4 bytes of Base64 coded
    //text we will spend 1/3 volume that is why each 64 characters in database
    //must be validated as string that has 48 charackers max at the moment of 
    //insertion into database.
    private $cypher = ['openssl' => "aes-128-cbc", ];
    
    
    /**
     * 
     */
    public function __construct() {
        
        
        $curDate = new \DateTime();
        $this->setDateC($curDate)->setDateU(clone $curDate);
    }
    
    /**
     * It returns Closure that has two parameters $ciphertext decryption $key and will return
     * decrypted $ciphertext.
     *    
     * @return Closure Closure that has two parameters $ciphertext and decryption $key 
     */
    protected function getDecrypter(){
        
        if ( function_exists('openssl_decrypt')){
            $cipher = $this->cypher['openssl'];
            return function ($ciphertext, $key) use ( $cipher ) {
                $ivlen = openssl_cipher_iv_length($cipher);
                return substr(@openssl_decrypt($ciphertext, $cipher, $key, 0),$ivlen);
            };
        }
        
        function($plaintext, $key){
            return $plaintext;
        };        
        
    }
    
    /**
     * It returns Closure that has two parameters $plaintext encryption $key and will return
     * encrypted $plaintext.
     *    
     * @return Closure Closure that has two parameters $plaintext and encryption $key 
     */
    protected function getEncrypter(){
        
        if ( function_exists('openssl_encrypt')){
            $cipher = $this->cypher['openssl'];
            return function ($plaintext, $key) use ( $cipher ) {
                $ivlen = openssl_cipher_iv_length($cipher);
                $nonce = openssl_random_pseudo_bytes($ivlen);
                return @openssl_encrypt($nonce.$plaintext, $cipher, $key, 0);
            };
        }
        
        function($plaintext, $key){
            return $plaintext;
        };        
        
    }
    
    /**
     * 
     * @param type $plainText
     * @return string
     */
    protected function encryptString($plainText){
        $result = $plainText;
        if (  $plainText !== '' && !is_null($this->user) ){
            $encrypter = $this->getEncrypter();
            $result = $encrypter($plainText, $this->user->getPui());
        }
        return $result;
    }

    protected function decryptString($cipherText){
        $result = $cipherText;
        if ( $cipherText !== '' && !is_null($this->user) ){
            $decrypter = $this->getDecrypter();
            $result = $decrypter($cipherText, $this->user->getPui());
        }
        return $result;
    }

    /**
     * Set Priority
     *
     * @param integer $priority
     *
     * @return PaymentInfo
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get Priority
     *
     * @return integer
     */
    public function getPriority()
    {
        return $this->priority;
    }
    
    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return PaymentInfo
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $this->encryptString($firstName);

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->decryptString($this->firstName);
    }

    /**
     * Set secondName
     *
     * @param string $secondName
     *
     * @return PaymentInfo
     */
    public function setSecondName($secondName)
    {
        $this->secondName = $this->encryptString($secondName);

        return $this;
    }

    /**
     * Get secondName
     *
     * @return string
     */
    public function getSecondName()
    {
        return $this->decryptString($this->secondName);
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return PaymentInfo
     */
    public function setLastName($lastName)
    {
        $this->lastName = $this->encryptString($lastName);

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->decryptString($this->lastName);;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return PaymentInfo
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set country
     *
     * @param string $country
     *
     * @return PaymentInfo
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set region
     *
     * @param string $region
     *
     * @return PaymentInfo
     */
    public function setRegion($region)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Get region
     *
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return PaymentInfo
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set street
     *
     * @param string $street
     *
     * @return PaymentInfo
     */
    public function setStreet($street)
    {
        $this->street = $this->encryptString($street);

        return $this;
    }

    /**
     * Get street
     *
     * @return string
     */
    public function getStreet()
    {
        return $this->decryptString($this->street);
    }

    /**
     * Set house
     *
     * @param string $house
     *
     * @return PaymentInfo
     */
    public function setHouse($house)
    {
        $this->house = $this->encryptString($house);

        return $this;
    }

    /**
     * Get house
     *
     * @return string
     */
    public function getHouse()
    {
        return $this->decryptString($this->house);
    }

    /**
     * Set apartment
     *
     * @param string $apartment
     *
     * @return PaymentInfo
     */
    public function setApartment($apartment)
    {
        $this->apartment = $this->encryptString($apartment);

        return $this;
    }

    /**
     * Get apartment
     *
     * @return string
     */
    public function getApartment()
    {
        return $this->decryptString($this->apartment);
    }

    /**
     * Set iban
     *
     * @param string $iban
     *
     * @return PaymentInfo
     */
    public function setIban($iban)
    {
        $this->iban = $iban;

        return $this;
    }

    /**
     * Get iban
     *
     * @return string
     */
    public function getIban()
    {
        return $this->iban;
    }

    /**
     * Set subIban
     *
     * @param string $subIban
     *
     * @return PaymentInfo
     */
    public function setSubIban($subIban)
    {
        $this->subIban = $subIban;

        return $this;
    }

    /**
     * Get subIban
     *
     * @return string
     */
    public function getSubIban()
    {
        return $this->subIban;
    }

    /**
     * Set note
     *
     * @param string $note
     *
     * @return PaymentInfo
     */
    public function setNote($note)
    {
        $this->note = $this->encryptString($note);

        return $this;
    }

    /**
     * Get note
     *
     * @return string
     */
    public function getNote()
    {
        return $this->decryptString($this->note);
    }

    /**
     * Set dateC
     *
     * @param \DateTime $dateC
     *
     * @return PaymentInfo
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
     * @return PaymentInfo
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return PaymentInfo
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
    
    
    /**
     * Set place
     *
     * @param \AppBundle\Entity\Place $place
     *
     * @return PaymentInfo
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
