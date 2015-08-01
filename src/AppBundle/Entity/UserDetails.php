<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class UserDetails
{
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
     * @ORM\Column(name="geboortedatum", type="date")
     */
    private $geboortedatum;

    /**
     * @var string
     *
     * @ORM\Column(name="foto", type="string", length=255)
     */
    private $foto;


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



}
