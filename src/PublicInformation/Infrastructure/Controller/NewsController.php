<?php
declare(strict_types=1);

namespace App\PublicInformation\Infrastructure\Controller;

use App\PublicInformation\Domain\News\NewsPost;
use App\PublicInformation\Infrastructure\DoctrineDbal\DbalNewsPostRepository;
use App\PublicInformation\Infrastructure\SymfonyFormType\NewsPostType;
use App\Shared\Domain\SystemClock;
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

final class NewsController
{
    private DbalNewsPostRepository        $newsPostRepository;
    private Environment                   $twig;
    private FormFactoryInterface          $formFactory;
    private RouterInterface               $router;
    private SystemClock                   $clock;
    private AuthorizationCheckerInterface $authorizationChecker;

    public function __construct(
        DbalNewsPostRepository $newsPostRepository,
        Environment $twig,
        FormFactoryInterface $formFactory,
        RouterInterface $router,
        SystemClock $clock,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->newsPostRepository   = $newsPostRepository;
        $this->twig                 = $twig;
        $this->formFactory          = $formFactory;
        $this->router               = $router;
        $this->clock                = $clock;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @Route("/", name="newsPosts", methods={"GET", "POST"})
     */
    public function newsPosts(Request $request): Response
    {
        $form      = null;
        $showModal = false;
        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $form = $this->formFactory->create(NewsPostType::class);
            $form->handleRequest($request);
            if ($form->isSubmitted()) {
                if ($form->isValid()) {
                    $newsPost = NewsPost::createFromForm($form->getData(), $this->clock);
                    $this->newsPostRepository->insert($newsPost);

                    return new RedirectResponse($this->router->generate('newsPosts'));
                }
                $showModal = true;
            }
        }

        return new Response(
            $this->twig->render(
                '@PublicInformation/default/news.html.twig',
                [
                    'newPosts'  => $this->newsPostRepository->findTenMostRecentNewsPosts(),
                    'form'      => $form ? $form->createView() : null,
                    'showModal' => $showModal,
                ]
            )
        );
    }

    /**
     * @Route("/archief", name="newsArchiveIndex", methods={"GET"})
     */
    public function newsArchiveIndex(): Response
    {
        return new Response(
            $this->twig->render(
                '@PublicInformation/default/archive_index.html.twig',
                ['years' => $this->newsPostRepository->findYearsForArchive()]
            )
        );
    }

    /**
     * @Route("/archief/{year}", name="newsArchiveForYear", methods={"GET"})
     */
    public function newsArchiveForYear(int $year): Response
    {
        return new Response(
            $this->twig->render(
                '@PublicInformation/default/news.html.twig',
                ['newPosts' => $this->newsPostRepository->findNewsPostsForYear($year)]
            )
        );
    }

    /**
     * @Route("/news/remove-news-post", name="removeNewsPost", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function removeNewsPost(Request $request): Response
    {
        $this->newsPostRepository->remove((int) $request->request->get('id'));

        return new RedirectResponse($request->headers->get('referer'));
    }

    /**
     * @Route("/news/edit-news-post/{id}", name="editNewsPost", methods={"GET", "POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function editNewsPost(Request $request, int $id): Response
    {
        $newsPost = $this->newsPostRepository->find($id);
        if (!$newsPost) {
            throw new NotFoundHttpException();
        }
        $form = $this->formFactory->create(
            NewsPostType::class,
            [],
            [
                'title'   => $newsPost->title(),
                'content' => $newsPost->contentForForm(),
            ]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newsPost->updateFromForm($form->getData());
            $this->newsPostRepository->update($newsPost);

            return new RedirectResponse($this->router->generate('newsPosts'));
        }

        return new Response(
            $this->twig->render(
                '@PublicInformation/default/edit_news_post.html.twig',
                ['form' => $form->createView(), 'referer' => $request->headers->get('referer')]
            )
        );
    }
}
