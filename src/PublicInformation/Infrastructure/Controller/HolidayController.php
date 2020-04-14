<?php
declare(strict_types=1);

namespace App\PublicInformation\Infrastructure\Controller;

use App\PublicInformation\Domain\Holiday\Holiday;
use App\PublicInformation\Infrastructure\DoctrineDbal\DbalHolidayRepository;
use App\PublicInformation\Infrastructure\SymfonyFormType\HolidayType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Twig\Environment;

final class HolidayController
{
    private DbalHolidayRepository         $holidayRepository;
    private Environment                   $twig;
    private FormFactoryInterface          $formFactory;
    private RouterInterface               $router;
    private AuthorizationCheckerInterface $authorizationChecker;

    public function __construct(
        DbalHolidayRepository $holidayRepository,
        Environment $twig,
        FormFactoryInterface $formFactory,
        RouterInterface $router,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->holidayRepository    = $holidayRepository;
        $this->twig                 = $twig;
        $this->formFactory          = $formFactory;
        $this->router               = $router;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @Route("/vakanties", name="holidays", methods={"GET", "POST"})
     */
    public function holidays(Request $request): Response
    {
        $form      = null;
        $showModal = false;
        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $form = $this->formFactory->create(HolidayType::class);
            $form->handleRequest($request);
            if ($form->isSubmitted()) {
                if ($form->isValid()) {
                    $holiday = Holiday::createFromForm($form->getData());
                    $this->holidayRepository->insert($holiday);

                    return new RedirectResponse($this->router->generate('holidays'));
                }
                $showModal = true;
            }
        }

        return new Response(
            $this->twig->render(
                '@PublicInformation/default/holidays.html.twig',
                [
                    'holidays'  => $this->holidayRepository->findCurrentAndFutureHolidays(),
                    'form'      => $form ? $form->createView() : null,
                    'showModal' => $showModal,
                ]
            )
        );
    }

    /**
     * @Route("/remove-holiday", name="removeHoliday", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function removeHoliday(Request $request): Response
    {
        $this->holidayRepository->remove((int) $request->request->get('id'));

        return new RedirectResponse($request->headers->get('referer'));
    }

    /**
     * @Route("/vakanties/edit-holiday/{id}", name="editHoliday", methods={"GET", "POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function editHoliday(Request $request, int $id): Response
    {
        $holiday = $this->holidayRepository->find($id);
        if (!$holiday) {
            throw new NotFoundHttpException();
        }
        $form = $this->formFactory->create(
            HolidayType::class,
            [],
            [
                'name'      => $holiday->name(),
                'startDate' => $holiday->startDate(),
                'endDate'   => $holiday->endDate(),
            ]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $holiday->updateFromForm($form->getData());
            $this->holidayRepository->update($holiday);

            return new RedirectResponse($this->router->generate('holidays'));
        }

        return new Response(
            $this->twig->render(
                '@PublicInformation/default/edit_holiday.html.twig',
                ['form' => $form->createView(), 'referer' => $request->headers->get('referer')]
            )
        );
    }
}
