<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="clubblad")
 */
class Clubblad
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="date")
     */
    protected $datum;

    /**
     * @ORM\Column(length=300)
     */
    protected $locatie;

    public function getAll()
    {
        $items = new \stdClass();
        $items->id = $this->id;
        $items->datum = $this->datum->format('d-m-Y');
        $items->locatie = $this->locatie;
        return $items;
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
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/clubblad';
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
     * Set datum
     *
     * @param \DateTime $datum
     * @return Clubblad
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
        return $this->datum->format('d-m-Y');
    }

    /**
     * Set locatie
     *
     * @param string $locatie
     * @return Clubblad
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
}
