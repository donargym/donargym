<?php
declare(strict_types=1);

namespace App\Subscription\Infrastructure\Controller;

use App\Shared\Domain\SystemClock;
use App\Subscription\Domain\Subscription;
use App\Subscription\Infrastructure\DoctrineDbal\DbalSubscriptionRepository;
use App\Subscription\Infrastructure\SymfonyFormType\SubscribeType;
use App\Subscription\Infrastructure\SymfonyMailer\SymfonyNewMemberConfirmationMailer;
use App\Subscription\Infrastructure\SymfonyMailer\SymfonyNotifyMemberSecretariatAboutNewMemberMailer;
use App\Subscription\Infrastructure\SymfonyMailer\SymfonyNotifyTrainerAboutNewMemberMailer;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

final class SubscriptionController
{
    private DbalSubscriptionRepository                         $subscriptionRepository;
    private FormFactoryInterface                               $formFactory;
    private Environment                                        $twig;
    private SessionInterface                                   $session;
    private RouterInterface                                    $router;
    private SystemClock                                        $clock;
    private SymfonyNewMemberConfirmationMailer                 $newMemberConfirmationMailer;
    private SymfonyNotifyMemberSecretariatAboutNewMemberMailer $notifyMemberSecretariatAboutNewMemberMailer;
    private SymfonyNotifyTrainerAboutNewMemberMailer           $notifyTrainerAboutNewMemberMailer;

    public function __construct(
        DbalSubscriptionRepository $subscriptionRepository,
        FormFactoryInterface $formFactory,
        Environment $twig,
        SessionInterface $session,
        RouterInterface $router,
        SystemClock $clock,
        SymfonyNewMemberConfirmationMailer $newMemberConfirmationMailer,
        SymfonyNotifyMemberSecretariatAboutNewMemberMailer $notifyMemberSecretariatAboutNewMemberMailer,
        SymfonyNotifyTrainerAboutNewMemberMailer $notifyTrainerAboutNewMemberMailer
    ) {
        $this->subscriptionRepository                      = $subscriptionRepository;
        $this->formFactory                                 = $formFactory;
        $this->twig                                        = $twig;
        $this->session                                     = $session;
        $this->router                                      = $router;
        $this->clock                                       = $clock;
        $this->newMemberConfirmationMailer                 = $newMemberConfirmationMailer;
        $this->notifyMemberSecretariatAboutNewMemberMailer = $notifyMemberSecretariatAboutNewMemberMailer;
        $this->notifyTrainerAboutNewMemberMailer           = $notifyTrainerAboutNewMemberMailer;
    }

    /**
     * @Route("/inschrijven/", name="subscribe", methods={"GET", "POST"})
     */
    public function subscribe(Request $request)
    {
        $form = $this->formFactory->create(SubscribeType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $subscription = Subscription::createFromForm($form->getData(), $this->clock);
            $this->subscriptionRepository->insert($subscription);
            $this->notifyMemberSecretariatAboutNewMemberMailer->notify($subscription);
            $this->notifyTrainerAboutNewMemberMailer->notify($subscription);
            $this->newMemberConfirmationMailer->notify($subscription);
            $successMessage = 'Inschrijving succesvol verstuurd';
            $this->session->getFlashBag()->add('success', $successMessage);

            return new RedirectResponse($this->router->generate('newsPosts'));
        }

        return new Response(
            $this->twig->render(
                '@Subscription/subscribe.html.twig',
                ['form' => $form->createView()]
            )
        );
    }
}
