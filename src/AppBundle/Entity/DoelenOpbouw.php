<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="doelenopbouw")
 */
class DoelenOpbouw
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
    protected $trede;

    /**
     * @ORM\OneToOne(targetEntity="Doelen")
     * @ORM\JoinColumn(name="subdoel_id", referencedColumnName="id", nullable=FALSE)
     **/
    protected $subdoel;


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
     * Set trede
     *
     * @param integer $trede
     * @return DoelenOpbouw
     */
    public function setTrede($trede)
    {
        $this->trede = $trede;

        return $this;
    }

    /**
     * Get trede
     *
     * @return integer 
     */
    public function getTrede()
    {
        return $this->trede;
    }

    /**
     * Set subdoel
     *
     * @param \AppBundle\Entity\Doelen $subdoel
     * @return DoelenOpbouw
     */
    public function setSubdoel(\AppBundle\Entity\Doelen $subdoel = null)
    {
        $this->subdoel = $subdoel;

        return $this;
    }

    /**
     * Get subdoel
     *
     * @return \AppBundle\Entity\Doelen 
     */
    public function getSubdoel()
    {
        return $this->subdoel;
    }
}
