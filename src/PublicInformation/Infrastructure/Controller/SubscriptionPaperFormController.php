<?php
declare(strict_types=1);

namespace App\PublicInformation\Infrastructure\Controller;

use App\PublicInformation\Domain\Subscription\SubscriptionPaperForm;
use App\PublicInformation\Infrastructure\DoctrineDbal\DbalSubscriptionPaperFormRepository;
use App\PublicInformation\Infrastructure\SymfonyFormType\SubscriptionPaperFormType;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Twig\Environment;

final class SubscriptionPaperFormController
{
    private DbalSubscriptionPaperFormRepository $subscriptionPaperFormRepository;
    private Environment                         $twig;
    private Filesystem                          $filesystem;
    private LoggerInterface                     $logger;
    private string                              $uploadLocation;
    private FormFactoryInterface                $formFactory;
    private AuthorizationCheckerInterface       $authorizationChecker;
    private string                              $locationFromWebRoot;

    public function __construct(
        DbalSubscriptionPaperFormRepository $subscriptionPaperFormRepository,
        Environment $twig,
        Filesystem $filesystem,
        LoggerInterface $logger,
        FormFactoryInterface $formFactory,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->subscriptionPaperFormRepository = $subscriptionPaperFormRepository;
        $this->twig                            = $twig;
        $this->filesystem                      = $filesystem;
        $this->logger                          = $logger;
        $this->formFactory                     = $formFactory;
        $this->authorizationChecker            = $authorizationChecker;
        $this->uploadLocation                  = __DIR__ . '/../../../../public/uploads/formulieren/';
        $this->locationFromWebRoot             = '/uploads/formulieren/';
    }

    /**
     * @Route("/lidmaatschap/formulieren/", name="subscriptionPaperForms", methods={"GET", "POST"})
     */
    public function subscriptionPaperForms(Request $request): Response
    {
        $form      = null;
        $showModal = false;
        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $form = $this->formFactory->create(SubscriptionPaperFormType::class);
            $form->handleRequest($request);
            if ($form->isSubmitted()) {
                if ($form->isValid()) {
                    $subscriptionPaperForm = SubscriptionPaperForm::createFromForm(
                        $form->getData(),
                        $this->uploadLocation
                    );
                    $this->subscriptionPaperFormRepository->insert($subscriptionPaperForm);

                    return new RedirectResponse($request->headers->get('referer'));
                }
                $showModal = true;
            }
        }

        return new Response(
            $this->twig->render(
                '@PublicInformation/subscription/paper_forms.html.twig',
                [
                    'subscriptionPaperForms' => $this->subscriptionPaperFormRepository->findAll(),
                    'locationFromWebRoot'    => $this->locationFromWebRoot,
                    'form'                   => $form ? $form->createView() : null,
                    'showModal'              => $showModal,
                ]
            )
        );
    }

    /**
     * @Route("/lidmaatschap/remove-paper-form", name="removePaperForm", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function removePaperForm(Request $request): Response
    {
        $paperForm      = $this->subscriptionPaperFormRepository->find((int) $request->request->get('id'));
        $fullPathToFile = $this->uploadLocation . $paperForm->fileName();
        if ($this->filesystem->exists($fullPathToFile)) {
            try {
                $this->filesystem->remove($fullPathToFile);
                $this->subscriptionPaperFormRepository->remove($paperForm->id());
            } catch (IOException $exception) {
                $this->logger->log(
                    LogLevel::ERROR,
                    sprintf('something went wrong while removing a paper form'),
                    ['exception' => $exception]
                );
            }
        }

        return new RedirectResponse($request->headers->get('referer'));
    }
}
