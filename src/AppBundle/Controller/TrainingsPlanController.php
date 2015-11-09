<?php
namespace AppBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Controller\FpdfController as FPDF;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Security("has_role('ROLE_TRAINER')")
 */
class TrainingsPlanController extends SelectieController
{
    /**
     * @Route("/pdf/test/", name="testPdf")
     * @Method("GET")
     */
    public function testPDFCreation()
    {
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial','B',16);
        $pdf->Cell(40,10,'Hello World!');

        return new Response($pdf->Output(), 200, array(
            'Content-Type' => 'application/pdf'));


    }
}