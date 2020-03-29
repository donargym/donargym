<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="wedstrijdkalender")
 */
class Wedstrijdkalender
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="date")
     */
    protected $datum;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $tijden;

    /**
     * @ORM\Column(length=156)
     */
    protected $wedstrijdnaam;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $locatie;

    /**
     * @ORM\ManyToMany(targetEntity="Persoon", inversedBy="wedstrijdkalender")
     * @ORM\JoinTable(name="personen_wedstrijdkalender")
     * @ORM\OrderBy({"geboortedatum" = "ASC"})
     **/
    protected $persoon;

    /**
     * @ORM\ManyToOne(targetEntity="Groepen", inversedBy="wedstrijdkalender")
     * @ORM\JoinColumn(name="groepen_id", referencedColumnName="id", nullable=FALSE)
     */
    protected $groep;

    public function getAll()
    {
        $items = new \stdClass();
        $items->id = $this->id;
        $items->datum = $this->datum->format("d-m-Y");
        $items->tijden = $this->tijden;
        $items->wedstrijdnaam = $this->wedstrijdnaam;
        $items->locatie = $this->locatie;
        return $items;
    }
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->persoon = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set datum
     *
     * @param \DateTime $datum
     * @return Wedstrijdkalender
     */
    public function setDatum($datum)
    {
        $this->datum = $datum;

        return $this;
    }

    /**
     * Get datum
     *
     * @return \DateTime 
     */
    public function getDatum()
    {
        return $this->datum;
    }

    /**
     * Set tijden
     *
     * @param string $tijden
     * @return Wedstrijdkalender
     */
    public function setTijden($tijden)
    {
        $this->tijden = $tijden;

        return $this;
    }

    /**
     * Get tijden
     *
     * @return string 
     */
    public function getTijden()
    {
        return $this->tijden;
    }

    /**
     * Set wedstrijdnaam
     *
     * @param \156 $wedstrijdnaam
     * @return Wedstrijdkalender
     */
    public function setWedstrijdnaam($wedstrijdnaam)
    {
        $this->wedstrijdnaam = $wedstrijdnaam;

        return $this;
    }

    /**
     * Get wedstrijdnaam
     *
     * @return \156 
     */
    public function getWedstrijdnaam()
    {
        return $this->wedstrijdnaam;
    }

    /**
     * Set locatie
     *
     * @param string $locatie
     * @return Wedstrijdkalender
     */
    public function setLocatie($locatie)
    {
        $this->locatie = $locatie;

        return $this;
    }

    /**
     * Get locatie
     *
     * @return string 
     */
    public function getLocatie()
    {
        return $this->locatie;
    }

    /**
     * Add persoon
     *
     * @param \App\Entity\Persoon $persoon
     * @return Wedstrijdkalender
     */
    public function addPersoon(\App\Entity\Persoon $persoon)
    {
        $this->persoon[] = $persoon;

        return $this;
    }

    /**
     * Remove persoon
     *
     * @param \App\Entity\Persoon $persoon
     */
    public function removePersoon(\App\Entity\Persoon $persoon)
    {
        $this->persoon->removeElement($persoon);
    }

    /**
     * Get persoon
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPersoon()
    {
        return $this->persoon;
    }

    /**
     * Set groep
     *
     * @param \App\Entity\Groepen $groep
     * @return Wedstrijdkalender
     */
    public function setGroep(\App\Entity\Groepen $groep)
    {
        $this->groep = $groep;

        return $this;
    }

    /**
     * Get groep
     *
     * @return \App\Entity\Groepen
     */
    public function getGroep()
    {
        return $this->groep;
    }
}
