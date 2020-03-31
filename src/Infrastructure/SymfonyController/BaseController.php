<?php

namespace App\Infrastructure\SymfonyController;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class BaseController extends AbstractController
{
    protected function addToDB($object)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $em->persist($object);
        $em->flush();
    }

    protected function removeFromDB($object)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($object);
        $em->flush();
    }
}
