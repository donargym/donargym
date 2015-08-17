<?php

namespace AppBundle\Controller;

use AppBundle\Entity\FileUpload;
use AppBundle\Entity\FotoUpload;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Httpfoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Entity\Content;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


/**
 * @Security("has_role('ROLE_ADMIN')")
 */
class AdminController extends BaseController
{
    protected $header;
    protected $calendarItems;

    public function __construct()
    {
    }

    /**
     * @Route("/admin/", name="getAdminIndexPage")
     * @Method("GET")
     */
    public function getIndexPageAction()
    {
        $this->header = 'bannerhome'.rand(1,2);
        $this->calendarItems = $this->getCalendarItems();
        return $this->render('inloggen/adminIndex.html.twig', array(
            'calendarItems' => $this->calendarItems,
            'header' => $this->header
        ));
    }

    /**
     * @Route("/admin/foto/", name="getAdminFotoPage")
     * @Method("GET")
     */
    public function getAdminFotoPage()
    {
        $this->header = 'bannerhome'.rand(1,2);
        $this->calendarItems = $this->getCalendarItems();
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT fotoupload
                FROM AppBundle:FotoUpload fotoupload
                ORDER BY fotoupload.naam');
        $content = $query->getResult();
        $contentItems = array();
        for($i=0;$i<count($content);$i++)
        {
            $contentItems[$i] = $content[$i]->getAll();
        }
        return $this->render('inloggen/adminFotos.html.twig', array(
            'contentItems' => $contentItems,
            'calendarItems' => $this->calendarItems,
            'header' => $this->header
        ));
    }

    /**
     * @Template()
     * @Route("/admin/foto/add/", name="addAdminFotoPage")
     * @Method({"GET", "POST"})
     */
    public function addAdminFotoPageAction(Request $request)
    {
        $this->header = 'bannerhome'.rand(1,2);
        $this->calendarItems = $this->getCalendarItems();
        $foto = new FotoUpload();
        $form = $this->createFormBuilder($foto)
            ->add('naam')
            ->add('file')
            ->add('uploadBestand', 'submit')
            ->getForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($foto);
            $em->flush();
            $this->get('helper.imageresizer')->resizeImage($foto->getAbsolutePath(), $foto->getUploadRootDir()."/" , null, $width=597);
            return $this->redirectToRoute('getAdminFotoPage');
        }
        else {
            return $this->render('inloggen/addAdminFotos.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header,
                'form' => $form->createView(),
            ));
        }
    }

    /**
     * @Route("/admin/foto/remove/{id}/", name="removeAdminFotoPage")
     * @Method({"GET", "POST"})
     */
    public function removeAdminFotoPage($id, Request $request)
    {
        if($request->getMethod() == 'GET')
        {
            $this->header = 'bannerhome'.rand(1,2);
            $this->calendarItems = $this->getCalendarItems();
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT fotoupload
                FROM AppBundle:FotoUpload fotoupload
                WHERE fotoupload.id = :id')
                ->setParameter('id', $id);
            $foto = $query->setMaxResults(1)->getOneOrNullResult();
            if(count($foto) > 0)
            {
                return $this->render('inloggen/removeAdminFotos.html.twig', array(
                    'calendarItems' => $this->calendarItems,
                    'header' => $this->header,
                    'content' => $foto->getAll(),
                ));
            }
            else
            {
                return $this->render('error/pageNotFound.html.twig', array(
                    'calendarItems' => $this->calendarItems,
                    'header' => $this->header
                ));
            }
        }
        elseif($request->getMethod() == 'POST')
        {
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT fotoupload
                FROM AppBundle:FotoUpload fotoupload
                WHERE fotoupload.id = :id')
                ->setParameter('id', $id);
            $foto = $query->setMaxResults(1)->getOneOrNullResult();
            $em->remove($foto);
            $em->flush();
            return $this->redirectToRoute('getAdminFotoPage');
        }
        else
        {
            return $this->render('error/pageNotFound.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header
            ));
        }
    }

    /**
     * @Route("/admin/bestanden/", name="getAdminBestandenPage")
     * @Method("GET")
     */
    public function getAdminBestandenPage()
    {
        $this->header = 'bannerhome'.rand(1,2);
        $this->calendarItems = $this->getCalendarItems();
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT fileupload
                FROM AppBundle:FileUpload fileupload
                ORDER BY fileupload.naam');
        $content = $query->getResult();
        $contentItems = array();
        for($i=0;$i<count($content);$i++)
        {
            $contentItems[$i] = $content[$i]->getAll();
        }
        return $this->render('inloggen/adminUploads.html.twig', array(
            'contentItems' => $contentItems,
            'calendarItems' => $this->calendarItems,
            'header' => $this->header
        ));
    }

    /**
     * @Template()
     * @Route("/admin/bestanden/add/", name="addAdminBestandenPage")
     * @Method({"GET", "POST"})
     */
    public function addAdminBestandenPageAction(Request $request)
    {
        $this->header = 'bannerhome'.rand(1,2);
        $this->calendarItems = $this->getCalendarItems();
        $file = new FileUpload();
        $form = $this->createFormBuilder($file)
            ->add('naam')
            ->add('file')
            ->add('uploadBestand', 'submit')
            ->getForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($file);
            $em->flush();
            return $this->redirectToRoute('getAdminBestandenPage');
        }
        else {
            return $this->render('inloggen/addAdminUploads.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header,
                'form' => $form->createView(),
            ));
        }
    }

    /**
     * @Route("/admin/bestanden/remove/{id}/", name="removeAdminBestandenPage")
     * @Method({"GET", "POST"})
     */
    public function removeAdminBestandenPage($id, Request $request)
    {
        if($request->getMethod() == 'GET')
        {
            $this->header = 'bannerhome'.rand(1,2);
            $this->calendarItems = $this->getCalendarItems();
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT fileupload
                FROM AppBundle:FileUpload fileupload
                WHERE fileupload.id = :id')
                ->setParameter('id', $id);
            $file = $query->setMaxResults(1)->getOneOrNullResult();
            if(count($file) > 0)
            {
                return $this->render('inloggen/removeAdminUploads.html.twig', array(
                    'calendarItems' => $this->calendarItems,
                    'header' => $this->header,
                    'content' => $file->getAll(),
                ));
            }
            else
            {
                return $this->render('error/pageNotFound.html.twig', array(
                    'calendarItems' => $this->calendarItems,
                    'header' => $this->header
                ));
            }
        }
        elseif($request->getMethod() == 'POST')
        {
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT fileupload
                FROM AppBundle:FileUpload fileupload
                WHERE fileupload.id = :id')
                ->setParameter('id', $id);
            $file = $query->setMaxResults(1)->getOneOrNullResult();
            $em->remove($file);
            $em->flush();
            return $this->redirectToRoute('getAdminBestandenPage');
        }
        else
        {
            return $this->render('error/pageNotFound.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header
            ));
        }
    }

    /**
     * @Route("/admin/selectie/", name="getAdminSelectiePage")
     * @Method("GET")
     */
    public function getAdminSelectiePage()
    {
        $this->header = 'bannerhome'.rand(1,2);
        $this->calendarItems = $this->getCalendarItems();
        return $this->render('inloggen/adminSelectie.html.twig', array(
            'calendarItems' => $this->calendarItems,
            'header' => $this->header
        ));
    }
}