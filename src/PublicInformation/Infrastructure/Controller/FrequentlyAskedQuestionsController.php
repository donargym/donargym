<?php
declare(strict_types=1);

namespace App\PublicInformation\Infrastructure\Controller;

use App\PublicInformation\Domain\FrequentlyAskedQuestion\FrequentlyAskedQuestion;
use App\PublicInformation\Infrastructure\DoctrineDbal\DbalFrequentlyAskedQuestionRepository;
use App\PublicInformation\Infrastructure\SymfonyFormType\FrequentlyAskedQuestionType;
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

final class FrequentlyAskedQuestionsController
{
    private Environment                           $twig;
    private DbalFrequentlyAskedQuestionRepository $frequentlyAskedQuestionRepository;
    private FormFactoryInterface                  $formFactory;
    private RouterInterface                       $router;
    private AuthorizationCheckerInterface         $authorizationChecker;

    public function __construct(
        Environment $twig,
        DbalFrequentlyAskedQuestionRepository $frequentlyAskedQuestionRepository,
        FormFactoryInterface $formFactory,
        RouterInterface $router,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->twig                              = $twig;
        $this->frequentlyAskedQuestionRepository = $frequentlyAskedQuestionRepository;
        $this->formFactory                       = $formFactory;
        $this->router                            = $router;
        $this->authorizationChecker              = $authorizationChecker;
    }

    /**
     * @Route("/contact/veelgestelde-vragen/", name="frequentlyAskedQuestions", methods={"GET", "POST"})
     */
    public function frequentlyAskedQuestions(Request $request): Response
    {
        $form      = null;
        $showModal = false;
        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $form = $this->formFactory->create(FrequentlyAskedQuestionType::class);
            $form->handleRequest($request);
            if ($form->isSubmitted()) {
                if ($form->isValid()) {
                    $frequentlyAskedQuestion = FrequentlyAskedQuestion::createFromForm($form->getData());
                    $this->frequentlyAskedQuestionRepository->insert($frequentlyAskedQuestion);

                    return new RedirectResponse($this->router->generate('frequentlyAskedQuestions'));
                }
                $showModal = true;
            }
        }

        return new Response(
            $this->twig->render(
                '@PublicInformation/contact/frequently_asked_questions.html.twig',
                [
                    'questions' => $this->frequentlyAskedQuestionRepository->findAll(),
                    'form'      => $form ? $form->createView() : null,
                    'showModal' => $showModal,
                ]
            )
        );
    }

    /**
     * @Route("/contact/remove-veelgestelde-vraag/", name="removeFrequentlyAskedQuestion", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function removeFrequentlyAskedQuestion(Request $request): Response
    {
        $this->frequentlyAskedQuestionRepository->remove((int) $request->request->get('id'));

        return new RedirectResponse($request->headers->get('referer'));
    }

    /**
     * @Route("/contact/edit-frequently-asked-question/{id}", name="editFrequentlyAskedQuestion", methods={"GET", "POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function editFrequentlyAskedQuestion(Request $request, int $id): Response
    {
        $frequentlyAskedQuestion = $this->frequentlyAskedQuestionRepository->find($id);
        if (!$frequentlyAskedQuestion) {
            throw new NotFoundHttpException();
        }
        $form = $this->formFactory->create(
            FrequentlyAskedQuestionType::class,
            [],
            [
                'question' => $frequentlyAskedQuestion->question(),
                'answer'   => $frequentlyAskedQuestion->answer(),
            ]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $frequentlyAskedQuestion->updateFromForm($form->getData());
            $this->frequentlyAskedQuestionRepository->update($frequentlyAskedQuestion);

            return new RedirectResponse($this->router->generate('frequentlyAskedQuestions'));
        }

        return new Response(
            $this->twig->render(
                '@PublicInformation/contact/edit_frequently_asked_question.html.twig',
                ['form' => $form->createView(), 'referer' => $request->headers->get('referer')]
            )
        );
    }
}
