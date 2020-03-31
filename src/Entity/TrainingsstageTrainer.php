<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="trainingsstage_trainer")
 */
final class TrainingsstageTrainer
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
     * @var string
     *
     * @ORM\Column(length=300, name="phone2")
     *
     * @Assert\NotBlank(message = "Dit veld is verplicht")
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
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true, name="diet")
     */
    private $diet;

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

    public function getDiet()
    {
        return $this->diet;
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

    public function setDiet($diet)
    {
        $this->diet = $diet;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }
}
