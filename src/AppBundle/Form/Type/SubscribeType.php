<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Inschrijving;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SubscribeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('firstname', 'text', array('label' => 'Voornaam*'))
            ->add('lastname', 'text', array('label' => 'Achternaam*'))
            ->add('nameletters', 'text', array('label' => 'Initialen*'))
            ->add(
                'dateofbirth',
                'date',
                array(
                    'empty_value' => '',
                    'years'       => range(1908, date('Y')),
                    'format'      => 'dd MM yyyy',
                    'label'       => 'Geboortedatum*'
                )
            )
            ->add(
                'gender',
                'choice',
                array(
                    'label'             => 'Geslacht*',
                    'placeholder'       => 'Selecteer een geslacht',
                    'choices'           => array(
                        'Man'   => 'Man',
                        'Vrouw' => 'Vrouw',
                    ),
                    'choices_as_values' => true,
                )
            )
            ->add('address', 'text', array('label' => 'Straat + huisnummer*'))
            ->add('postcode', 'text', array('label' => 'Postcode*'))
            ->add('city', 'text', array('label' => 'Plaats*'))
            ->add('phone1', 'text', array('label' => 'Telefoonnummer*'))
            ->add('phone2', 'text', array('required' => false, 'label' => 'Evt. 2e telefoonnummer'))
            ->add('bankaccountnumber', 'text', array('label' => 'IBAN nummer*'))
            ->add('bankaccountholder', 'text', array('label' => 'Naam rekeninghouder*'))
            ->add('emailaddress', 'email', array('label' => 'Email adres*'))
            ->add(
                'havebeensubscribed',
                'choice',
                array(
                    'label'             => 'Bent u eerder lid geweest bij Donar?*',
                    'choices'           => array(
                        'Nee' => 'Nee',
                        'Ja'  => 'ja',
                    ),
                    'choices_as_values' => true,
                )
            )
            ->add(
                'subscribedfrom',
                'date',
                array(
                    'format'   => 'dd MM yyyy',
                    'required' => false,
                    'label'    => 'Zo ja, van wanneer?',
                    'years'    => range(1908, date('Y')),
                )
            )
            ->add(
                'subscribeduntil',
                'date',
                array(
                    'format'   => 'dd MM yyyy',
                    'required' => false,
                    'label'    => 'Tot wanneer?',
                    'years'    => range(1908, date('Y')),
                )
            )
            ->add(
                'otherclub',
                'choice',
                array(
                    'label'             => 'Bent u lid van een andere vereniging?*',
                    'choices'           => array(
                        'Nee' => 'Nee',
                        'Ja'  => 'Ja',
                    ),
                    'choices_as_values' => true,
                )
            )
            ->add('whatotherclub', 'text', array('required' => false, 'label' => 'Zo ja, welke vereniging?'))
            ->add(
                'bondscontributiebetaald',
                'choice',
                array(
                    'label'             => 'Heeft u dit jaar bondscontributie betaald?*',
                    'choices'           => array(
                        'Nee' => 'Nee',
                        'Ja'  => 'Ja',
                    ),
                    'choices_as_values' => true,
                )
            )
            ->add(
                'category',
                'choice',
                array(
                    'label'       => 'Categorie*',
                    'placeholder' => 'Kies een categorie',
                    'choices'     => array(
                        'Peuters t/m 3 jaar'     => 'Peuters t/m 3 jaar',
                        'Kleuters 4 t/m 6 jaar'  => 'Kleuters 4 t/m 6 jaar',
                        'Meisjes 6 t/m 9 jaar'   => 'Meisjes 6 t/m 9 jaar',
                        'Meisjes 10 t/m 15 jaar' => 'Meisjes 10 t/m 15 jaar',
                        'Jongens 6 t/m 9 jaar'   => 'Jongens 6 t/m 9 jaar',
                        'Jongens 10 t/m 15 jaar' => 'Jongens 10 t/m 15 jaar',
                        'Dames 16 jaar en ouder' => 'Dames 16 jaar en ouder',
                        'Heren 16 jaar en ouder' => 'Heren 16 jaar en ouder',
                        'Selectie dames'         => 'Selectie dames',
                        'Aerobics/Bodyshape'     => 'Aerobics/Bodyshape',
                        'Badminton/Volleybal'    => 'Badminton/Volleybal',
                    ),
                )
            )
            ->add(
                'days',
                'choice',
                array(
                    'label'             => 'Selecteer lesdag(en)*',
                    'multiple'          => true,
                    'expanded'          => true,
                    'choices'           => array(
                        'Maandag'   => 'Maandag',
                        'Dinsdag'   => 'Dinsdag',
                        'Woensdag'  => 'Woensdag',
                        'Donderdag' => 'Donderdag',
                        'Vrijdag'   => 'Vrijdag',
                        'Zaterdag'  => 'Zaterdag',
                        'Zondag'    => 'Zondag',
                    ),
                    'choices_as_values' => true,
                )
            )
            ->add(
                'locations',
                'choice',
                array(
                    'label'             => 'Selecteer locatie(s)*',
                    'multiple'          => true,
                    'expanded'          => true,
                    'choices'           => array(
                        'Oude Bleijk'           => 'Oude Bleijk',
                        'Mari Andriessenstraat' => 'Mari Andriessenstraat',
                        'Turnhal Den Haag GVP'  => 'Turnhal Den Haag GVP',
                        'Turnhal Den Haag ZP'   => 'Turnhal Den Haag ZP',
                        'Zaanstraat'            => 'Zaanstraat',
                        'Renswoudelaan'         => 'Renswoudelaan',
                        '2e Sweelinckstraat'    => '2e Sweelinckstraat',
                        'Den Helderstraat'      => 'Den Helderstraat',
                        'Erasmusweg'            => 'Erasmusweg',
                        'Walenburg'             => 'Walenburg',
                    ),
                    'choices_as_values' => true,
                )
            )
            ->add('starttime', 'time', array('empty_value' => '', 'label' => 'Starttijd les*',))
            ->add('trainer', 'text', array('label' => 'Leiding*',))
            ->add('how', 'textarea', array('required' => false, 'label' => 'Hoe bent u bij Donar terecht gekomen?',))
            ->add('vrijwilligerstaken', 'textarea', array(
                'attr' => array('rows'=> '4', 'cols' => '50'),
                'required' => true,
                'label' => 'Bij aanmelding gaan wij ervan uit dat u zich als meerderjarige of als ouder van een minderjarige 
                1 à 2 maal per jaar wil inzetten als vrijwilliger zodat wij als vereniging wedstrijden en evenementen
                kunnen blijven organiseren. U kunt zich aanmelden als vrijwilliger voor: Telcommissie (optellen
                cijfers tijdens wedstrijden), kassa, catering, bestuur, opbouwen en afbouwen van turntoestellen
                tijdens wedstrijden, juryleden (cursus wordt vergoed!!), onderhoud gebouwen.
                Hieronder kunt u aangeven wat uw voorkeur heeft. Meerdere opties zijn mogelijk:',
                ))
            ->add(
                'accept',
                'checkbox',
                array(
                    'required' => true,
                    'label'    => 'Door dit vakje aan te klikken verklaart u op de hoogte te zijn van de voorwaarden die behoren bij het lidmaatschap 
van DONAR, te vinden onder het kopje formulieren in het lidmaatschap menu. U verklaart deze te zullen aannemen en de plichten, die aan het lidmaatschap verbonden zijn, altijd te zullen nakomen.
Ook gaat u akkoord met de doorlopende incasso, ook te vinden onder het kopje formulieren.',
                )
            )
            ->add(
                'acceptPrivacyPolicy',
                'checkbox',
                array(
                    'required' => true,
                    'label'    => 'Door dit vakje aan te klikken verklaart u op de hoogte te zijn van het privacy beleid van de vereniging, en hiermee akkoord te gaan. Het privacy beleid is onder aan deze pagina te vinden.',
                )
            )
            ->add(
                'acceptNamePublished',
                'choice',
                array(
                    'required' => true,
                    'label'    => 'Gaat u ermee akkoord dat de naam van het aankomend lid gepubliceerd wordt op de website, in het krantje of op social media?',
                    'choices'  => array(
                        'Ja' => true,
                        'Nee' => false,
                    ),
                    'choices_as_values' => true,
                    'expanded' => true,
                )
            )
            ->add(
                'acceptPicturesPublished',
                'choice',
                array(
                    'required' => true,
                    'label'    => 'Gaat u ermee akkoord dat beeldmateriaal van het aankomend lid gepubliceerd wordt op de website, in het krantje of op social media?',
                    'choices'  => array(
                        'Ja' => true,
                        'Nee' => false,
                    ),
                    'choices_as_values' => true,
                    'expanded' => true,
                )
            )
            ->add('save', 'submit', array('label' => 'Verstuur formulier'));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'AppBundle\Entity\Inschrijving',
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'inschrijfformulier';
    }
}
