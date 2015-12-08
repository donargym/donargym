<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="seizoensdoelen")
 */
class SeizoensDoelen
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
    protected $seizoen;

    /**
     * @ORM\ManyToOne(targetEntity="Doelen")
     * @ORM\JoinColumn(name="doel_id", referencedColumnName="id", unique=false)
     **/
    protected $doel;

    /**
     * @ORM\Column(type="integer", nullable=TRUE)
     */
    protected $cijfer;

    /**
    * @ORM\Column(type="boolean", nullable=TRUE)
    **/
    protected $tachtigProcent;

    /**
     * @ORM\Column(type="boolean", nullable=TRUE)
     **/
    protected $negentigProcent;

    /**
     * @ORM\ManyToOne(targetEntity="Persoon", inversedBy="seizoensdoelen")
     * @ORM\JoinColumn(name="persoon_id", referencedColumnName="id")
     **/
    private $persoon;


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
     * Set seizoen
     *
     * @param string $seizoen
     * @return SeizoensDoelen
     */
    public function setSeizoen($seizoen)
    {
        $this->seizoen = $seizoen;

        return $this;
    }

    /**
     * Get seizoen
     *
     * @return string 
     */
    public function getSeizoen()
    {
        return $this->seizoen;
    }

    /**
     * Set cijfer
     *
     * @param string $cijfer
     * @return SeizoensDoelen
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
     * Set tachtigProcent
     *
     * @param string $tachtigProcent
     * @return SeizoensDoelen
     */
    public function setTachtigProcent($tachtigProcent)
    {
        $this->seizoen = $tachtigProcent;

        return $this;
    }

    /**
     * Get tachtigProcent
     *
     * @return boolean
     */
    public function getTachtigProcent()
    {
        return $this->tachtigProcent;
    }

    /**
     * Set negentigProcent
     *
     * @param string $negentigProcent
     * @return SeizoensDoelen
     */
    public function setNegentigProcent($negentigProcent)
    {
        $this->negentigProcent = $negentigProcent;

        return $this;
    }

    /**
     * Get negentigProcent
     *
     * @return boolean
     */
    public function getNegentigProcent()
    {
        return $this->negentigProcent;
    }

    /**
     * Set doel
     *
     * @param \AppBundle\Entity\Doelen $doel
     * @return SeizoensDoelen
     */
    public function setDoel(\AppBundle\Entity\Doelen $doel = null)
    {
        $this->doel = $doel;

        return $this;
    }

    /**
     * Get doel
     *
     * @return \AppBundle\Entity\Doelen 
     */
    public function getDoel()
    {
        return $this->doel;
    }

    /**
     * Set persoon
     *
     * @param \AppBundle\Entity\Persoon $persoon
     * @return SeizoensDoelen
     */
    public function setPersoon(\AppBundle\Entity\Persoon $persoon = null)
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
}
