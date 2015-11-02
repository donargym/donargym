<?php
namespace AppBundle\Entity;
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
     * @ORM\ManyToOne(targetEntity="Doelen", cascade={"persist"})
     * @ORM\JoinColumn(name="doel_id", referencedColumnName="id")
     **/
    protected $doel;

    /**
     * @ORM\ManyToOne(targetEntity="Persoon", inversedBy="subdoelen", cascade={"persist"})
     * @ORM\JoinColumn(name="persoon_id", referencedColumnName="id")
     **/
    private $persoon;

    /**
     * @ORM\OneToMany(targetEntity="Cijfers", mappedBy="subdoel")
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
     * @param \AppBundle\Entity\Doelen $doel
     * @return SubDoelen
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
     * @return SubDoelen
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

    /**
     * Add cijfers
     *
     * @param \AppBundle\Entity\Cijfers $cijfers
     * @return SubDoelen
     */
    public function addCijfer(\AppBundle\Entity\Cijfers $cijfers)
    {
        $this->cijfers[] = $cijfers;
        return $this;
    }

    /**
     * Remove cijfers
     *
     * @param \AppBundle\Entity\Cijfers $cijfers
     */
    public function removeCijfer(\AppBundle\Entity\Cijfers $cijfers)
    {
        $this->cijfers->removeElement($cijfers);
    }

    /**
     * Get cijfers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCijfers()
    {
        return $this->cijfers;
    }
}