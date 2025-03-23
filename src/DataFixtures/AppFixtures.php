<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $director = new User();
        $director->setEmail('director@email.fr');

        $password = $this->hasher->hashPassword($director, '123');
        $director->setPassword($password);

        $director->setName('Director');
        $director->setSurname('Director');
        $director->setRoles(['ROLE_DIRECTOR']);

        $veterinarian = new User();
        $veterinarian->setEmail('veterinarian@email.fr');

        $password = $this->hasher->hashPassword($veterinarian, '123');
        $veterinarian->setPassword($password);

        $veterinarian->setName('Veterinarian');
        $veterinarian->setSurname('Veterinarian');
        $veterinarian->setRoles(['ROLE_VETERINARIAN']);

        $assistant = new User();
        $assistant->setEmail('assistant@email.fr');

        $password = $this->hasher->hashPassword($assistant, '123');
        $assistant->setPassword($password);

        $assistant->setName('assistant');
        $assistant->setSurname('assistant');
        $assistant->setRoles(['ROLE_ASSISTANT']);

        $manager->persist($director);
        $manager->persist($veterinarian);
        $manager->persist($assistant);
        $manager->flush();
    }
}
