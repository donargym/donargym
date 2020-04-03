<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="groepen")
 */
class Groepen
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\OneToMany(targetEntity="Functie", mappedBy="groep", cascade={"persist", "remove"}, orphanRemoval=TRUE)
     */
    protected $functies;

    /**
     * @ORM\OneToMany(targetEntity="Wedstrijduitslagen", mappedBy="groep", cascade={"persist", "remove"}, orphanRemoval=TRUE)
     */
    protected $wedstrijduitslagen;

    /**
     * @ORM\OneToMany(targetEntity="Trainingen", mappedBy="groep", cascade={"persist", "remove"}, orphanRemoval=TRUE)
     */
    protected $trainingen;

    /**
     * @ORM\OneToMany(targetEntity="Wedstrijdkalender", mappedBy="groep", cascade={"persist", "remove"}, orphanRemoval=TRUE)
     */
    private $wedstrijdkalender;

    public function __construct()
    {
        $this->functies = new ArrayCollection();
        $this->wedstrijduitslagen = new ArrayCollection();
        $this->trainingen = new ArrayCollection();
        $this->wedstrijdkalender = new ArrayCollection();
    }

    public function getIdName()
    {
        $groep = new \stdClass();
        $groep->id = $this->id;
        $groep->name = $this->name;
        return $groep;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getFuncties()
    {
        return $this->functies->toArray();
    }

    public function addFunctie(Functie $functie)
    {
        if (!$this->functies->contains($functie)) {
            $this->functies->add($functie);
            $functie->setGroep($this);
        }

        return $this;
    }

    public function removeFunctie(Functie $functie)
    {
        if ($this->functies->contains($functie)) {
            $this->functies->removeElement($functie);
            $functie->setGroep(null);
        }

        return $this;
    }

    public function getPeople()
    {
        return array_map(
            function ($functie) {
                return $functie->getPersoon();
            },
            $this->functies->toArray()
        );
    }

    /**
     * Remove functies
     *
     * @param \App\Entity\Functie $functies
     */
    public function removeFuncty(\App\Entity\Functie $functies)
    {
        $this->functies->removeElement($functies);
    }

    /**
     * Add wedstrijduitslagen
     *
     * @param \App\Entity\Wedstrijduitslagen $wedstrijduitslagen
     * @return Groepen
     */
    public function addWedstrijduitslagen(\App\Entity\Wedstrijduitslagen $wedstrijduitslagen)
    {
        $this->wedstrijduitslagen[] = $wedstrijduitslagen;

        return $this;
    }

    /**
     * Remove wedstrijduitslagen
     *
     * @param \App\Entity\Wedstrijduitslagen $wedstrijduitslagen
     */
    public function removeWedstrijduitslagen(\App\Entity\Wedstrijduitslagen $wedstrijduitslagen)
    {
        $this->wedstrijduitslagen->removeElement($wedstrijduitslagen);
    }

    /**
     * Get wedstrijduitslagen
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getWedstrijduitslagen()
    {
        return $this->wedstrijduitslagen;
    }

    /**
     * Add trainingen
     *
     * @param \App\Entity\Trainingen $trainingen
     * @return Groepen
     */
    public function addTrainingen(\App\Entity\Trainingen $trainingen)
    {
        $this->trainingen[] = $trainingen;

        return $this;
    }

    /**
     * Remove trainingen
     *
     * @param \App\Entity\Trainingen $trainingen
     */
    public function removeTrainingen(\App\Entity\Trainingen $trainingen)
    {
        $this->trainingen->removeElement($trainingen);
    }

    /**
     * Get trainingen
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTrainingen()
    {
        return $this->trainingen;
    }

    /**
     * Add functies
     *
     * @param \App\Entity\Functie $functies
     * @return Groepen
     */
    public function addFuncty(\App\Entity\Functie $functies)
    {
        $this->functies[] = $functies;

        return $this;
    }

    /**
     * Add wedstrijdkalender
     *
     * @param \App\Entity\Wedstrijdkalender $wedstrijdkalender
     * @return Groepen
     */
    public function addWedstrijdkalender(\App\Entity\Wedstrijdkalender $wedstrijdkalender)
    {
        $this->wedstrijdkalender[] = $wedstrijdkalender;

        return $this;
    }

    /**
     * Remove wedstrijdkalender
     *
     * @param \App\Entity\Wedstrijdkalender $wedstrijdkalender
     */
    public function removeWedstrijdkalender(\App\Entity\Wedstrijdkalender $wedstrijdkalender)
    {
        $this->wedstrijdkalender->removeElement($wedstrijdkalender);
    }

    /**
     * Get wedstrijdkalender
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getWedstrijdkalender()
    {
        return $this->wedstrijdkalender;
    }
}
