<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="aanwezigheid")
 */
class Aanwezigheid
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Persoon", inversedBy="aanwezigheid")
     * @ORM\JoinColumn(name="persoon_id", referencedColumnName="id", nullable=FALSE)
     */
    protected $persoon;

    /**
     * @ORM\ManyToOne(targetEntity="Trainingsdata", inversedBy="aanwezigheid")
     * @ORM\JoinColumn(name="trainingdata_id", referencedColumnName="id", nullable=FALSE)
     */
    protected $trainingsdata;

    /**
     * @ORM\Column(length=1, nullable=true)
     */
    protected $aanwezig;

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
     * Set aanwezig
     *
     * @param string $aanwezig
     * @return Aanwezigheid
     */
    public function setAanwezig($aanwezig)
    {
        $this->aanwezig = $aanwezig;

        return $this;
    }

    /**
     * Get aanwezig
     *
     * @return string 
     */
    public function getAanwezig()
    {
        return $this->aanwezig;
    }

    /**
     * Set persoon
     *
     * @param \AppBundle\Entity\Persoon $persoon
     * @return Aanwezigheid
     */
    public function setPersoon(\AppBundle\Entity\Persoon $persoon)
    {
        $this->persoon = $persoon;

        return $this;
    }

    /**
     * Get persoon
     *
     * @return \AppBundle\Entity\Persoon 
     */
    public function getPersoon()
    {
        return $this->persoon;
    }


    /**
     * Set trainingsdata
     *
     * @param \AppBundle\Entity\Trainingsdata $trainingsdata
     * @return Aanwezigheid
     */
    public function setTrainingsdata(\AppBundle\Entity\Trainingsdata $trainingsdata)
    {
        $this->trainingsdata = $trainingsdata;

        return $this;
    }

    /**
     * Get trainingsdata
     *
     * @return \AppBundle\Entity\Trainingsdata 
     */
    public function getTrainingsdata()
    {
        return $this->trainingsdata;
    }
}
