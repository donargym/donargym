<?php
declare(strict_types=1);

namespace App\PublicInformation\Infrastructure\Controller;

use App\PublicInformation\Infrastructure\DoctrineDbal\DbalClubMagazineRepository;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

final class ClubMagazineController
{
    private DbalClubMagazineRepository $clubMagazineRepository;
    private Environment                $twig;
    private Filesystem                 $filesystem;
    private LoggerInterface            $logger;
    private string                     $uploadLocation;

    public function __construct(
        DbalClubMagazineRepository $clubMagazineRepository,
        Environment $twig,
        Filesystem $filesystem,
        LoggerInterface $logger
    ) {
        $this->clubMagazineRepository = $clubMagazineRepository;
        $this->twig                   = $twig;
        $this->filesystem             = $filesystem;
        $this->logger                 = $logger;
        $this->uploadLocation         = __DIR__ . '/../../../../public/uploads/clubblad/';
    }

    /**
     * @Route("/clubblad", name="clubMagazine", methods={"GET"})
     */
    public function clubMagazine(): Response
    {
        return new Response(
            $this->twig->render(
                '@PublicInformation/default/club_magazine.html.twig',
                [
                    'clubMagazines' => $this->clubMagazineRepository->findAll(),
                    'years'         => $this->clubMagazineRepository->findAllYears(),
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
