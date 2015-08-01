<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\User;

class LoadUserData implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $userAdmin = new User();
        $userAdmin->setUsername('admin');
        $encoder = $this->container
            ->get('security.encoder_factory')
            ->getEncoder($userAdmin);
        $userAdmin->setPassword($encoder->encodePassword('', $userAdmin->getSalt()));
        $userAdmin->setEmail('webmaster@donargym.nl');
        $userAdmin->setIsActive(true);

        $manager->persist($userAdmin);
        $manager->flush();

        $userSelectie = new User();
        $userSelectie->setUsername('selectie');
        $encoder = $this->container
            ->get('security.encoder_factory')
            ->getEncoder($userSelectie);
        $userSelectie->setPassword($encoder->encodePassword('', $userSelectie->getSalt()));
        $userSelectie->setEmail('selectie@donargym.nl');
        $userSelectie->setIsActive(true);

        $manager->persist($userSelectie);
        $manager->flush();
    }
}