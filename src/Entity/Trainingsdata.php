<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="trainingsdata")
 */
class Trainingsdata
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="Aanwezigheid", mappedBy="trainingsdata", cascade={"persist", "remove"}, orphanRemoval=TRUE)
     */
    private $aanwezigheid;

    /**
     * @ORM\ManyToOne(targetEntity="Trainingen", inversedBy="trainingsdata")
     * @ORM\JoinColumn(name="trainingdata_id", referencedColumnName="id", nullable=FALSE)
     */
    protected $trainingen;

    /**
     * @ORM\Column(type="date")
     */
    protected $lesdatum;

//    /**
//     * @ORM\OneToOne(targetEntity="Voorbereiding")
//     * @ORM\JoinColumn(name="voorbereiding_id", referencedColumnName="id")
//     **/
//    protected $voorbereiding;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->aanwezigheid = new ArrayCollection();
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
     * Set lesdatum
     *
     * @param \DateTime $lesdatum
     * @return Trainingsdata
     */
    public function setLesdatum($lesdatum)
    {
        $this->lesdatum = $lesdatum;

        return $this;
    }

    /**
     * Get lesdatum
     *
     * @return \DateTime 
     */
    public function getLesdatum()
    {
        return $this->lesdatum;
    }

    /**
     * Add aanwezigheid
     *
     * @param \App\Entity\Aanwezigheid $aanwezigheid
     * @return Trainingsdata
     */
    public function addAanwezigheid(\App\Entity\Aanwezigheid $aanwezigheid)
    {
        $this->aanwezigheid[] = $aanwezigheid;

        return $this;
    }

    /**
     * Remove aanwezigheid
     *
     * @param \App\Entity\Aanwezigheid $aanwezigheid
     */
    public function removeAanwezigheid(\App\Entity\Aanwezigheid $aanwezigheid)
    {
        $this->aanwezigheid->removeElement($aanwezigheid);
    }

    /**
     * Get aanwezigheid
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAanwezigheid()
    {
        return $this->aanwezigheid;
    }

    /**
     * Set trainingen
     *
     * @param \App\Entity\Trainingen $trainingen
     * @return Trainingsdata
     */
    public function setTrainingen(\App\Entity\Trainingen $trainingen)
    {
        $this->trainingen = $trainingen;

        return $this;
    }

    /**
     * Get trainingen
     *
     * @return \App\Entity\Trainingen
     */
    public function getTrainingen()
    {
        return $this->trainingen;
    }
}
