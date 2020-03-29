<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="subdoelen")
 */
class SubDoelen
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Doelen")
     * @ORM\JoinColumn(name="doel_id", referencedColumnName="id")
     **/
    protected $doel;

    /**
     * @ORM\ManyToOne(targetEntity="Persoon", inversedBy="subdoelen", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="persoon_id", referencedColumnName="id")
     **/
    private $persoon;

    /**
     * @ORM\OneToMany(targetEntity="Cijfers", mappedBy="subdoel", cascade={"persist", "remove"}, orphanRemoval=TRUE)
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private $cijfers;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->cijfers = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set doel
     *
     * @param \App\Entity\Doelen $doel
     * @return SubDoelen
     */
    public function setDoel(\App\Entity\Doelen $doel = null)
    {
        $this->doel = $doel;
        return $this;
    }

    /**
     * Get doel
     *
     * @return \App\Entity\Doelen
     */
    public function getDoel()
    {
        return $this->doel;
    }

    /**
     * Set persoon
     *
     * @param \App\Entity\Persoon $persoon
     * @return SubDoelen
     */
    public function setPersoon(\App\Entity\Persoon $persoon = null)
    {
        $this->persoon = $persoon;
        return $this;
    }

    /**
     * Get persoon
     *
     * @return \App\Entity\Persoon
     */
    public function getPersoon()
    {
        return $this->persoon;
    }

    /**
     * Add cijfers
     *
     * @param \App\Entity\Cijfers $cijfers
     * @return SubDoelen
     */
    public function addCijfer(\App\Entity\Cijfers $cijfers)
    {
        $this->cijfers[] = $cijfers;
        return $this;
    }

    /**
     * Remove cijfers
     *
     * @param \App\Entity\Cijfers $cijfers
     */
    public function removeCijfer(\App\Entity\Cijfers $cijfers)
    {
        $this->cijfers->removeElement($cijfers);
    }

    /**
     * Get cijfers
     *
     * @return array
     */
    public function getCijfers($timestamp = null)
    {
        if ($timestamp == null) {
            $timestamp = time();
        }
        if (date("m", $timestamp) >= '08') {
            $seizoen = date("Y", $timestamp);
        } else {
            $seizoen = (int)date("Y", $timestamp) - 1;
        }

        $cijfers = array();
        /** @var Cijfers $cijfer */
        foreach ($this->cijfers as $cijfer) {
            if (($cijfer->getDate()->format('Y') == $seizoen && $cijfer->getDate()->format('m') >= 8)
                || ($cijfer->getDate()->format('Y') == ($seizoen + 1) && $cijfer->getDate()->format('m') < 8)) {
                $cijfers[] = $cijfer;
            }
        }
        return $cijfers;
    }
}
