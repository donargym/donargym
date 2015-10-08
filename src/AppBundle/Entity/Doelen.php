<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="doelen")
 */
class Doelen
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(length=156)
     */
    protected $naam;

    /**
     * @ORM\Column(length=156)
     */
    protected $toestel;

    /**
     * @ORM\Column(length=156, nullable=TRUE)
     */
    protected $trede;

    /**
     * @ORM\Column(length=512, nullable=TRUE)
     */
    protected $subdoelen;

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
     * Set naam
     *
     * @param string $naam
     * @return Doelen
     */
    public function setNaam($naam)
    {
        $this->naam = $naam;

        return $this;
    }

    /**
     * Get naam
     *
     * @return string 
     */
    public function getNaam()
    {
        return $this->naam;
    }

    /**
     * Set toestel
     *
     * @param string $toestel
     * @return Doelen
     */
    public function setToestel($toestel)
    {
        $this->toestel = $toestel;

        return $this;
    }

    /**
     * Get toestel
     *
     * @return string 
     */
    public function getToestel()
    {
        return $this->toestel;
    }

    /**
     * @param $subdoelen
     * @return $this
     */
    public function setSubdoelen($subdoelen)
    {
        $this->subdoelen = $subdoelen;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSubdoelen()
    {
        return $this->subdoelen;
    }

    /**
     * @param $trede
     * @return $this
     */
    public function setTrede($trede)
    {
        $this->trede = $trede;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTrede()
    {
        return $this->trede;
    }
}
