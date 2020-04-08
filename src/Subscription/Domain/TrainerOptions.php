<?php
declare(strict_types=1);

namespace App\Subscription\Domain;

use App\Shared\Domain\EmailAddress;

final class TrainerOptions
{
    private static array $trainerEmailAddresses
        = [
            'Anchella' => 'jufanchella@donargym.nl',
            'Cindy'    => 'jufcindy@donargym.nl',
            'Demi'     => 'jufdemi@donargym.nl',
            'Eric'     => 'Ericcastens@donargym.nl',
            'Ilse'     => 'jufilse@donargym.nl',
            'Loes'     => 'loestrompet@hotmail.com',
            'Martine'  => 'jufmartine@donargym.nl',
            'Merel'    => 'jufmerel@donargym.nl',
            'Rachel'   => 'jufrachel@donargym.nl',
            'Renske'   => 'jufrenske@donargym.nl',
            'Vera'     => 'Verawessels@donargym.nl',
            'Charon'   => 'jufcharon@donargym.nl',
        ];

    public static function trainerOptionsForForm(): array
    {
        return [
            'subscription_form.trainer_not_found' => 'Mijn leiding staat er niet bij',
            'trainer.anchella'                    => 'Anchella',
            'trainer.cindy'                       => 'Cindy',
            'trainer.demi'                        => 'Demi',
            'trainer.eric'                        => 'Eric',
            'trainer.ilse'                        => 'Ilse',
            'trainer.loes'                        => 'Loes',
            'trainer.martine'                     => 'Martine',
            'trainer.merel'                       => 'Merel',
            'trainer.rachel'                      => 'Rachel',
            'trainer.renske'                      => 'Renske',
            'trainer.vera'                        => 'Vera',
            'trainer.charon'                      => 'Charon',
        ];
    }

    public static function findEmailAddressForTrainer(string $trainerName): EmailAddress
    {
        if (isset(self::$trainerEmailAddresses[$trainerName])) {
            return EmailAddress::fromString(self::$trainerEmailAddresses[$trainerName]);
        }

        return EmailAddress::fromString('webmaster@donargym.nl');
    }
}
