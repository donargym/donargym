<?php
declare(strict_types=1);

namespace App\PublicInformation\Infrastructure\Controller;

use App\PublicInformation\Infrastructure\DoctrineDbal\DbalHolidayRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

final class HolidayController
{
    private DbalHolidayRepository $holidayRepository;
    private Environment           $twig;

    public function __construct(
        DbalHolidayRepository $holidayRepository,
        Environment $twig
    ) {
        $this->holidayRepository = $holidayRepository;
        $this->twig              = $twig;
    }

    /**
     * @Route("/vakanties", name="holidays", methods={"GET"})
     */
    public function holidays(): Response
    {
        return new Response(
            $this->twig->render(
                '@PublicInformation/default/holidays.html.twig',
                ['holidays' => $this->holidayRepository->findCurrentAndFutureHolidays()]
            )
        );
    }
}
