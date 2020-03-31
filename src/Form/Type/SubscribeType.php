<?php

namespace App\Form\Type;

use App\Entity\Inschrijving;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SubscribeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('firstname', TextType::class, array('label' => 'Voornaam*'))
            ->add('lastname', TextType::class, array('label' => 'Achternaam*'))
            ->add('nameletters', TextType::class, array('label' => 'Initialen*'))
            ->add(
                'dateofbirth',
                DateType::class,
                array(
                    'empty_data'  => '',
                    'years'       => range(date('Y'), 1908),
                    'format'      => 'dd MM yyyy',
                    'label'       => 'Geboortedatum*'
                )
            )
            ->add(
                'gender',
                ChoiceType::class,
                array(
                    'label'             => 'Geslacht*',
                    'placeholder'       => 'Selecteer een geslacht',
                    'choices'           => array(
                        'Man'   => 'Man',
                        'Vrouw' => 'Vrouw',
                    ),
                )
            )
            ->add('address', TextType::class, array('label' => 'Straat + huisnummer*'))
            ->add('postcode', TextType::class, array('label' => 'Postcode*'))
            ->add('city', TextType::class, array('label' => 'Plaats*'))
            ->add('phone1', TextType::class, array('label' => 'Telefoonnummer*'))
            ->add('phone2', TextType::class, array('required' => false, 'label' => 'Evt. 2e telefoonnummer'))
            ->add('bankaccountnumber', TextType::class, array('label' => 'IBAN nummer*'))
            ->add('bankaccountholder', TextType::class, array('label' => 'Naam rekeninghouder*'))
            ->add('emailaddress', EmailType::class, array('label' => 'Email adres*'))
            ->add(
                'havebeensubscribed',
                ChoiceType::class,
                array(
                    'label'             => 'Bent u eerder lid geweest bij Donar?*',
                    'choices'           => array(
                        'Nee' => 'Nee',
                        'Ja'  => 'ja',
                    ),
                )
            )
            ->add(
                'subscribedfrom',
                DateType::class,
                array(
                    'format'   => 'dd MM yyyy',
                    'required' => false,
                    'label'    => 'Zo ja, van wanneer?',
                    'years'    => range(1908, date('Y')),
                )
            )
            ->add(
                'subscribeduntil',
                DateType::class,
                array(
                    'format'   => 'dd MM yyyy',
                    'required' => false,
                    'label'    => 'Tot wanneer?',
                    'years'    => range(1908, date('Y')),
                )
            )
            ->add(
                'otherclub',
                ChoiceType::class,
                array(
                    'label'             => 'Bent u lid van een andere vereniging?*',
                    'choices'           => array(
                        'Nee' => 'Nee',
                        'Ja'  => 'Ja',
                    ),
                )
            )
            ->add('whatotherclub', TextType::class, array('required' => false, 'label' => 'Zo ja, welke vereniging?'))
            ->add(
                'bondscontributiebetaald',
                ChoiceType::class,
                array(
                    'label'             => 'Heeft u dit jaar bondscontributie betaald?*',
                    'choices'           => array(
                        'Nee' => 'Nee',
                        'Ja'  => 'Ja',
                    ),
                )
            )
            ->add(
                'category',
                ChoiceType::class,
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
                        'Aerobics/Bodyshape'     => 'Aerobics/Bodyshape',
                        'Badminton/Volleybal'    => 'Badminton/Volleybal',
                    ),
                )
            )
            ->add(
                'days',
                ChoiceType::class,
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
                    ),
                )
            )
            ->add(
                'locations',
                ChoiceType::class,
                array(
                    'label'             => 'Selecteer locatie(s)*',
                    'multiple'          => true,
                    'expanded'          => true,
                    'choices'           => array(
                        'Mari Andriessenstraat'  => 'Mari Andriessenstraat',
                        'Zaanstraat'             => 'Zaanstraat',
                        'Renswoudelaan'          => 'Renswoudelaan',
                        '2e Sweelinckstraat'     => '2e Sweelinckstraat',
                        'Den Helderstraat'       => 'Den Helderstraat',
                        'Erasmusweg'             => 'Erasmusweg',
                        'Walenburg'              => 'Walenburg',
                        'Sportcampus Zuiderpark' => 'Sportcampus Zuiderpark',
                    ),
                )
            )
            ->add('starttime', TimeType::class, array('empty_data' => '', 'label' => 'Starttijd les*',))
            ->add(
                'trainer',
                ChoiceType::class,
                array(
                    'label'       => 'Leiding*',
                    'placeholder' => 'Kies een leiding',
                    'choices'     => Inschrijving::trainerOptions(),
                )
            )
            ->add('how', TextareaType::class, array('required' => false, 'label' => 'Hoe bent u bij Donar terecht gekomen?',))
            ->add(
                'vrijwilligerstaken',
                TextareaType::class,
                array(
                    'attr'     => array('rows' => '4', 'cols' => '50'),
                    'required' => true,
                    'label'    => 'Bij aanmelding gaan wij ervan uit dat u zich als meerderjarige of als ouder van een minderjarige 
                1 à 2 maal per jaar wil inzetten als vrijwilliger zodat wij als vereniging wedstrijden en evenementen
                kunnen blijven organiseren. U kunt zich aanmelden als vrijwilliger voor: Telcommissie (optellen
                cijfers tijdens wedstrijden), kassa, catering, bestuur, opbouwen en afbouwen van turntoestellen
                tijdens wedstrijden, juryleden (cursus wordt vergoed!!), onderhoud gebouwen.
                Hieronder kunt u aangeven wat uw voorkeur heeft. Meerdere opties zijn mogelijk:',
                )
            )
            ->add(
                'accept',
                CheckboxType::class,
                array(
                    'required' => true,
                    'label'    => 'Door dit vakje aan te klikken verklaart u op de hoogte te zijn van de voorwaarden die behoren bij het lidmaatschap 
van DONAR, te vinden onder het kopje formulieren in het lidmaatschap menu. U verklaart deze te zullen aannemen en de plichten, die aan het lidmaatschap verbonden zijn, altijd te zullen nakomen.
Ook gaat u akkoord met de doorlopende incasso, ook te vinden onder het kopje formulieren.',
                )
            )
            ->add(
                'acceptPrivacyPolicy',
                CheckboxType::class,
                array(
                    'required' => true,
                    'label'    => 'Door dit vakje aan te klikken verklaart u op de hoogte te zijn van het privacy beleid van de vereniging, en hiermee akkoord te gaan. Het privacy beleid is onder aan deze pagina te vinden.',
                )
            )
            ->add(
                'acceptNamePublished',
                ChoiceType::class,
                array(
                    'required'          => true,
                    'label'             => 'Gaat u ermee akkoord dat de naam van het aankomend lid gepubliceerd wordt op de website, in het krantje of op social media?',
                    'choices'           => array(
                        'Ja'  => true,
                        'Nee' => false,
                    ),
                    'expanded'          => true,
                )
            )
            ->add(
                'acceptPicturesPublished',
                ChoiceType::class,
                array(
                    'required'          => true,
                    'label'             => 'Gaat u ermee akkoord dat beeldmateriaal van het aankomend lid gepubliceerd wordt op de website, in het krantje of op social media?',
                    'choices'           => array(
                        'Ja'  => true,
                        'Nee' => false,
                    ),
                    'expanded'          => true,
                )
            )
            ->add('save', SubmitType::class, array('label' => 'Verstuur formulier'));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'App\Entity\Inschrijving',
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
