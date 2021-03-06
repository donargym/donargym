<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="trainingen")
 */
class Trainingen
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Groepen", inversedBy="trainingen")
     * @ORM\JoinColumn(name="groepen_id", referencedColumnName="id", nullable=FALSE)
     */
    protected $groep;

    /**
     * @ORM\ManyToMany(targetEntity="Persoon", inversedBy="trainingen")
     * @ORM\JoinTable(name="personen_trainingen")
     * @ORM\OrderBy({"geboortedatum" = "ASC"})
     **/
    protected $persoon;

    /**
     * @ORM\OneToMany(targetEntity="Trainingsdata", mappedBy="trainingen", cascade={"persist", "remove"}, orphanRemoval=TRUE)
     */
    private $trainingsdata;

    /**
     * @ORM\Column(name="dag", type="string", length=255))
     */
    protected $dag;

    /**
     * @ORM\Column(name="tijdvan", type="string", length=255))
     */
    protected $tijdvan;

    /**
     * @ORM\Column(name="tijdtot", type="string", length=255))
     */
    protected $tijdtot;

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
     * Set dag
     *
     * @param string $dag
     * @return Trainingen
     */
    public function setDag($dag)
    {
        $this->dag = $dag;

        return $this;
    }

    /**
     * Get dag
     *
     * @return string 
     */
    public function getDag()
    {
        return $this->dag;
    }

    /**
     * Set tijdvan
     *
     * @param string $tijdvan
     * @return Trainingen
     */
    public function setTijdvan($tijdvan)
    {
        $this->tijdvan = $tijdvan;

        return $this;
    }

    /**
     * Get tijdvan
     *
     * @return string 
     */
    public function getTijdvan()
    {
        return $this->tijdvan;
    }

    /**
     * Set tijdtot
     *
     * @param string $tijdtot
     * @return Trainingen
     */
    public function setTijdtot($tijdtot)
    {
        $this->tijdtot = $tijdtot;

        return $this;
    }

    /**
     * Get tijdtot
     *
     * @return string 
     */
    public function getTijdtot()
    {
        return $this->tijdtot;
    }

    /**
     * Set groep
     *
     * @param \AppBundle\Entity\Groepen $groep
     * @return Trainingen
     */
    public function setGroep(\AppBundle\Entity\Groepen $groep)
    {
        $this->groep = $groep;

        return $this;
    }

    /**
     * Get groep
     *
     * @return \AppBundle\Entity\Groepen 
     */
    public function getGroep()
    {
        return $this->groep;
    }


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->trainingsdata = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add trainingsdata
     *
     * @param \AppBundle\Entity\Trainingsdata $trainingsdata
     * @return Trainingen
     */
    public function addTrainingsdatum(\AppBundle\Entity\Trainingsdata $trainingsdata)
    {
        $this->trainingsdata[] = $trainingsdata;

        return $this;
    }

    /**
     * Remove trainingsdata
     *
     * @param \AppBundle\Entity\Trainingsdata $trainingsdata
     */
    public function removeTrainingsdatum(\AppBundle\Entity\Trainingsdata $trainingsdata)
    {
        $this->trainingsdata->removeElement($trainingsdata);
    }

    /**
     * Get trainingsdata
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTrainingsdata()
    {
        return $this->trainingsdata;
    }

    /**
     * Add persoon
     *
     * @param \AppBundle\Entity\Persoon $persoon
     * @return Trainingen
     */
    public function addPersoon(\AppBundle\Entity\Persoon $persoon)
    {
        $this->persoon[] = $persoon;

        return $this;
    }

    /**
     * Remove persoon
     *
     * @param \AppBundle\Entity\Persoon $persoon
     */
    public function removePersoon(\AppBundle\Entity\Persoon $persoon)
    {
        $this->persoon->removeElement($persoon);
    }

    /**
     * Get persoon
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPersoon()
    {
        return $this->persoon;
    }
}
