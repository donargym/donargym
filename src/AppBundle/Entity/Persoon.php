<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * @ORM\Entity
 * @ORM\Table(name="persoon")
 */
class Persoon
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="voornaam", type="string", length=255)
     */
    private $voornaam;

    /**
     * @var string
     *
     * @ORM\Column(name="achternaam", type="string", length=255)
     */
    private $achternaam;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="geboortedatum", type="string", length=255)
     */
    private $geboortedatum;

    /**
     * @ORM\OneToOne(targetEntity="SelectieFoto", cascade={"persist", "remove"}, orphanRemoval=TRUE))
     * @ORM\JoinColumn(name="foto_id", referencedColumnName="id")
     **/
    private $foto;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="persoon")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=FALSE)
     **/
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="Functie", mappedBy="persoon", cascade={"persist", "remove"}, orphanRemoval=TRUE)
     */
    private $functie;

    /**
     * @ORM\OneToMany(targetEntity="SeizoensDoelen", mappedBy="persoon", cascade={"persist", "remove"}, orphanRemoval=TRUE)
     */
    private $seizoensdoelen;

    /**
     * @ORM\OneToMany(targetEntity="SubDoelen", mappedBy="persoon", cascade={"persist", "remove"}, orphanRemoval=TRUE)
     */
    private $subdoelen;

    /**
     * @ORM\ManyToMany(targetEntity="Trainingen", mappedBy="persoon")
     * @ORM\JoinTable(name="personen_trainingen",
     *      joinColumns={@ORM\JoinColumn(name="trainingen_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="persoon_id", referencedColumnName="id", onDelete="cascade")}
     *      )
     **/
    private $trainingen;

    /**
     * @ORM\OneToMany(targetEntity="Aanwezigheid", mappedBy="persoon", cascade={"persist", "remove"}, orphanRemoval=TRUE)
     */
    private $aanwezigheid;

    /**
     * @ORM\OneToOne(targetEntity="Stukje", cascade={"persist", "remove"}, orphanRemoval=TRUE)
     * @ORM\JoinColumn(name="stukje_id", referencedColumnName="id")
     **/
    private $stukje;

    public function __construct()
    {
        $this->functie = new ArrayCollection();
        $this->doelen = new ArrayCollection();
        $this->aanwezigheid = new ArrayCollection();
        $this->trainingen = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */

    public function getAll()
    {
        $persoon = new \stdClass();
        $persoon->id = $this->getId();
        $persoon->voornaam = $this->getVoornaam();
        $persoon->achternaam = $this->getAchternaam();
        $foto = $this->getFoto();
        if ($foto == null) {$persoon->foto = "plaatje.jpg";}
        else {$persoon->foto = $foto->getLocatie();}
        $persoon->categorie = $this->categorie(strtotime($this->getGeboortedatum()));
        return $persoon;
    }

    public function categorie($geboortedatum)
    {
        if(date('m',time()) < 8)
        {
            if((date('Y',time())-date("Y",$geboortedatum))<8)
            {
                return "Voorinstap";
            }
            if((date('Y',time())-date("Y",$geboortedatum))==8)
            {
                return "Voorinstap";
            }
            elseif((date('Y',time())-date("Y",$geboortedatum))==9)
            {
                return "Instap";
            }
            elseif((date('Y',time())-date("Y",$geboortedatum))==10)
            {
                return "Pupil 1";
            }
            elseif((date('Y',time())-date("Y",$geboortedatum))==11)
            {
                return "Pupil 2";
            }
            elseif((date('Y',time())-date("Y",$geboortedatum))==12)
            {
                return "Jeugd 1";
            }
            elseif((date('Y',time())-date("Y",$geboortedatum))==13)
            {
                return "Jeugd 2";
            }
            elseif((date('Y',time())-date("Y",$geboortedatum))==14 || (date(Y,time())-date("Y",$geboortedatum))==15)
            {
                return "Junior";
            }
            else
            {
                return "Senior";
            }
        }
        else
        {

            if((date('Y',time())-date("Y",$geboortedatum))<7)
            {
                return "Voorinstap";
            }
            if((date('Y',time())-date("Y",$geboortedatum))==7)
            {
                return "Voorinstap";
            }
            elseif((date('Y',time())-date("Y",$geboortedatum))==8)
            {
                return "Instap";
            }
            elseif((date('Y',time())-date("Y",$geboortedatum))==9)
            {
                return "Pupil 1";
            }
            elseif((date('Y',time())-date("Y",$geboortedatum))==10)
            {
                return "Pupil 2";
            }
            elseif((date('Y',time())-date("Y",$geboortedatum))==11)
            {
                return "Jeugd 1";
            }
            elseif((date('Y',time())-date("Y",$geboortedatum))==12)
            {
                return "Jeugd 2";
            }
            elseif((date('Y',time())-date("Y",$geboortedatum))==13 || (date("Y",time())-date("Y",$geboortedatum))==14)
            {
                return "Junior";
            }
            else
            {
                return "Senior";
            }
        }
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * Set voornaam
     *
     * @param string $voornaam
     * @return Persoon
     */
    public function setVoornaam($voornaam)
    {
        $this->voornaam = $voornaam;

        return $this;
    }

    /**
     * Get voornaam
     *
     * @return string 
     */
    public function getVoornaam()
    {
        return $this->voornaam;
    }

    /**
     * Set achternaam
     *
     * @param string $achternaam
     * @return Persoon
     */
    public function setAchternaam($achternaam)
    {
        $this->achternaam = $achternaam;

        return $this;
    }

    /**
     * Get achternaam
     *
     * @return string 
     */
    public function getAchternaam()
    {
        return $this->achternaam;
    }

    /**
     * Set foto
     *
     * @param \AppBundle\Entity\SelectieFoto $foto
     * @return Persoon
     */
    public function setFoto(\AppBundle\Entity\SelectieFoto $foto = null)
    {
        $this->foto = $foto;

        return $this;
    }

    /**
     * Get foto
     *
     * @return \AppBundle\Entity\SelectieFoto 
     */
    public function getFoto()
    {
        return $this->foto;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     * @return Persoon
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    public function addFunctie(Functie $functie)
    {
        if (!$this->functie->contains($functie)) {
            $this->functie->add($functie);
            $functie->setPersoon($this);
        }

        return $this;
    }

    public function removeFunctie(Functie $functie)
    {
        if ($this->functie->contains($functie)) {
            $this->functie->removeElement($functie);
            $functie->setPersoon(null);
        }

        return $this;
    }

    public function getGroepen()
    {
        return array_map(
            function ($functie) {
                return $functie->getGroep();
            },
            $this->functie->toArray()
        );
    }

    /**
     * Get functie
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFunctie()
    {
        return $this->functie;
    }

    /**
     * Set stukje
     *
     * @param \AppBundle\Entity\Stukje $stukje
     * @return Persoon
     */
    public function setStukje(\AppBundle\Entity\Stukje $stukje = null)
    {
        $this->stukje = $stukje;

        return $this;
    }

    /**
     * Get stukje
     *
     * @return \AppBundle\Entity\Stukje 
     */
    public function getStukje()
    {
        return $this->stukje;
    }

    /**
     * Add aanwezigheid
     *
     * @param \AppBundle\Entity\Aanwezigheid $aanwezigheid
     * @return Persoon
     */
    public function addAanwezigheid(\AppBundle\Entity\Aanwezigheid $aanwezigheid)
    {
        $this->aanwezigheid[] = $aanwezigheid;

        return $this;
    }

    /**
     * Remove aanwezigheid
     *
     * @param \AppBundle\Entity\Aanwezigheid $aanwezigheid
     */
    public function removeAanwezigheid(\AppBundle\Entity\Aanwezigheid $aanwezigheid)
    {
        $this->aanwezigheid->removeElement($aanwezigheid);
    }

    /**
     * Get aanwezigheid
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAanwezigheid()
    {
        return $this->aanwezigheid;
    }

    /**
     * Add seizoensdoelen
     *
     * @param \AppBundle\Entity\SeizoensDoelen $seizoensdoelen
     * @return Persoon
     */
    public function addSeizoensdoelen(\AppBundle\Entity\SeizoensDoelen $seizoensdoelen)
    {
        $this->seizoensdoelen[] = $seizoensdoelen;

        return $this;
    }

    /**
     * Remove seizoensdoelen
     *
     * @param \AppBundle\Entity\SeizoensDoelen $seizoensdoelen
     */
    public function removeSeizoensdoelen(\AppBundle\Entity\SeizoensDoelen $seizoensdoelen)
    {
        $this->seizoensdoelen->removeElement($seizoensdoelen);
    }

    /**
     * Get seizoensdoelen
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSeizoensdoelen()
    {
        return $this->seizoensdoelen;
    }

    /**
     * Add subdoelen
     *
     * @param \AppBundle\Entity\SubDoelen $subdoelen
     * @return Persoon
     */
    public function addSubdoelen(\AppBundle\Entity\SubDoelen $subdoelen)
    {
        $this->subdoelen[] = $subdoelen;

        return $this;
    }

    /**
     * Remove subdoelen
     *
     * @param \AppBundle\Entity\SubDoelen $subdoelen
     */
    public function removeSubdoelen(\AppBundle\Entity\SubDoelen $subdoelen)
    {
        $this->subdoelen->removeElement($subdoelen);
    }

    /**
     * Get subdoelen
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSubdoelen()
    {
        return $this->subdoelen;
    }

    /**
     * Add trainingen
     *
     * @param \AppBundle\Entity\Trainingen $trainingen
     * @return Persoon
     */
    public function addTrainingen(\AppBundle\Entity\Trainingen $trainingen)
    {
        $trainingen->addPersoon($this);
        $this->trainingen[] = $trainingen;

        return $this;
    }

    /**
     * Remove trainingen
     *
     * @param \AppBundle\Entity\Trainingen $trainingen
     */
    public function removeTrainingen(\AppBundle\Entity\Trainingen $trainingen)
    {
        $this->trainingen->removeElement($trainingen);
        $trainingen->removePersoon($this);
    }

    /**
     * Get trainingen
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTrainingen()
    {
        return $this->trainingen;
    }

    /**
     * Set geboortedatum
     *
     * @param string $geboortedatum
     * @return Persoon
     */
    public function setGeboortedatum($geboortedatum)
    {
        $this->geboortedatum = $geboortedatum;

        return $this;
    }

    /**
     * Get geboortedatum
     *
     * @return string 
     */
    public function getGeboortedatum()
    {
        return $this->geboortedatum;
    }
}
