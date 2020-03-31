<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InschrijvingRepository")
 * @ORM\Table(name="inschrijvingen")
 */
final class Inschrijving
{
    public static function trainerOptions()
    {
        return array(
            'Mijn leiding staat er niet bij' => 'webmaster@donargym.nl',
            'Anchella'                       => 'jufanchella@donargym.nl',
            'Cindy'                          => 'jufcindy@donargym.nl',
            'Demi'                           => 'jufdemi@donargym.nl',
            'Eric'                           => 'Ericcastens@donargym.nl',
            'Ilse'                           => 'jufilse@donargym.nl',
            'Loes'                           => 'loestrompet@hotmail.com',
            'Martine'                        => 'jufmartine@donargym.nl',
            'Merel'                          => 'jufmerel@donargym.nl',
            'Rachel'                         => 'jufrachel@donargym.nl',
            'Renske'                         => 'jufrenske@donargym.nl',
            'Vera'                           => 'Verawessels@donargym.nl',
            'Charon'                         => 'jufcharon@donargym.nl',
        );
    }

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(length=300)
     *
     * @Assert\NotBlank(message = "Dit veld is verplicht")
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(length=300)
     *
     * @Assert\NotBlank(message = "Dit veld is verplicht")
     */
    private $lastname;

    /**
     * @var string
     *
     * @ORM\Column(length=300)
     *
     * @Assert\NotBlank(message = "Dit veld is verplicht")
     */
    private $nameletters;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(type="datetime")
     *
     * @Assert\NotBlank(message = "Dit veld is verplicht")
     * @Assert\Type(type = "\DateTime", message="Geen geldige datum")
     */
    private $dateofbirth;

    /**
     * @var string
     *
     * @ORM\Column(length=300)
     *
     * @Assert\NotBlank(message = "Dit veld is verplicht")
     */
    private $gender;

    /**
     * @var string
     *
     * @ORM\Column(length=300)
     *
     * @Assert\NotBlank(message = "Dit veld is verplicht")
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(length=300)
     *
     * @Assert\NotBlank(message = "Dit veld is verplicht")
     */
    private $postcode;

    /**
     * @var string
     *
     * @ORM\Column(length=300)
     *
     * @Assert\NotBlank(message = "Dit veld is verplicht")
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(length=300)
     *
     * @Assert\NotBlank(message = "Dit veld is verplicht")
     */
    private $phone1;

    /**
     * @var string
     *
     * @ORM\Column(length=300, nullable=true)
     */
    private $phone2 = null;

    /**
     * @var string
     *
     * @ORM\Column(length=300)
     *
     * @Assert\NotBlank(message = "Dit veld is verplicht")
     * @Assert\Iban(message = "Dit is geen geldige IBAN")
     */
    private $bankaccountnumber;

    /**
     * @var string
     *
     * @ORM\Column(length=300)
     *
     * @Assert\NotBlank(message = "Dit veld is verplicht")
     */
    private $bankaccountholder;

    /**
     * @var string
     *
     * @ORM\Column(length=300)
     *
     * @Assert\NotBlank(message = "Dit veld is verplicht")
     * @Assert\Email(message = "Geen geldig email adres")
     */
    private $emailaddress;

    /**
     * @var string
     *
     * @ORM\Column(length=300)
     *
     * @Assert\NotBlank(message = "Dit veld is verplicht")
     */
    private $havebeensubscribed;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @Assert\Type(type = "\DateTime", message="Geen geldige datum")
     */
    private $subscribedfrom = null;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @Assert\Type(type = "\DateTime", message="Geen geldige datum")
     */
    private $subscribeduntil = null;

    /**
     * @var string
     *
     * @ORM\Column(length=300)
     *
     * @Assert\NotBlank(message = "Dit veld is verplicht")
     */
    private $otherclub;

    /**
     * @var string
     *
     * @ORM\Column(length=300, nullable=true)
     */
    private $whatotherclub = '';

    /**
     * @var string
     *
     * @ORM\Column(length=300)
     *
     * @Assert\NotBlank(message = "Dit veld is verplicht")
     */
    private $bondscontributiebetaald;

    /**
     * @var string
     *
     * @ORM\Column(length=300)
     *
     * @Assert\NotBlank(message = "Dit veld is verplicht")
     */
    private $category;

    /**
     * @var array
     *
     * @ORM\Column(type="array")
     *
     * @Assert\NotBlank(message = "Dit veld is verplicht")
     */
    private $days;

    /**
     * @var array
     *
     * @ORM\Column(type="array")
     *
     * @Assert\NotBlank(message = "Dit veld is verplicht")
     */
    private $locations;

    /**
     * @var string
     *
     * @ORM\Column(length=300)
     *
     * @Assert\NotBlank(message = "Dit veld is verplicht")
     */
    private $starttime;

    /**
     * @var string
     *
     * @ORM\Column(length=300)
     *
     * @Assert\NotBlank(message = "Dit veld is verplicht")
     */
    private $trainer;

    /**
     * @var string
     *
     * @ORM\Column(length=300, nullable=true)
     */
    private $how = '';

    /**
     * @var string
     *
     * @ORM\Column(length=300)
     */
    private $vrijwilligerstaken;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     *
     * @Assert\IsTrue()
     */
    private $accept;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $acceptPrivacyPolicy;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $acceptNamePublished;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $acceptPicturesPublished;

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     * @return string
     */
    public function getNameletters()
    {
        return $this->nameletters;
    }

    /**
     * @param string $nameletters
     */
    public function setNameletters($nameletters)
    {
        $this->nameletters = $nameletters;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getDateofbirth()
    {
        return $this->dateofbirth;
    }

    /**
     * @param \DateTimeImmutable $dateofbirth
     */
    public function setDateofbirth($dateofbirth)
    {
        $this->dateofbirth = $dateofbirth;
    }

    /**
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param string $gender
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * @param string $postcode
     */
    public function setPostcode($postcode)
    {
        $this->postcode = $postcode;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getPhone1()
    {
        return $this->phone1;
    }

    /**
     * @param string $phone1
     */
    public function setPhone1($phone1)
    {
        $this->phone1 = $phone1;
    }

    /**
     * @return string
     */
    public function getPhone2()
    {
        return $this->phone2;
    }

    /**
     * @param string $phone2
     */
    public function setPhone2($phone2)
    {
        $this->phone2 = $phone2;
    }

    /**
     * @return string
     */
    public function getBankaccountnumber()
    {
        return $this->bankaccountnumber;
    }

    /**
     * @param string $bankaccountnumber
     */
    public function setBankaccountnumber($bankaccountnumber)
    {
        $this->bankaccountnumber = $bankaccountnumber;
    }

    /**
     * @return string
     */
    public function getBankaccountholder()
    {
        return $this->bankaccountholder;
    }

    /**
     * @param string $bankaccountholder
     */
    public function setBankaccountholder($bankaccountholder)
    {
        $this->bankaccountholder = $bankaccountholder;
    }

    /**
     * @return string
     */
    public function getEmailaddress()
    {
        return $this->emailaddress;
    }

    /**
     * @param string $emailaddress
     */
    public function setEmailaddress($emailaddress)
    {
        $this->emailaddress = $emailaddress;
    }

    /**
     * @return string
     */
    public function isHavebeensubscribed()
    {
        return $this->havebeensubscribed;
    }

    /**
     * @param string $havebeensubscribed
     */
    public function setHavebeensubscribed($havebeensubscribed)
    {
        $this->havebeensubscribed = $havebeensubscribed;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getSubscribedfrom()
    {
        return $this->subscribedfrom;
    }

    /**
     * @param \DateTimeImmutable $subscribedfrom
     */
    public function setSubscribedfrom($subscribedfrom)
    {
        $this->subscribedfrom = $subscribedfrom;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getSubscribeduntil()
    {
        return $this->subscribeduntil;
    }

    /**
     * @param \DateTimeImmutable $subscribeduntil
     */
    public function setSubscribeduntil($subscribeduntil)
    {
        $this->subscribeduntil = $subscribeduntil;
    }

    /**
     * @return string
     */
    public function isOtherclub()
    {
        return $this->otherclub;
    }

    /**
     * @param string $otherclub
     */
    public function setOtherclub($otherclub)
    {
        $this->otherclub = $otherclub;
    }

    /**
     * @return string
     */
    public function getWhatotherclub()
    {
        return $this->whatotherclub;
    }

    /**
     * @param string $whatotherclub
     */
    public function setWhatotherclub($whatotherclub)
    {
        $this->whatotherclub = $whatotherclub;
    }

    /**
     * @return string
     */
    public function isBondscontributiebetaald()
    {
        return $this->bondscontributiebetaald;
    }

    /**
     * @param string $bondscontributiebetaald
     */
    public function setBondscontributiebetaald($bondscontributiebetaald)
    {
        $this->bondscontributiebetaald = $bondscontributiebetaald;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param string $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return array
     */
    public function getDays()
    {
        return $this->days;
    }

    /**
     * @param array $days
     */
    public function setDays($days)
    {
        $this->days = $days;
    }

    /**
     * @return array
     */
    public function getLocations()
    {
        return $this->locations;
    }

    /**
     * @param array $locations
     */
    public function setLocations($locations)
    {
        $this->locations = $locations;
    }

    /**
     * @return string
     */
    public function getStarttime()
    {
        return $this->starttime;
    }

    /**
     * @param \DateTime $starttime
     */
    public function setStarttime($starttime)
    {
        $this->starttime = $starttime->format('H:i');
    }

    /**
     * @return string
     */
    public function getTrainer()
    {
        return $this->trainer;
    }

    /**
     * @param string $trainer
     */
    public function setTrainer($trainer)
    {
        $this->trainer = $trainer;
    }

    /**
     * @return string
     */
    public function getHow()
    {
        return $this->how;
    }

    /**
     * @param string $how
     */
    public function setHow($how)
    {
        $this->how = $how;
    }

    /**
     * @return bool
     */
    public function isAccept()
    {
        return $this->accept;
    }

    /**
     * @param bool $accept
     */
    public function setAccept($accept)
    {
        $this->accept = $accept;
    }

    /**
     * @return bool
     */
    public function isAcceptPrivacyPolicy()
    {
        return $this->acceptPrivacyPolicy;
    }

    /**
     * @param bool $acceptPrivacyPolicy
     */
    public function setAcceptPrivacyPolicy($acceptPrivacyPolicy)
    {
        $this->acceptPrivacyPolicy = $acceptPrivacyPolicy;
    }

    /**
     * @return bool
     */
    public function isAcceptNamePublished()
    {
        return $this->acceptNamePublished;
    }

    /**
     * @param bool $acceptNamePublished
     */
    public function setAcceptNamePublished($acceptNamePublished)
    {
        $this->acceptNamePublished = $acceptNamePublished;
    }

    /**
     * @return bool
     */
    public function isAcceptPicturesPublished()
    {
        return $this->acceptPicturesPublished;
    }

    /**
     * @param bool $acceptPicturesPublished
     */
    public function setAcceptPicturesPublished($acceptPicturesPublished)
    {
        $this->acceptPicturesPublished = $acceptPicturesPublished;
    }

    /**
     * @return string
     */
    public function getVrijwilligerstaken()
    {
        return $this->vrijwilligerstaken;
    }

    /**
     * @param string $vrijwilligerstaken
     */
    public function setVrijwilligerstaken($vrijwilligerstaken)
    {
        $this->vrijwilligerstaken = $vrijwilligerstaken;
    }
}
