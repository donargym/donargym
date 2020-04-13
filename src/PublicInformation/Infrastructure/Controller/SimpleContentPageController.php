<?php
declare(strict_types=1);

namespace App\PublicInformation\Infrastructure\Controller;

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

final class SimpleContentPageController
{
    private DbalSimpleContentPageRepository $simpleContentPageRepository;
    private Environment                     $twig;
    private FormFactoryInterface            $formFactory;
    private RouterInterface                 $router;
    private SystemClock                     $clock;

    public function __construct(
        DbalSimpleContentPageRepository $simpleContentPageRepository,
        Environment $twig,
        FormFactoryInterface $formFactory,
        RouterInterface $router,
        SystemClock $clock
    ) {
        $this->simpleContentPageRepository = $simpleContentPageRepository;
        $this->twig                        = $twig;
        $this->formFactory                 = $formFactory;
        $this->router                      = $router;
        $this->clock                       = $clock;
    }

    /**
     * @Route("/page/{pageName}/", name="simpleContentPage", methods={"GET", "POST"})
     */
    public function simpleContentPage(Request $request, string $pageName): Response
    {
        $simpleContentPage = $this->simpleContentPageRepository->getMostRecentContentForPage($pageName);
        if (!$simpleContentPage) {
            throw new NotFoundHttpException();
        }
        $showForm = false;
        $form = $this->formFactory->create(
            SimplePageContentType::class,
            null,
            ['content' => $simpleContentPage->pageContent()]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $simpleContentPage = SimpleContentPage::createNew($pageName, $form->getData()['pageContent'], $this->clock);
                $this->simpleContentPageRepository->insert($simpleContentPage);

                return new RedirectResponse($request->headers->get('referer'));
            }
            $showForm = true;
        }

        return new Response(
            $this->twig->render(
                '@PublicInformation/default/simple_content_page.html.twig',
                [
                    'content'  => $simpleContentPage->pageContent(),
                    'form'     => $form->createView(),
                    'showForm' => $showForm,
                ]
            )
        );
    }
}
