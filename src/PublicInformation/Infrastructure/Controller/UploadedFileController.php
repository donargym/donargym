<?php
declare(strict_types=1);

namespace App\PublicInformation\Infrastructure\Controller;

use App\PublicInformation\Infrastructure\DoctrineDbal\DbalUploadedFileRepository;
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
final class UploadedFileController
{
    private DbalUploadedFileRepository      $uploadedFileRepository;
    private Environment                     $twig;
    private Filesystem                      $filesystem;
    private LoggerInterface                 $logger;
    private string                          $locationFromWebRoot;
    private string                          $uploadLocation;

    public function __construct(
        DbalUploadedFileRepository $uploadedFileRepository,
        Environment $twig,
        Filesystem $filesystem,
        LoggerInterface $logger
    ) {
        $this->uploadedFileRepository = $uploadedFileRepository;
        $this->twig                   = $twig;
        $this->filesystem             = $filesystem;
        $this->logger                 = $logger;
        $this->locationFromWebRoot    = '/uploads/files/';
        $this->uploadLocation         = __DIR__ . '/../../../../public' . $this->locationFromWebRoot;
    }

    /**
     * @Route("/admin/file/", name="publicFiles", methods={"GET"})
     */
    public function publicFiles(): Response
    {
        return new Response(
            $this->twig->render(
                '@PublicInformation/admin/files.html.twig',
                [
                    'files'               => $this->uploadedFileRepository->findAllOrderedAlphabetically(),
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
        $clubMagazine   = $this->uploadedFileRepository->find((int) $request->request->get('id'));
        $fullPathToFile = $this->uploadLocation . $clubMagazine->fileName();
        if ($this->filesystem->exists($fullPathToFile)) {
            try {
                $this->filesystem->remove($fullPathToFile);
                $this->uploadedFileRepository->remove($clubMagazine->id());
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
