<?php

declare(strict_types=1);

namespace App\PublicInformation\Infrastructure\SymfonyController;

use App\PublicInformation\Domain\SimpleContentPage;
use App\PublicInformation\Infrastructure\DoctrineDbal\DbalSimpleContentPageRepository;
use App\PublicInformation\Infrastructure\SymfonyFormType\SimplePageContentType;
use App\Shared\Domain\SystemClock;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

final class SymfonySimpleContentPageController
{
    private DbalSimpleContentPageRepository $simpleContentPageRepository;
    private Environment $twig;
    private FormFactoryInterface $formFactory;
    private RouterInterface $router;
    private SystemClock $clock;

    public function __construct(
        DbalSimpleContentPageRepository $simpleContentPageRepository,
        Environment $twig,
        FormFactoryInterface $formFactory,
        RouterInterface $router,
        SystemClock $clock
    )
    {
        $this->simpleContentPageRepository = $simpleContentPageRepository;
        $this->twig                        = $twig;
        $this->formFactory                 = $formFactory;
        $this->router                      = $router;
        $this->clock                       = $clock;
    }

    /**
     * @Route("/page/{pageName}/", name="simpleContentPage", methods={"GET"})
     */
    public function simpleContentPage(string $pageName): Response
    {
        $simpleContentPage = $this->simpleContentPageRepository->getMostRecentContentForPage($pageName);
        if (!$simpleContentPage) {
            throw new NotFoundHttpException();
        }

        return new Response(
            $this->twig->render(
                'default/simple_content_page.html.twig',
                ['content' => $simpleContentPage->pageContent()]
            )
        );
    }

    /**
     * @Route("/editPage/{pageName}/", name="editSimpleContentPage", methods={"GET", "POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function editSimpleContentPage(Request $request, string $pageName): Response
    {
        $simpleContentPage = $this->simpleContentPageRepository->getMostRecentContentForPage($pageName);
        if (!$simpleContentPage) {
            throw new NotFoundHttpException();
        }

        $form = $this->formFactory->create(
            SimplePageContentType::class,
            null,
            ['content' => $simpleContentPage->pageContent()]
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $simpleContentPage = SimpleContentPage::createNew($pageName, $form->getData()['pageContent'], $this->clock);
            $this->simpleContentPageRepository->insert($simpleContentPage);

            return new RedirectResponse($this->router->generate('simpleContentPage', ['pageName' => $pageName]));
        }

        return new Response(
            $this->twig->render(
                'default/edit_simple_content_page.html.twig',
                ['content' => $simpleContentPage->pageContent(), 'form' => $form->createView()]
            )
        );
    }
}
