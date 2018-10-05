<?php

namespace AppBundle\Entity;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityRepository;

final class InschrijvingRepository extends EntityRepository
{
    public function saveInschrijving(Inschrijving $inschrijving, $databaseName, $userName, $password, $dbServerIp)
    {
        /** @noinspection PhpInternalEntityUsedInspection */
        $config = new Configuration();

        $connectionParams = array(
            'dbname'   => $databaseName,
            'user'     => $userName,
            'password' => $password,
            'host'     => $dbServerIp,
            'driver'   => 'pdo_mysql',
            'charset'  => 'utf8'
        );

        $connection = DriverManager::getConnection($connectionParams, $config);

        $sql
            = <<<EOQ
INSERT INTO
  inschrijvingen
SET
  first_name = :first_name,
  lastname = :lastname,
  nameletters = :nameletters,
  dateofbirth = :dateofbirth,
  gender = :gender,
  address = :address,
  postcode = :postcode,
  city = :city,
  phone1 = :phone1,
  phone2 = :phone2,
  bankaccountnumber = :bankaccountnumber,
  bankaccountholder = :bankaccountholder,
  emailaddress = :emailaddress,
  havebeensubscribed = :havebeensubscribed,
  subscribedfrom = :subscribedfrom,
  subscribeduntil = :subscribeduntil,
  otherclub = :otherclub,
  whatotherclub = :whatotherclub,
  bondscontributiebetaald = :bondscontributiebetaald,
  category = :category,
  days = :days,
  locations = :locations,
  starttime = :starttime,
  trainer = :trainer,
  how = :how,
  vrijwilligerstaken = :vrijwilligerstaken,
  accept = :accept,
  accept_privacy_policy = :acceptPrivacyPolicy,
  accept_name_published = :acceptNamePublished,
  accept_pictures_published = :acceptPicturesPublished
EOQ;

        $connection->executeQuery(
            $sql,
            array(
                'first_name'              => $inschrijving->getFirstName(),
                'lastname'                => $inschrijving->getLastname(),
                'nameletters'             => $inschrijving->getNameletters(),
                'dateofbirth'             => $inschrijving->getDateofbirth()->format('Y-m-d h:i'),
                'gender'                  => $inschrijving->getGender(),
                'address'                 => $inschrijving->getAddress(),
                'postcode'                => $inschrijving->getPostcode(),
                'city'                    => $inschrijving->getCity(),
                'phone1'                  => $inschrijving->getPhone1(),
                'phone2'                  => $inschrijving->getPhone2(),
                'bankaccountnumber'       => $inschrijving->getBankaccountnumber(),
                'bankaccountholder'       => $inschrijving->getBankaccountholder(),
                'emailaddress'            => $inschrijving->getEmailaddress(),
                'havebeensubscribed'      => $inschrijving->isHavebeensubscribed(),
                'subscribedfrom'          => $inschrijving->getSubscribedfrom() ? $inschrijving->getSubscribedfrom()->format('Y-m-d h:i') : null,
                'subscribeduntil'         => $inschrijving->getSubscribeduntil() ? $inschrijving->getSubscribeduntil()->format('Y-m-d h:i') : null,
                'otherclub'               => $inschrijving->isOtherclub(),
                'whatotherclub'           => $inschrijving->getWhatotherclub(),
                'bondscontributiebetaald' => $inschrijving->isBondscontributiebetaald(),
                'category'                => $inschrijving->getCategory(),
                'days'                    => serialize($inschrijving->getDays()),
                'locations'               => serialize($inschrijving->getLocations()),
                'starttime'               => $inschrijving->getStarttime(),
                'trainer'                 => $inschrijving->getTrainer(),
                'how'                     => $inschrijving->getHow(),
                'vrijwilligerstaken'      => $inschrijving->getVrijwilligerstaken(),
                'accept'                  => $inschrijving->isAccept(),
                'acceptPrivacyPolicy'     => $inschrijving->isacceptPrivacyPolicy(),
                'acceptNamePublished'     => $inschrijving->isacceptNamePublished(),
                'acceptPicturesPublished' => $inschrijving->isacceptPicturesPublished(),
            )
        );
    }
}
