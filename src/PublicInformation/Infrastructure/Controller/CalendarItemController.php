<?php
declare(strict_types=1);

namespace App\PublicInformation\Infrastructure\Controller;

use App\PublicInformation\Domain\Calendar\CalendarItem;
use App\PublicInformation\Infrastructure\DoctrineDbal\DbalCalendarItemRepository;
use App\PublicInformation\Infrastructure\SymfonyFormType\CalendarItemType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

final class CalendarItemController
{
    private Environment                $twig;
    private DbalCalendarItemRepository $publicCalendarItemRepository;
    private FormFactoryInterface       $formFactory;
    private RouterInterface            $router;

    public function __construct(
        Environment $twig,
        DbalCalendarItemRepository $publicCalendarItemRepository,
        FormFactoryInterface $formFactory,
        RouterInterface $router
    ) {
        $this->twig                         = $twig;
        $this->publicCalendarItemRepository = $publicCalendarItemRepository;
        $this->formFactory                  = $formFactory;
        $this->router                       = $router;
    }

    /**
     * @Route("/agenda/details/{id}/", name="publicCalendarItem", methods={"GET"})
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
     * @Route("/agenda/remove-item", name="removePublicCalendarItem", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function removePublicCalendarItem(Request $request): Response
    {
        $this->publicCalendarItemRepository->remove((int) $request->request->get('id'));

        return new RedirectResponse($request->headers->get('referer'));
    }

    /**
     * @Route("/agenda/add-item", name="addPublicCalendarItem", methods={"GET", "POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function addPublicCalendarItem(Request $request): Response
    {
        $form = $this->formFactory->create(CalendarItemType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $calendarItem = CalendarItem::createFromForm($form->getData());
            $this->publicCalendarItemRepository->insert($calendarItem);

            return new RedirectResponse($this->router->generate('newsPosts'));
        }

        return new Response(
            $this->twig->render(
                '@PublicInformation/default/add_or_edit_calendar_item.html.twig',
                ['form' => $form->createView(), 'referer' => $request->headers->get('referer')]
            )
        );
    }

    /**
     * @Route("/agenda/edit-item/{id}", name="editPublicCalendarItem", methods={"GET", "POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function editPublicCalendarItem(Request $request, int $id): Response
    {
        $calendarItem = $this->publicCalendarItemRepository->find($id);
        if (!$calendarItem) {
            throw new NotFoundHttpException();
        }
        $form = $this->formFactory->create(
            CalendarItemType::class,
            [],
            [
                'date'     => $calendarItem->date(),
                'activity' => $calendarItem->name(),
                'location' => $calendarItem->locationDescription(),
                'time'     => $calendarItem->timeDescription(),
            ]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $calendarItem->updateFromForm($form->getData());
            $this->publicCalendarItemRepository->update($calendarItem);

            return new RedirectResponse($this->router->generate('newsPosts'));
        }

        return new Response(
            $this->twig->render(
                '@PublicInformation/default/add_or_edit_calendar_item.html.twig',
                ['form' => $form->createView(), 'referer' => $request->headers->get('referer')]
            )
        );
    }
}
