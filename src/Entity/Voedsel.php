<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="voedsel")
 */
class Voedsel
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Persoon", inversedBy="voedsel")
     * @ORM\JoinColumn(name="persoon_id", referencedColumnName="id", nullable=FALSE)
     **/
    protected $persoon;

    /**
     * @ORM\Column(name="voedsel", type="string", length=255))
     */
    protected $voedsel;

    /**
     * @ORM\Column(name="hoeveelheid", type="string", length=255))
     */
    protected $hoeveelheid;

    /**
     * @ORM\Column(name="overig", type="string", length=255, nullable=TRUE))
     */
    protected $overig;

    public function getAll()
    {
        $voedsel = new \stdClass();
        $voedsel->id = $this->getId();
        $voedsel->voedsel = $this->getVoedsel();
        $voedsel->hoeveelheid = $this->getHoeveelheid();
        $voedsel->overig = $this->getOverig();
        return $voedsel;
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
     * Set voedsel
     *
     * @param string $voedsel
     * @return Voedsel
     */
    public function setVoedsel($voedsel)
    {
        $this->voedsel = $voedsel;

        return $this;
    }

    /**
     * Get voedsel
     *
     * @return string 
     */
    public function getVoedsel()
    {
        return $this->voedsel;
    }

    /**
     * Set hoeveelheid
     *
     * @param string $hoeveelheid
     * @return Voedsel
     */
    public function setHoeveelheid($hoeveelheid)
    {
        $this->hoeveelheid = $hoeveelheid;

        return $this;
    }

    /**
     * Get hoeveelheid
     *
     * @return string 
     */
    public function getHoeveelheid()
    {
        return $this->hoeveelheid;
    }

    /**
     * Set overig
     *
     * @param string $overig
     * @return Voedsel
     */
    public function setOverig($overig)
    {
        $this->overig = $overig;

        return $this;
    }

    /**
     * Get overig
     *
     * @return string 
     */
    public function getOverig()
    {
        return $this->overig;
    }

    /**
     * Set persoon
     *
     * @param \App\Entity\Persoon $persoon
     * @return Voedsel
     */
    public function setPersoon(\App\Entity\Persoon $persoon)
    {
        $this->persoon = $persoon;

        return $this;
    }

    /**
     * Get persoon
     *
     * @return \App\Entity\Persoon
     */
    public function getPersoon()
    {
        return $this->persoon;
    }
}
