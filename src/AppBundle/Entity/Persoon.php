<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\PersoonRepository")
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
     * @ORM\OneToOne(targetEntity="Vloermuziek", cascade={"persist", "remove"}, orphanRemoval=TRUE))
     * @ORM\JoinColumn(name="vloermuziek_id", referencedColumnName="id")
     **/
    private $vloermuziek;

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
     * @ORM\OneToMany(targetEntity="Voedsel", mappedBy="persoon", cascade={"persist", "remove"}, orphanRemoval=TRUE)
     */
    private $voedsel;

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
     * @ORM\ManyToMany(targetEntity="Wedstrijdkalender", mappedBy="persoon")
     * @ORM\JoinTable(name="personen_wedstrijdkalender",
     *      joinColumns={@ORM\JoinColumn(name="wedstrijdkalender_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="persoon_id", referencedColumnName="id", onDelete="cascade")}
     *      )
     **/
    private $wedstrijdkalender;

    /**
     * @ORM\OneToMany(targetEntity="Aanwezigheid", mappedBy="persoon", cascade={"persist", "remove"}, orphanRemoval=TRUE)
     */
    private $aanwezigheid;

    /**
     * @ORM\OneToOne(targetEntity="Stukje", cascade={"persist", "remove"}, orphanRemoval=TRUE)
     * @ORM\JoinColumn(name="stukje_id", referencedColumnName="id")
     **/
    private $stukje;

    /**
     * @ORM\Column(type="integer", nullable=TRUE)
     */
    private $voortgangSprong;

    /**
     * @ORM\Column(type="integer", nullable=TRUE)
     */
    private $voortgangBrug;

    /**
     * @ORM\Column(type="integer", nullable=TRUE)
     */
    private $voortgangBalk;

    /**
     * @ORM\Column(type="integer", nullable=TRUE)
     */
    private $voortgangVloer;

    /**
     * @ORM\Column(type="integer", nullable=TRUE)
     */
    private $voortgangTotaal;

    /**
     * @ORM\Column(length=255, nullable=TRUE)
     */
    private $lastUpdatedAtSeizoen;

    public function __construct()
    {
        $this->functie = new ArrayCollection();
        $this->subdoelen = new ArrayCollection();
        $this->seizoensdoelen = new ArrayCollection();
        $this->aanwezigheid = new ArrayCollection();
        $this->trainingen = new ArrayCollection();
        $this->voedsel = new ArrayCollection();
        $this->wedstrijdkalender = new ArrayCollection();
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
        $geboortedatum = $this->getGeboortedatum();
        $persoon->geboortedatum = date('d-m-Y', strtotime($geboortedatum));
        $foto = $this->getFoto();
        $vloermuziek = $this->getVloermuziek();
        if ($vloermuziek == null) {$persoon->vloermuziek = null;}
        else {$persoon->vloermuziek = $vloermuziek->getLocatie();}
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
     * Set vloermuziek
     *
     * @param \AppBundle\Entity\Vloermuziek $vloermuziek
     * @return Persoon
     */
    public function setVloermuziek(\AppBundle\Entity\Vloermuziek $vloermuziek = null)
    {
        $this->vloermuziek = $vloermuziek;

        return $this;
    }

    /**
     * Get foto
     *
     * @return \AppBundle\Entity\SelectieFoto
     */
    public function getVloermuziek()
    {
        return $this->vloermuziek;
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
     * Set voortgangSprong
     *
     * @param integer $voortgangSprong
     * @return Persoon
     */
    public function setVoortgangSprong($voortgangSprong)
    {
        $this->voortgangSprong = $voortgangSprong;

        return $this;
    }

    /**
     * Get voortgangSprong
     *
     * @return integer 
     */
    public function getVoortgangSprong()
    {
        return $this->voortgangSprong;
    }

    /**
     * Set voortgangBrug
     *
     * @param integer $voortgangBrug
     * @return Persoon
     */
    public function setVoortgangBrug($voortgangBrug)
    {
        $this->voortgangBrug = $voortgangBrug;

        return $this;
    }

    /**
     * Get voortgangBrug
     *
     * @return integer 
     */
    public function getVoortgangBrug()
    {
        return $this->voortgangBrug;
    }

    /**
     * Set voortgangBalk
     *
     * @param integer $voortgangBalk
     * @return Persoon
     */
    public function setVoortgangBalk($voortgangBalk)
    {
        $this->voortgangBalk = $voortgangBalk;

        return $this;
    }

    /**
     * Get voortgangBalk
     *
     * @return integer 
     */
    public function getVoortgangBalk()
    {
        return $this->voortgangBalk;
    }

    /**
     * Set voortgangVloer
     *
     * @param integer $voortgangVloer
     * @return Persoon
     */
    public function setVoortgangVloer($voortgangVloer)
    {
        $this->voortgangVloer = $voortgangVloer;

        return $this;
    }

    /**
     * Get voortgangVloer
     *
     * @return integer 
     */
    public function getVoortgangVloer()
    {
        return $this->voortgangVloer;
    }

    /**
     * Set voortgangTotaal
     *
     * @param integer $voortgangTotaal
     * @return Persoon
     */
    public function setVoortgangTotaal($voortgangTotaal)
    {
        $this->voortgangTotaal = $voortgangTotaal;

        return $this;
    }

    /**
     * Get lastUpdatedAtSeizoen
     *
     * @return string
     */
    public function getLastUpdatedAtSeizoen()
    {
        return $this->lastUpdatedAtSeizoen;
    }

    /**
     * Set lastUpdatedAtSeizoen
     *
     * @param string $lastUpdatedAtSeizoen
     * @return Persoon
     */
    public function setLastUpdatedAtSeizoen($lastUpdatedAtSeizoen)
    {
        $this->lastUpdatedAtSeizoen = $lastUpdatedAtSeizoen;

        return $this;
    }

    /**
     * Get voortgangTotaal
     *
     * @return integer
     */
    public function getVoortgangTotaal()
    {
        return $this->voortgangTotaal;
    }

    /**
     * Add voedsel
     *
     * @param \AppBundle\Entity\Voedsel $voedsel
     * @return Persoon
     */
    public function addVoedsel(\AppBundle\Entity\Voedsel $voedsel)
    {
        $this->voedsel[] = $voedsel;

        return $this;
    }

    /**
     * Remove voedsel
     *
     * @param \AppBundle\Entity\Voedsel $voedsel
     */
    public function removeVoedsel(\AppBundle\Entity\Voedsel $voedsel)
    {
        $this->voedsel->removeElement($voedsel);
    }

    /**
     * Get voedsel
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getVoedsel()
    {
        return $this->voedsel;
    }

    /**
     * Add wedstrijdkalender
     *
     * @param \AppBundle\Entity\Wedstrijdkalender $wedstrijdkalender
     * @return Persoon
     */
    public function addWedstrijdkalender(\AppBundle\Entity\Wedstrijdkalender $wedstrijdkalender)
    {
        $this->wedstrijdkalender[] = $wedstrijdkalender;

        return $this;
    }

    /**
     * Remove wedstrijdkalender
     *
     * @param \AppBundle\Entity\Wedstrijdkalender $wedstrijdkalender
     */
    public function removeWedstrijdkalender(\AppBundle\Entity\Wedstrijdkalender $wedstrijdkalender)
    {
        $this->wedstrijdkalender->removeElement($wedstrijdkalender);
    }

    /**
     * Get wedstrijdkalender
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getWedstrijdkalender()
    {
        return $this->wedstrijdkalender;
    }
}
