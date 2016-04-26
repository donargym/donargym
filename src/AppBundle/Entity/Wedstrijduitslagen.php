<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use MyProject\Proxies\__CG__\OtherProject\Proxies\__CG__\stdClass;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="wedstrijduitslagen")
 */
class Wedstrijduitslagen
{
    private $temp;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(length=300)
     */
    protected $locatie;

    /**
     * @Assert\File(maxSize="6000000")
     */
    private $file;

    /**
     * @ORM\ManyToOne(targetEntity="Groepen", inversedBy="wedstrijduitslagen")
     * @ORM\JoinColumn(name="groepen_id", referencedColumnName="id", nullable=FALSE)
     */
    protected $groep;

    /**
     * @ORM\Column(type="date")
     */
    protected $datum;

    /**
     * @ORM\Column(name="naam", type="string", length=255))
     */
    protected $naam;

    public function getAll()
    {
        $uitslagen = new \stdClass();
        $uitslagen->id = $this->getId();
        $uitslagen->locatie = $this->getLocatie();
        $uitslagen->datum = $this->getDatum();
        $uitslagen->naam = $this->getNaam();
        return $uitslagen;
    }

    public function getAbsolutePath()
    {
        return null === $this->locatie
            ? null
            : $this->getUploadRootDir().'/'.$this->locatie;
    }

    public function getWebPath()
    {
        return null === $this->locatie
            ? null
            : $this->getUploadDir().'/'.$this->locatie;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../httpdocs/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/wedstrijduitslagen';
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
        if (isset($this->locatie)) {
            $this->temp = $this->locatie;
            $this->locatie = null;
        } else {
            $this->locatie = 'initial';
        }
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->getFile()) {
            $filename = sha1(uniqid(mt_rand(), true));
            $this->locatie = $filename.'.'.$this->getFile()->getClientOriginalExtension();
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->getFile()) {
            return;
        }
        $this->getFile()->move($this->getUploadRootDir(), $this->locatie);
        if (isset($this->temp)) {
            unlink($this->getUploadRootDir().'/'.$this->temp);
            $this->temp = null;
        }
        $this->file = null;
    }

    /**
     * @ORM\PreRemove()
     */
    public function storeFilenameForRemove()
    {
        $this->temp = $this->getAbsolutePath();
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if (isset($this->temp)) {
            unlink($this->temp);
        }
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
     * Set locatie
     *
     * @param string $locatie
     * @return Wedstrijduitslagen
     */
    public function setLocatie($locatie)
    {
        $this->locatie = $locatie;

        return $this;
    }

    /**
     * Get locatie
     *
     * @return string 
     */
    public function getLocatie()
    {
        return $this->locatie;
    }

    /**
     * Set datum
     *
     * @param \DateTime $datum
     * @return Wedstrijduitslagen
     */
    public function setDatum($datum)
    {
        $this->datum = $datum;

        return $this;
    }

    /**
     * Get datum
     *
     * @return \DateTime 
     */
    public function getDatum()
    {
        return $this->datum;
    }

    /**
     * Set naam
     *
     * @param string $naam
     * @return Wedstrijduitslagen
     */
    public function setNaam($naam)
    {
        $this->naam = $naam;

        return $this;
    }

    /**
     * Get naam
     *
     * @return string 
     */
    public function getNaam()
    {
        return $this->naam;
    }

    /**
     * Set groep
     *
     * @param \AppBundle\Entity\Groepen $groep
     * @return Wedstrijduitslagen
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
}
