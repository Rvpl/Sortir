<?php

namespace App\Tests\Entity;


use App\Entity\Participant;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserEntityTest extends TestCase
{
    public function test(): void
    {
        $mail = "oupsidoupsi@test.fr";
        $mdp = "123456";
        $roles=['ROLE_USER'];
        $nom='poupipapa';
        $prenom='valjean';
        $participant = (new Participant())
            ->setEmail($mail)
            ->setPassword($mdp)
            ->setNom($nom)
            ->setPrenom($prenom)
            ->setRoles($roles);
        $this->assertCount(1, $participant->getRoles());
        $this->assertEquals($prenom, $participant->getPrenom());
        $this->assertEquals($mail, $participant->getEmail());
        $this->assertEquals($mdp, $participant->getPassword());
        $this->assertCount(1, $participant->getRoles());
        $this->assertEquals($nom, $participant->getNom());
        $this->assertNull($participant->getId());
        $this->assertEquals($nom, $participant->getNom());
    }
}
