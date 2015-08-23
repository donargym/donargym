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
     * @ORM\OneToOne(targetEntity="DoelenOpbouw")
     * @ORM\JoinColumn(name="opbouw_id", referencedColumnName="id")
     **/
    protected $opbouw;


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
     * Set opbouw
     *
     * @param \AppBundle\Entity\DoelenOpbouw $opbouw
     * @return Doelen
     */
    public function setOpbouw(\AppBundle\Entity\DoelenOpbouw $opbouw = null)
    {
        $this->opbouw = $opbouw;

        return $this;
    }

    /**
     * Get opbouw
     *
     * @return \AppBundle\Entity\DoelenOpbouw 
     */
    public function getOpbouw()
    {
        return $this->opbouw;
    }
}
