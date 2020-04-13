<?php
declare(strict_types=1);

namespace App\PublicInformation\Infrastructure\Controller;

use App\PublicInformation\Domain\Picture\Picture;
use App\PublicInformation\Infrastructure\DoctrineDbal\DbalPictureRepository;
use App\PublicInformation\Infrastructure\SymfonyFormType\PictureType;
use App\Shared\Domain\ImageResizer;
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
use Twig\Environment;

/**
 * @IsGranted("ROLE_ADMIN")
 */
final class PictureController
{
    private DbalPictureRepository      $pictureRepository;
    private Environment                $twig;
    private Filesystem                 $filesystem;
    private LoggerInterface            $logger;
    private string                     $locationFromWebRoot;
    private string                     $uploadLocation;
    private FormFactoryInterface       $formFactory;
    private ImageResizer               $imageResizer;

    public function __construct(
        DbalPictureRepository $pictureRepository,
        Environment $twig,
        Filesystem $filesystem,
        LoggerInterface $logger,
        FormFactoryInterface $formFactory,
        ImageResizer $imageResizer
    ) {
        $this->pictureRepository   = $pictureRepository;
        $this->twig                = $twig;
        $this->filesystem          = $filesystem;
        $this->logger              = $logger;
        $this->locationFromWebRoot = '/uploads/fotos/';
        $this->uploadLocation      = __DIR__ . '/../../../../public' . $this->locationFromWebRoot;
        $this->formFactory         = $formFactory;
        $this->imageResizer = $imageResizer;
    }

    /**
     * @Route("/admin/foto/", name="publicPictures", methods={"GET", "POST"})
     */
    public function publicPictures(Request $request): Response
    {
        $showAddPictureModal = false;
        $form = $this->formFactory->create(PictureType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $picture = Picture::createFromForm($form->getData(), $this->uploadLocation, $this->imageResizer);
                $this->pictureRepository->insert($picture);

                return new RedirectResponse($request->headers->get('referer'));
            }

            $showAddPictureModal = true;
        }
        return new Response(
            $this->twig->render(
                '@PublicInformation/admin/pictures.html.twig',
                [
                    'pictures'            => $this->pictureRepository->findAllOrderedAlphabetically(),
                    'locationFromWebRoot' => $this->locationFromWebRoot,
                    'form'                => $form->createView(),
                    'showAddPictureModal' => $showAddPictureModal,
                ]
            )
        );
    }

    /**
     * @Route("/admin/remove-picture/", name="removePicture", methods={"POST"})
     */
    public function removePicture(Request $request): Response
    {
        $picture        = $this->pictureRepository->find((int) $request->request->get('id'));
        $fullPathToFile = $this->uploadLocation . $picture->fileName();
        if ($this->filesystem->exists($fullPathToFile)) {
            try {
                $this->filesystem->remove($fullPathToFile);
                $this->pictureRepository->remove($picture->id());
            } catch (IOException $exception) {
                $this->logger->log(
                    LogLevel::ERROR,
                    sprintf('something went wrong while removing a picture'),
                    ['exception' => $exception]
                );
            }
        }

        return new RedirectResponse($request->headers->get('referer'));
    }
}
