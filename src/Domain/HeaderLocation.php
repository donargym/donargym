<?php

declare(strict_types=1);

namespace App\Domain;

final class HeaderLocation
{
    public function getHeader($page = null)
    {
        switch ($page) {
            case 'wedstrijdturnen':
                return 'bannerwedstrijdturnen' . rand(1, 12);
                break;
            case 'recreatie':
                return 'bannerrecreatie' . rand(1, 4);
                break;
            default:
                return 'bannerhome' . rand(1, 4);
                break;
        }
    }
}
