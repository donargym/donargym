<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="vakanties")
 */
class Vakanties
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(length=300)
     */
    protected $naam;

    /**
     * @ORM\Column(type="date")
     */
    protected $van;

    /**
     * @ORM\Column(type="date")
     */
    protected $tot;

    public function getAll()
    {
        $items = new \stdClass();
        $items->id = $this->id;
        $items->naam = $this->naam;
        $items->van = $this->van->format('d-m-Y');
        $items->tot = $this->tot->format('d-m-Y');
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
     * Set naam
     *
     * @param string $naam
     * @return Vakanties
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
     * Set van
     *
     * @param \DateTime $van
     * @return Vakanties
     */
    public function setVan($van)
    {
        $this->van = $van;

        return $this;
    }

    /**
     * Get van
     *
     * @return \DateTime 
     */
    public function getVan()
    {
        if($this->van)
        {
            return $this->van;
        }
        else
        {
            return null;
        }
    }

    /**
     * Set tot
     *
     * @param \DateTime $tot
     * @return Vakanties
     */
    public function setTot($tot)
    {
        $this->tot = $tot;

        return $this;
    }

    /**
     * Get tot
     *
     * @return \DateTime 
     */
    public function getTot()
    {
        return $this->tot;
    }
}
