<?php

namespace App\Controller;

use App\Entity\SendMail;
use App\Entity\SubDoelen;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;


class BaseController extends AbstractController
{
    protected $calendarItems;
    protected $header;
    protected $groepItems;
    protected $groepIds;

    public function getHeader($page = null)
    {
        switch ($page) {
            case 'wedstrijdturnen':
                return 'wedstrijdturnen' . rand(1, 12);
                break;
            case 'recreatie':
                return 'bannerrecreatie' . rand(1, 4);
                break;
            default:
                return 'bannerhome' . rand(1, 4);
                break;
        }
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

    public function getCalendarItems()
    {
        $em            = $this->getDoctrine()->getManager();
        $query         = $em->createQuery(
            'SELECT calendar
                FROM App:Calendar calendar
                WHERE calendar.datum >= :datum
                ORDER BY calendar.datum ASC'
        )
            ->setParameter('datum', date('Y-m-d', time()));
        $calendar      = $query->getResult();
        $calendarItems = array();
        for ($i = 0; $i < count($calendar); $i++) {
            $calendarItems[$i] = $calendar[$i]->getAll();
        }
        return $calendarItems;
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

    protected function addSubDoelenAanPersoon($persoon)
    {
        $em     = $this->getDoctrine()->getManager();
        $query  = $em->createQuery(
            'SELECT doelen
        FROM App:Doelen doelen
        WHERE doelen.trede IS NULL'
        );
        $doelen = $query->getResult();
        foreach ($doelen as $doel) {
            $subdoelEntity = new SubDoelen();
            $subdoelEntity->setDoel($doel);
            $subdoelEntity->setPersoon($persoon);
            $em->persist($subdoelEntity);
            $em->flush();
        }
    }

    protected function setBasicPageData($page = null)
    {
        $wedstrijdLinkItems  = $this->getwedstrijdLinkItems();
        $this->groepItems    = $wedstrijdLinkItems[0];
        $this->groepIds      = $wedstrijdLinkItems[1];
        $this->header        = $this->getHeader($page);
        $this->calendarItems = $this->getCalendarItems();
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
