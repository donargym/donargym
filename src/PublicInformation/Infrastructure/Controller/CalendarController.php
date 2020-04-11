<?php
declare(strict_types=1);

namespace App\PublicInformation\Infrastructure\Controller;

use App\PublicInformation\Infrastructure\DoctrineDbal\DbalCalendarItemRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

final class CalendarController
{
    private Environment                $twig;
    private DbalCalendarItemRepository $publicCalendarItemRepository;

    public function __construct(
        Environment $twig,
        DbalCalendarItemRepository $publicCalendarItemRepository
    ) {
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

        return new Response(
            $this->twig->render('@PublicInformation/default/view_calendar.html.twig', ['calendarItem' => $calendarItem])
        );
    }

    /**
     * @Route("/agenda/", name="removePublicCalendarItem", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function removePublicCalendarItem(Request $request): Response
    {
        $this->publicCalendarItemRepository->remove($request->request->get('id'));

        return new RedirectResponse($request->headers->get('referer'));
    }
}
