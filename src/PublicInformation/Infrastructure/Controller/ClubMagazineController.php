<?php
declare(strict_types=1);

namespace App\PublicInformation\Infrastructure\Controller;

use App\PublicInformation\Domain\ClubMagazine\ClubMagazine;
use App\PublicInformation\Infrastructure\DoctrineDbal\DbalClubMagazineRepository;
use App\PublicInformation\Infrastructure\SymfonyFormType\ClubMagazineType;
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

final class ClubMagazineController
{
    private DbalClubMagazineRepository    $clubMagazineRepository;
    private Environment                   $twig;
    private Filesystem                    $filesystem;
    private LoggerInterface               $logger;
    private FormFactoryInterface          $formFactory;
    private AuthorizationCheckerInterface $authorizationChecker;
    private string                        $uploadLocation;
    private string                        $locationFromWebRoot;

    public function __construct(
        DbalClubMagazineRepository $clubMagazineRepository,
        Environment $twig,
        Filesystem $filesystem,
        LoggerInterface $logger,
        FormFactoryInterface $formFactory,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->clubMagazineRepository = $clubMagazineRepository;
        $this->twig                   = $twig;
        $this->filesystem             = $filesystem;
        $this->logger                 = $logger;
        $this->formFactory            = $formFactory;
        $this->authorizationChecker   = $authorizationChecker;
        $this->uploadLocation         = __DIR__ . '/../../../../public/uploads/clubblad/';
        $this->locationFromWebRoot    = '/uploads/clubblad/';
    }

    /**
     * @Route("/clubblad", name="clubMagazine", methods={"GET", "POST"})
     */
    public function clubMagazine(Request $request): Response
    {
        $form      = null;
        $showModal = false;
        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $form = $this->formFactory->create(ClubMagazineType::class);
            $form->handleRequest($request);
            if ($form->isSubmitted()) {
                if ($form->isValid()) {
                    $uploadedFile = ClubMagazine::createFromForm($form->getData(), $this->uploadLocation);
                    $this->clubMagazineRepository->insert($uploadedFile);

                    return new RedirectResponse($request->headers->get('referer'));
                }
                $showModal = true;
            }
        }

        return new Response(
            $this->twig->render(
                '@PublicInformation/default/club_magazine.html.twig',
                [
                    'clubMagazines'       => $this->clubMagazineRepository->findAll(),
                    'years'               => $this->clubMagazineRepository->findAllYears(),
                    'locationFromWebRoot' => $this->locationFromWebRoot,
                    'form'                => $form ? $form->createView() : null,
                    'showModal'           => $showModal,
                ]
            )
        );
    }

    /**
     * @Route("/clubblad/remove-club-magazine", name="removeClubMagazine", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function removeClubMagazine(Request $request): Response
    {
        $clubMagazine   = $this->clubMagazineRepository->find((int) $request->request->get('id'));
        $fullPathToFile = $this->uploadLocation . $clubMagazine->fileName();
        if ($this->filesystem->exists($fullPathToFile)) {
            try {
                $this->filesystem->remove($fullPathToFile);
                $this->clubMagazineRepository->remove($clubMagazine->id());
            } catch (IOException $exception) {
                $this->logger->log(
                    LogLevel::ERROR,
                    sprintf('something went wrong while removing a club magazine'),
                    ['exception' => $exception]
                );
            }
        }

        return new RedirectResponse($request->headers->get('referer'));
    }
}
