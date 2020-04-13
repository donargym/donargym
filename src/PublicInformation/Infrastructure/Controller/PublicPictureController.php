<?php
declare(strict_types=1);

namespace App\PublicInformation\Infrastructure\Controller;

use App\PublicInformation\Infrastructure\DoctrineDbal\DbalPictureRepository;
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

/**
 * @IsGranted("ROLE_ADMIN")
 */
final class PublicPictureController
{
    private DbalPictureRepository      $pictureRepository;
    private Environment                $twig;
    private Filesystem                 $filesystem;
    private LoggerInterface            $logger;
    private string                     $locationFromWebRoot;
    private string                     $uploadLocation;

    public function __construct(
        DbalPictureRepository $pictureRepository,
        Environment $twig,
        Filesystem $filesystem,
        LoggerInterface $logger
    ) {
        $this->pictureRepository   = $pictureRepository;
        $this->twig                = $twig;
        $this->filesystem          = $filesystem;
        $this->logger              = $logger;
        $this->locationFromWebRoot = '/uploads/fotos/';
        $this->uploadLocation      = __DIR__ . '/../../../../public' . $this->locationFromWebRoot;
    }

    /**
     * @Route("/admin/foto/", name="publicPictures", methods={"GET"})
     */
    public function publicPictures(): Response
    {
        return new Response(
            $this->twig->render(
                '@PublicInformation/admin/pictures.html.twig',
                [
                    'pictures'            => $this->pictureRepository->findAllOrderedAlphabetically(),
                    'locationFromWebRoot' => $this->locationFromWebRoot,
                ]
            )
        );
    }

    /**
     * @Route("/clubblad", name="removeClubMagazine", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function removeClubMagazine(Request $request): Response
    {
        $clubMagazine   = $this->pictureRepository->find((int) $request->request->get('id'));
        $fullPathToFile = $this->uploadLocation . $clubMagazine->fileName();
        if ($this->filesystem->exists($fullPathToFile)) {
            try {
                $this->filesystem->remove($fullPathToFile);
                $this->pictureRepository->remove($clubMagazine->id());
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
