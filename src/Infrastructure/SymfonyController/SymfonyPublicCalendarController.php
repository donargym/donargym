<?php

declare(strict_types=1);

namespace App\Infrastructure\SymfonyController;

use App\Infrastructure\DoctrineDbal\DbalPublicCalendarItemRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

final class SymfonyPublicCalendarController
{
    private Environment $twig;
    private DbalPublicCalendarItemRepository $publicCalendarItemRepository;

    public function __construct(
        Environment $twig,
        DbalPublicCalendarItemRepository $publicCalendarItemRepository
    )
    {
        $this->twig                         = $twig;
        $this->publicCalendarItemRepository = $publicCalendarItemRepository;
    }

    /**
     * @Route("/agenda/{id}/", name="publicCalendarItem", methods={"GET"})
     */
    public function publicCalendarItem(int $id): Response
    {
        $calendarItem = $this->publicCalendarItemRepository->find($id);
        if (!$calendarItem) {
            throw new NotFoundHttpException();
        }

        return new Response($this->twig->render('default/viewCalendar.html.twig', ['calendarItem' => $calendarItem]));
    }
}
