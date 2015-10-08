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
     * @ORM\OneToOne(targetEntity="Doelen")
     * @ORM\JoinColumn(name="doel_id", referencedColumnName="id")
     **/
    protected $doel;

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
