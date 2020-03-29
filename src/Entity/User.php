<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=190, unique=true)
     */
    private $username;

    /**
     * @ORM\COLUMN(type="string", length=60)
     */
    private $role;

    /**
     * @var string
     *
     * @ORM\Column(name="email2", type="string", length=190, unique=true, nullable=true)
     */
    private $email2;
	
	/**
     * @var string
     *
     * @ORM\Column(name="email3", type="string", length=190, unique=true, nullable=true)
     */
    private $email3;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isActive", type="boolean")
     */
    private $isActive;

    /**
     * @var string
     *
     * @ORM\Column(name="straatnr", type="string", length=255)
     */
    private $straatnr;

    /**
     * @var string
     *
     * @ORM\Column(name="postcode", type="string", length=255)
     */
    private $postcode;

    /**
     * @var string
     *
     * @ORM\Column(name="plaats", type="string", length=255)
     */
    private $plaats;

    /**
     * @var string
     *
     * @ORM\Column(name="tel1", type="string", length=255)
     */
    private $tel1;

    /**
     * @var string
     *
     * @ORM\Column(name="tel2", type="string", length=255, nullable=true)
     */
    private $tel2;

    /**
     * @var string
     *
     * @ORM\Column(name="tel3", type="string", length=255, nullable=true)
     */
    private $tel3;

    /**
     * @ORM\OneToMany(targetEntity="Persoon", mappedBy="user")
     *
     */
    private $persoon;

    /**
     * @ORM\ManyToOne(targetEntity="Vereniging", inversedBy="user")
     *
     */
    private $vereniging;

    /**
     * @ORM\OneToMany(targetEntity="Turnster", mappedBy="user", cascade={"persist", "remove"})
     */
    private $turnster;

    public function __construct()
    {
        $this->persoon = new ArrayCollection();
        $this->turnster = new ArrayCollection();
        $this->isActive = true;
    }

    /**
     * Add turnster
     *
     * @param \App\Entity\Turnster $turnster
     * @return User
     */
    public function addTurnster(\App\Entity\Turnster $turnster)
    {
        $this->turnster[] = $turnster;

        return $this;
    }

    /**
     * Remove turnster
     *
     * @param \App\Entity\Turnster $turnster
     */
    public function removeTurnster(\App\Entity\Turnster $turnster)
    {
        $this->turnster->removeElement($turnster);
    }

    /**
     * Get turnster
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTurnster()
    {
        return $this->turnster;
    }

    /**
     * Set vereniging
     *
     * @param \App\Entity\Vereniging $vereniging
     * @return User
     */
    public function setVereniging(\App\Entity\Vereniging $vereniging = null)
    {
        $this->vereniging = $vereniging;

        return $this;
    }

    /**
     * Get vereniging
     *
     * @return \App\Entity\Vereniging
     */
    public function getVereniging()
    {
        return $this->vereniging;
    }

    public function getUsername()
    {
        return $this->username;
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

    public function getSalt()
    {
        return null;
    }

    /**
     * Set email2
     *
     * @param string $email2
     * @return User
     */
    public function setEmail2($email2)
    {
        if ($email2 == "") {
            $this->email2 = null;
        } else {
            $this->email2 = $email2;
        }

        return $this;
    }

    /**
     * Get email2
     *
     * @return string 
     */
    public function getEmail2()
    {
        return $this->email2;
    }
	
	/**
     * Set email3
     *
     * @param string $email3
     * @return User
     */
    public function setEmail3($email3)
    {
        if ($email3 == "") {
            $this->email3 = null;
        } else {
            $this->email3 = $email3;
        }
        return $this;
    }
    /**
     * Get email3
     *
     * @return string
     */
    public function getEmail3()
    {
        return $this->email3;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set roles
     *
     * @param string $role
     * @return User
     */
    public function setRoles($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return \Symfony\Component\Security\Core\Role\Role[]
     */
    public function getRoles()
    {
        return array($this->role);
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    public function eraseCredentials()
    {
    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->isActive;
    }


    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            $this->isActive
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            $this->isActive
            ) = unserialize($serialized);
    }

    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set role
     *
     * @param string $role
     * @return User
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return string 
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set straatnr
     *
     * @param string $straatnr
     * @return User
     */
    public function setStraatnr($straatnr)
    {
        $this->straatnr = $straatnr;

        return $this;
    }

    /**
     * Get straatnr
     *
     * @return string 
     */
    public function getStraatnr()
    {
        return $this->straatnr;
    }

    /**
     * Set postcode
     *
     * @param string $postcode
     * @return User
     */
    public function setPostcode($postcode)
    {
        $this->postcode = $postcode;

        return $this;
    }

    /**
     * Get postcode
     *
     * @return string 
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * Set plaats
     *
     * @param string $plaats
     * @return User
     */
    public function setPlaats($plaats)
    {
        $this->plaats = $plaats;

        return $this;
    }

    /**
     * Get plaats
     *
     * @return string 
     */
    public function getPlaats()
    {
        return $this->plaats;
    }

    /**
     * Set tel1
     *
     * @param string $tel1
     * @return User
     */
    public function setTel1($tel1)
    {
        $this->tel1 = $tel1;

        return $this;
    }

    /**
     * Get tel1
     *
     * @return string 
     */
    public function getTel1()
    {
        return $this->tel1;
    }

    /**
     * Set tel2
     *
     * @param string $tel2
     * @return User
     */
    public function setTel2($tel2)
    {
        $this->tel2 = $tel2;

        return $this;
    }

    /**
     * Get tel2
     *
     * @return string 
     */
    public function getTel2()
    {
        return $this->tel2;
    }

    /**
     * Set tel3
     *
     * @param string $tel3
     * @return User
     */
    public function setTel3($tel3)
    {
        $this->tel3 = $tel3;

        return $this;
    }

    /**
     * Get tel3
     *
     * @return string 
     */
    public function getTel3()
    {
        return $this->tel3;
    }

    /**
     * Add persoon
     *
     * @param \App\Entity\persoon $persoon
     * @return User
     */
    public function addPersoon(\App\Entity\persoon $persoon)
    {
        $this->persoon[] = $persoon;

        return $this;
    }

    /**
     * Remove persoon
     *
     * @param \App\Entity\persoon $persoon
     */
    public function removePersoon(\App\Entity\persoon $persoon)
    {
        $this->persoon->removeElement($persoon);
    }

    /**
     * Get persoon
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPersoon()
    {
        return $this->persoon;
    }
}
