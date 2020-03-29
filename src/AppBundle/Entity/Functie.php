<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="functie")
 */
class Functie
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Persoon", inversedBy="functie")
     * @ORM\JoinColumn(name="persoon_id", referencedColumnName="id", nullable=FALSE)
     * @ORM\OrderBy({"geboortedatum" = "ASC"})
     */
    protected $persoon;

    /**
     * @ORM\ManyToOne(targetEntity="Groepen", inversedBy="functie")
     * @ORM\JoinColumn(name="groepen_id", referencedColumnName="id", nullable=FALSE)
     */
    protected $groep;

    /**
     * @ORM\Column(name="functie", type="string", length=255))
     */
    protected $functie;

    public function getId()
    {
        return $this->id;
    }

    public function getPersoon()
    {
        return $this->persoon;
    }

    public function setPersoon(Persoon $persoon = null)
    {
        $this->persoon = $persoon;
        return $this;
    }

    public function getGroep()
    {
        return $this->groep;
    }

    public function setGroep(Groepen $groep = null)
    {
        $this->groep = $groep;
        return $this;
    }

    /**
     * Set functie
     *
     * @param string $functie
     * @return Functie
     */
    public function setFunctie($functie)
    {
        $this->functie = $functie;

        return $this;
    }

    /**
     * Get functie
     *
     * @return string 
     */
    public function getFunctie()
    {
        return $this->functie;
    }
}
