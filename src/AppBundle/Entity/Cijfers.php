<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="cijfers")
 */
class Cijfers
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $cijfer;

    /**
     * @ORM\ManyToOne(targetEntity="SubDoelen", inversedBy="cijfers")
     * @ORM\JoinColumn(name="subdoel_id", referencedColumnName="id")
     **/
    private $subdoel;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

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
     * Set cijfer
     *
     * @param integer $cijfer
     * @return Cijfers
     */
    public function setCijfer($cijfer)
    {
        $this->cijfer = $cijfer;

        return $this;
    }

    /**
     * Get cijfer
     *
     * @return integer 
     */
    public function getCijfer()
    {
        return $this->cijfer;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Cijfers
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set subdoel
     *
     * @param \AppBundle\Entity\SubDoelen $subdoel
     * @return Cijfers
     */
    public function setSubdoel(\AppBundle\Entity\SubDoelen $subdoel = null)
    {
        $this->subdoel = $subdoel;

        return $this;
    }

    /**
     * Get subdoel
     *
     * @return \AppBundle\Entity\SubDoelen 
     */
    public function getSubdoel()
    {
        return $this->subdoel;
    }
}
