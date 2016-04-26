<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="stukje")
 */
class Stukje
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $toestelleuk;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $omdattoestelleuk;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $wedstrijd;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $element;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $leren;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $voorbeeld;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $overig;

    public function getAll()
    {
        $stukje = new \stdClass();
        $stukje->id = $this->id;
        $stukje->toestelleuk = $this->toestelleuk;
        $stukje->omdattoestelleuk = $this->omdattoestelleuk;
        $stukje->wedstrijd = $this->wedstrijd;
        $stukje->element = $this->element;
        $stukje->leren = $this->leren;
        $stukje->voorbeeld = $this->voorbeeld;
        $stukje->overig = $this->overig;
        return $stukje;
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
     * Set toestelleuk
     *
     * @param string $toestelleuk
     * @return Stukje
     */
    public function setToestelleuk($toestelleuk)
    {
        $this->toestelleuk = $toestelleuk;

        return $this;
    }

    /**
     * Get toestelleuk
     *
     * @return string 
     */
    public function getToestelleuk()
    {
        return $this->toestelleuk;
    }

    /**
     * Set omdattoestelleuk
     *
     * @param string $omdattoestelleuk
     * @return Stukje
     */
    public function setOmdattoestelleuk($omdattoestelleuk)
    {
        $this->omdattoestelleuk = $omdattoestelleuk;

        return $this;
    }

    /**
     * Get omdattoestelleuk
     *
     * @return string 
     */
    public function getOmdattoestelleuk()
    {
        return $this->omdattoestelleuk;
    }

    /**
     * Set wedstrijd
     *
     * @param string $wedstrijd
     * @return Stukje
     */
    public function setWedstrijd($wedstrijd)
    {
        $this->wedstrijd = $wedstrijd;

        return $this;
    }

    /**
     * Get wedstrijd
     *
     * @return string 
     */
    public function getWedstrijd()
    {
        return $this->wedstrijd;
    }

    /**
     * Set element
     *
     * @param string $element
     * @return Stukje
     */
    public function setElement($element)
    {
        $this->element = $element;

        return $this;
    }

    /**
     * Get element
     *
     * @return string 
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * Set leren
     *
     * @param string $leren
     * @return Stukje
     */
    public function setLeren($leren)
    {
        $this->leren = $leren;

        return $this;
    }

    /**
     * Get leren
     *
     * @return string 
     */
    public function getLeren()
    {
        return $this->leren;
    }

    /**
     * Set voorbeeld
     *
     * @param string $voorbeeld
     * @return Stukje
     */
    public function setVoorbeeld($voorbeeld)
    {
        $this->voorbeeld = $voorbeeld;

        return $this;
    }

    /**
     * Get voorbeeld
     *
     * @return string 
     */
    public function getVoorbeeld()
    {
        return $this->voorbeeld;
    }

    /**
     * Set overig
     *
     * @param string $overig
     * @return Stukje
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
}
