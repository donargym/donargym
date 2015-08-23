<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="afmeldingen")
 */
class Afmeldingen
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $datum;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $bericht;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $turnster;


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
     * @return Afmeldingen
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
     * Set bericht
     *
     * @param string $bericht
     * @return Afmeldingen
     */
    public function setBericht($bericht)
    {
        $this->bericht = $bericht;

        return $this;
    }

    /**
     * Get bericht
     *
     * @return string 
     */
    public function getBericht()
    {
        return $this->bericht;
    }

    /**
     * Set turnster
     *
     * @param string $turnster
     * @return Afmeldingen
     */
    public function setTurnster($turnster)
    {
        $this->turnster = $turnster;

        return $this;
    }

    /**
     * Get turnster
     *
     * @return string 
     */
    public function getTurnster()
    {
        return $this->turnster;
    }
}
