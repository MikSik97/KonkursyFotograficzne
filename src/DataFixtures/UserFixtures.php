<?php

namespace App\DataFixtures;

use App\Entity\UserAccounts;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

     public function __construct(UserPasswordEncoderInterface $passwordEncoder)
     {
         $this->passwordEncoder = $passwordEncoder;
     }
    public function load(ObjectManager $manager)
    {
        $user = new UserAccounts();
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'admin'
                     ));
        $user->setEmail("admin@admin.pl");
        $basic_role = $user->getRoles();
        $user->setRoles($basic_role);
        $manager->persist($user);
        $manager->flush();
    }
}
