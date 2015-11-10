<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="trainingsplan")
 */
class Trainingsplan
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $trainingsdatumId;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $trainingsplan;

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
     * Set trainingsdatumId
     *
     * @param integer $trainingsdatumId
     * @return Trainingsplan
     */
    public function setTrainingsdatumId($trainingsdatumId)
    {
        $this->trainingsdatumId = $trainingsdatumId;

        return $this;
    }

    /**
     * Get trainingsdatumId
     *
     * @return integer 
     */
    public function getTrainingsdatumId()
    {
        return $this->trainingsdatumId;
    }

    /**
     * Set trainingsplan
     *
     * @param string $trainingsplan
     * @return Trainingsplan
     */
    public function setTrainingsplan($trainingsplan)
    {
        $this->trainingsplan = $trainingsplan;

        return $this;
    }

    /**
     * Get trainingsplan
     *
     * @return string 
     */
    public function getTrainingsplan()
    {
        return $this->trainingsplan;
    }
}
