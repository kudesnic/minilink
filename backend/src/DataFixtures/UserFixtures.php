<?php

namespace App\DataFixtures;

use App\Entity\User as EntityUser;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{

    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $superAdmin = new EntityUser();
        $superAdmin->setEmail('andrey-super-admin@gmail.com');
        $superAdmin->setPassword($this->encoder->encodePassword($superAdmin, '12345678a'));
        $superAdmin->setName('Andrey');
        $superAdmin->setRoles([User::ROLE_SUPER_ADMIN]);
        $manager->persist($superAdmin);

        $admin = new EntityUser();
        $admin->setEmail('andrey-admin@gmail.com');
        $admin->setPassword($this->encoder->encodePassword($admin, '12345678a'));
        $admin->setName('Andrey');
        $admin->setRoles([User::ROLE_USER]);
        $manager->persist($admin);

        $manager->flush();
    }

}
