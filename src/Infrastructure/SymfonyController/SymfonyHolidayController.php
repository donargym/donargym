<?php

declare(strict_types=1);

namespace App\Infrastructure\SymfonyController;

use App\Infrastructure\DoctrineDbal\DbalHolidayRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

final class SymfonyHolidayController
{
    private DbalHolidayRepository $holidayRepository;
    private Environment $twig;

    public function __construct(
        DbalHolidayRepository $holidayRepository,
        Environment $twig
    )
    {
        $this->holidayRepository = $holidayRepository;
        $this->twig = $twig;
    }

    /**
     * @Route("/vakanties", name="holidays", methods={"GET"})
     */
    public function holidays(): Response
    {
        return new Response(
            $this->twig->render(
                'default/holidays.html.twig',
                ['holidays' => $this->holidayRepository->findCurrentAndFutureHolidays()]
            )
        );
    }
}
