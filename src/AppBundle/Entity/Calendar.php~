<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="calendar")
 */
class Calendar
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
     * @ORM\Column(length=156)
     */
    protected $activiteit;

    /**
     * @ORM\Column(type="text")
     */
    protected $locatie;

    /**
     * @ORM\Column(type="text")
     */
    protected $tijd;

    public function getAll()
    {
        $items = new \stdClass();
        $items->id = $this->id;
        $items->datum = $this->datum->format('d-m-Y');
        $items->activiteit = $this->activiteit;
        $items->locatie = $this->locatie;
        $items->tijd = $this->tijd;
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
     * Set datum
     *
     * @param \DateTime $datum
     * @return Calendar
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
        return $this->datum->format('d-m-Y');
    }

    /**
     * Set activiteit
     *
     * @param string $activiteit
     * @return Calendar
     */
    public function setActiviteit($activiteit)
    {
        $this->activiteit = $activiteit;

        return $this;
    }

    /**
     * Get activiteit
     *
     * @return string 
     */
    public function getActiviteit()
    {
        return $this->activiteit;
    }

    /**
     * Set locatie
     *
     * @param string $locatie
     * @return Calendar
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
     * Set tijd
     *
     * @param string $tijd
     * @return Calendar
     */
    public function setTijd($tijd)
    {
        $this->tijd = $tijd;

        return $this;
    }

    /**
     * Get tijd
     *
     * @return string 
     */
    public function getTijd()
    {
        return $this->tijd;
    }
}
