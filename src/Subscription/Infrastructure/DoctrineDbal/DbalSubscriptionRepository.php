<?php
declare(strict_types=1);

namespace App\Subscription\Infrastructure\DoctrineDbal;

use App\Subscription\Domain\Subscription;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;

final class DbalSubscriptionRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function insert(Subscription $subscription): void
    {
        $this->connection->createQueryBuilder()
            ->insert('inschrijvingen')
            ->values(
                [
                    'first_name'                => ':firstName',
                    'lastname'                  => ':lastName',
                    'nameletters'               => ':nameLetters',
                    'dateofbirth'               => ':dateOfBirth',
                    'gender'                    => ':gender',
                    'address'                   => ':address',
                    'postcode'                  => ':postcode',
                    'city'                      => ':city',
                    'phone1'                    => ':phone1',
                    'phone2'                    => ':phone2',
                    'bankaccountnumber'         => ':bankAccountNumber',
                    'bankaccountholder'         => ':bankAccountHolder',
                    'emailaddress'              => ':emailAddress',
                    'havebeensubscribed'        => ':haveBeenSubscribed',
                    'subscribedfrom'            => ':subscribedFrom',
                    'subscribeduntil'           => ':subscribedUntil',
                    'otherclub'                 => ':otherClub',
                    'whatotherclub'             => ':whatOtherClub',
                    'bondscontributiebetaald'   => ':paidBondContribution',
                    'category'                  => ':category',
                    'days'                      => ':days',
                    'locations'                 => ':locations',
                    'starttime'                 => ':startTime',
                    'trainer'                   => ':trainer',
                    'how'                       => ':how',
                    'vrijwilligerstaken'        => ':voluntaryWork',
                    'accept'                    => ':accept',
                    'accept_privacy_policy'     => ':acceptPrivacyPolicy',
                    'accept_name_published'     => ':acceptNamePublished',
                    'accept_pictures_published' => ':acceptPicturesPublished',
                    'subscribed_at'             => ':subscribedAt',
                    'ooievaarspas'              => ':ooievaarspas',
                ]
            )
            ->setParameters(
                [
                    'firstName'               => $subscription->firstName(),
                    'lastName'                => $subscription->lastname(),
                    'nameLetters'             => $subscription->initials(),
                    'dateOfBirth'             => $subscription->dateofbirth(),
                    'gender'                  => $subscription->gender(),
                    'address'                 => $subscription->address(),
                    'postcode'                => $subscription->postcode(),
                    'city'                    => $subscription->city(),
                    'phone1'                  => $subscription->phone1(),
                    'phone2'                  => $subscription->phone2(),
                    'bankAccountNumber'       => $subscription->bankaccountnumber(),
                    'bankAccountHolder'       => $subscription->bankaccountholder(),
                    'emailAddress'            => $subscription->emailaddress(),
                    'haveBeenSubscribed'      => $subscription->haveBeenSubscribed(),
                    'subscribedFrom'          => $subscription->subscribedfrom()
                        ? $subscription->subscribedfrom()->format('Y-m-d h:i') : null,
                    'subscribedUntil'         => $subscription->subscribeduntil()
                        ? $subscription->subscribeduntil()->format('Y-m-d h:i') : null,
                    'otherClub'               => $subscription->otherClub(),
                    'whatOtherClub'           => $subscription->whatotherclub(),
                    'paidBondContribution'    => $subscription->paidBondContribution(),
                    'category'                => $subscription->category(),
                    'days'                    => json_encode($subscription->days()),
                    'locations'               => json_encode($subscription->locations()),
                    'startTime'               => $subscription->starttime()->format('H:i'),
                    'trainer'                 => $subscription->trainer(),
                    'how'                     => $subscription->how(),
                    'voluntaryWork'           => $subscription->voluntaryWork(),
                    'accept'                  => $subscription->accept(),
                    'acceptPrivacyPolicy'     => (int) $subscription->acceptPrivacyPolicy(),
                    'acceptNamePublished'     => (int) $subscription->acceptNamePublished(),
                    'acceptPicturesPublished' => (int) $subscription->acceptPicturesPublished(),
                    'subscribedAt'            => $subscription->subscribedAt(),
                    'ooievaarspas'            => (int) $subscription->ooievaarspas(),
                ],
                [
                    'subscribedAt'    => Types::DATETIME_IMMUTABLE,
                    'dateOfBirth'     => Types::DATETIME_IMMUTABLE,
                ]
            )
            ->execute();
    }
}
