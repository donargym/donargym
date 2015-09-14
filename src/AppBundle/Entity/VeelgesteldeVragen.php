<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="veelgesteldevragen")
 */
class VeelgesteldeVragen
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="text")
     */
    protected $vraag;

    /**
     * @ORM\Column(type="text")
     */
    protected $antwoord;

    public function getAll()
    {
        $items = new \stdClass();
        $items->id = $this->id;
        $items->vraag = $this->vraag;
        $items->antwoord = $this->antwoord;
        return $items;
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
     * Set vraag
     *
     * @param string $vraag
     * @return VeelgesteldeVragen
     */
    public function setVraag($vraag)
    {
        $this->vraag = $vraag;

        return $this;
    }

    /**
     * Get vraag
     *
     * @return string 
     */
    public function getVraag()
    {
        return $this->vraag;
    }

    /**
     * Set antwoord
     *
     * @param string $antwoord
     * @return VeelgesteldeVragen
     */
    public function setAntwoord($antwoord)
    {
        $this->antwoord = $antwoord;

        return $this;
    }

    /**
     * Get antwoord
     *
     * @return string 
     */
    public function getAntwoord()
    {
        return $this->antwoord;
    }
}
