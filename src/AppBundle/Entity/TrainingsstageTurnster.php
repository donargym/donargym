<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\TrainingsstageTurnsterRepository")
 * @ORM\Table(name="trainingsstage")
 */
final class TrainingsstageTurnster
{
    /**
     * @ORM\Column(type="integer", name="id")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(length=300, name="name")
     *
     * @Assert\NotBlank(message = "Dit veld is verplicht")
     */
    private $name;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(type="datetime", name="dateofbirth")
     *
     * @Assert\NotBlank(message = "Dit veld is verplicht")
     * @Assert\Type(type = "\DateTime", message="Geen geldige datum")
     */
    private $dateofbirth;

    /**
     * @var string
     *
     * @ORM\Column(length=300, name="emailaddress")
     *
     * @Assert\NotBlank(message = "Dit veld is verplicht")
     * @Assert\Email(message = "Geen geldig email adres")
     */
    private $emailaddress;

    /**
     * @var string
     *
     * @ORM\Column(length=300, name="phone1")
     *
     * @Assert\NotBlank(message = "Dit veld is verplicht")
     */
    private $phone1;

    /**
     * @var string|null
     *
     * @ORM\Column(length=300, nullable=true, name="phone2")
     */
    private $phone2;

    /**
     * @var string
     *
     * @ORM\Column(length=300, name="insurance_company")
     *
     * @Assert\NotBlank(message = "Dit veld is verplicht")
     */
    private $insuranceCompany;

    /**
     * @ORM\Column(length=300, name="insurance_card")
     *
     * @Assert\NotBlank(message="Dit veld is verplicht")
     * @Assert\File(
     *      maxSize="5M",
     *      mimeTypes = {"image/gif", "image/jpeg", "image/pjpeg", "image/png"},
     *      mimeTypesMessage = "Please upload a valid image: gif, jpg or png"
     *      )
     */
    private $insuranceCard;

    /**
     * @var string
     *
     * @ORM\Column(length=300, name="huis_arts")
     *
     * @Assert\NotBlank(message = "Dit veld is verplicht")
     */
    private $huisArts;

    /**
     * @var string
     *
     * @ORM\Column(length=300, name="bankaccountholder")
     *
     * @Assert\NotBlank(message = "Dit veld is verplicht")
     */
    private $bankaccountholder;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true, name="diet")
     */
    private $diet;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true, name="medicines")
     */
    private $medicines;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true, name="other")
     */
    private $other;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="accept")
     *
     * @Assert\IsTrue()
     */
    private $accept;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="created_at")
     */
    private $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDateofbirth()
    {
        return $this->dateofbirth;
    }

    public function getEmailaddress()
    {
        return $this->emailaddress;
    }

    public function getPhone1()
    {
        return $this->phone1;
    }

    public function getPhone2()
    {
        return $this->phone2;
    }

    public function getInsuranceCompany()
    {
        return $this->insuranceCompany;
    }

    public function getInsuranceCard()
    {
        return $this->insuranceCard;
    }

    public function getHuisArts()
    {
        return $this->huisArts;
    }

    public function getBankaccountholder()
    {
        return $this->bankaccountholder;
    }

    public function getDiet()
    {
        return $this->diet;
    }

    public function getMedicines()
    {
        return $this->medicines;
    }

    public function getOther()
    {
        return $this->other;
    }

    public function getAccept()
    {
        return $this->accept;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setDateofbirth($dateofbirth)
    {
        $this->dateofbirth = $dateofbirth;
    }

    public function setEmailaddress($emailaddress)
    {
        $this->emailaddress = $emailaddress;
    }

    public function setPhone1($phone1)
    {
        $this->phone1 = $phone1;
    }

    public function setPhone2($phone2)
    {
        $this->phone2 = $phone2;
    }

    public function setInsuranceCompany($insuranceCompany)
    {
        $this->insuranceCompany = $insuranceCompany;
    }

    public function setInsuranceCard($insuranceCard)
    {
        $this->insuranceCard = $insuranceCard;
    }

    public function setHuisArts($huisArts)
    {
        $this->huisArts = $huisArts;
    }

    public function setBankaccountholder($bankaccountholder)
    {
        $this->bankaccountholder = $bankaccountholder;
    }

    public function setDiet($diet)
    {
        $this->diet = $diet;
    }

    public function setMedicines($medicines)
    {
        $this->medicines = $medicines;
    }

    public function setOther($other)
    {
        $this->other = $other;
    }

    public function setAccept($accept)
    {
        $this->accept = $accept;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }
}
