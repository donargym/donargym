<?php

namespace App\Infrastructure\SymfonyController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

final class GetContentController extends BaseController
{
    private Environment $twig;

    public function __construct(
        Environment $twig
    )
    {
        $this->twig                            = $twig;
    }

    /**
     * @Route("/contact/{page}/", defaults={"page" = "contact"}, name="getContactPage", methods={"GET"})
     */
    public function getContactPageAction($page)
    {
        $em           = $this->getDoctrine()->getManager();
        $query        = $em->createQuery(
            'SELECT veelgesteldevragen
                FROM App:VeelgesteldeVragen veelgesteldevragen
                ORDER BY veelgesteldevragen.id'
        );
        $content      = $query->getResult();
        $contentItems = array();
        for ($i = 0; $i < count($content); $i++) {
            $contentItems[$i] = $content[$i]->getAll();
        }
        return $this->render(
            'contact/veelgesteldeVragen.html.twig',
            array(
                'contentItems' => $contentItems,
            )
        );
    }

    /**
     * @Route("/agenda/view/{id}/", name="getAgendaPage", methods={"GET"})
     */
    public function getAgendaPageAction($id)
    {
        $em      = $this->getDoctrine()->getManager();
        $query   = $em->createQuery(
            'SELECT calendar
                FROM App:Calendar calendar
                WHERE calendar.id = :id'
        )
            ->setParameter('id', $id);
        $content = $query->setMaxResults(1)->getOneOrNullResult();
        if ($content) {
            return $this->render(
                'default/viewCalendar.html.twig',
                array(
                    'content' => $content->getAll(),
                )
            );
        } else {
            return $this->render(
                'error/pageNotFound.html.twig',
                array()
            );
        }
    }
}
