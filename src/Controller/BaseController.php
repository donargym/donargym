<?php

namespace App\Controller;

use App\Domain\SystemClock;
use App\Entity\SendMail;
use App\Repository\DbalCompactCalendarItemRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;

abstract class BaseController extends AbstractController
{
    protected $groepItems;
    protected $groepIds;

    private DbalCompactCalendarItemRepository $compactCalendarItemRepository;
    private SystemClock $clock;

    public function __construct(DbalCompactCalendarItemRepository $compactCalendarItemRepository, SystemClock $clock)
    {
        $this->compactCalendarItemRepository = $compactCalendarItemRepository;
        $this->clock                         = $clock;
    }

    public function getwedstrijdLinkItems()
    {
        $em    = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT groepen
                FROM App:Groepen groepen
                ORDER BY groepen.id ASC'
        );

        $groepen = $query->getResult();
        if (count($groepen) > 0) {
            $groepItems = array();
            $groepId    = array();
            for ($i = 0; $i < count($groepen); $i++) {
                $groepItems[$i] = $groepen[$i]->getIdName();
                $groepId[]      = $groepen[$i]->getId();
            }
        }
        return array($groepItems, $groepId);
    }

    public function maand($maandNummer)
    {
        switch ($maandNummer) {
            case '01':
                return 'Januari';
                break;
            case '02':
                return 'Februari';
                break;
            case '03':
                return 'Maart';
                break;
            case '04':
                return 'April';
                break;
            case '05':
                return 'Mei';
                break;
            case '06':
                return 'Juni';
                break;
            case '07':
                return 'Juli';
                break;
            case '08':
                return 'Augustus';
                break;
            case '09':
                return 'September';
                break;
            case '10':
                return 'Oktober';
                break;
            case '11':
                return 'November';
                break;
            case '12':
                return 'December';
                break;
        }
    }

    public function generatePassword($length = 8)
    {
        $password  = "";
        $possible  = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";
        $maxlength = strlen($possible);
        if ($length > $maxlength) {
            $length = $maxlength;
        }
        $i = 0;
        while ($i < $length) {
            $char = substr($possible, mt_rand(0, $maxlength - 1), 1);
            if (!strstr($password, $char)) {
                $password .= $char;
                $i++;
            }
        }
        return $password;
    }

    protected function setBasicPageData()
    {
        $wedstrijdLinkItems  = $this->getwedstrijdLinkItems();
        $this->groepItems    = $wedstrijdLinkItems[0];
        $this->groepIds      = $wedstrijdLinkItems[1];
    }

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

    /**
     * @param string $subject
     * @param string $to
     * @param string $view twig template
     * @param array  $parameters
     * @param string $from
     */
    protected function sendEmail($subject, $to, $view, MailerInterface $mailer, array $parameters = array(), $from = 'noreply@donargym.nl')
    {

        $message = new TemplatedEmail();
        $message->subject($subject)
            ->from($from)
            ->to($to)
            ->textTemplate($view)
            ->context(['parameters' => $parameters]);

        $mailer->send($message);

        $sendMail = new SendMail();
        $sendMail->setDatum(new \DateTime())
            ->setVan($from)
            ->setAan($to)
            ->setOnderwerp($subject)
            ->setBericht($this->renderView($message->getTextTemplate(), $message->getContext()));
        $this->addToDB($sendMail);
    }
}
