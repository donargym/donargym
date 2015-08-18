<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="persoon")
 */
class Persoon
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="voornaam", type="string", length=255)
     */
    private $voornaam;

    /**
     * @var string
     *
     * @ORM\Column(name="achternaam", type="string", length=255)
     */
    private $achternaam;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="geboortedatum", type="date")
     */
    private $geboortedatum;

    /**
     * @var string
     *
     * @ORM\Column(name="foto", type="string", length=255)
     */
    private $foto;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="Persoon")
     * @ORM\JoinColumn(name="User_id", referencedColumnName="id")
     **/
    private $user;

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
     * Set voornaam
     *
     * @param string $voornaam
     * @return Persoon
     */
    public function setVoornaam($voornaam)
    {
        $this->voornaam = $voornaam;

        return $this;
    }

    /**
     * Get voornaam
     *
     * @return string 
     */
    public function getVoornaam()
    {
        return $this->voornaam;
    }

    /**
     * Set achternaam
     *
     * @param string $achternaam
     * @return Persoon
     */
    public function setAchternaam($achternaam)
    {
        $this->achternaam = $achternaam;

        return $this;
    }

    /**
     * Get achternaam
     *
     * @return string 
     */
    public function getAchternaam()
    {
        return $this->achternaam;
    }

    /**
     * Set geboortedatum
     *
     * @param \DateTime $geboortedatum
     * @return Persoon
     */
    public function setGeboortedatum($geboortedatum)
    {
        $this->geboortedatum = $geboortedatum;

        return $this;
    }

    /**
     * Get geboortedatum
     *
     * @return \DateTime 
     */
    public function getGeboortedatum()
    {
        return $this->geboortedatum;
    }

    /**
     * Set foto
     *
     * @param string $foto
     * @return Persoon
     */
    public function setFoto($foto)
    {
        $this->foto = $foto;

        return $this;
    }

    /**
     * Get foto
     *
     * @return string 
     */
    public function getFoto()
    {
        return $this->foto;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     * @return Persoon
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
}
