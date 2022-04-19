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
        $utilisateur = (new Participant())
            ->setEmail($mail)
            ->setPassword($mdp)
            ->setNom($nom)
            ->setPrenom($prenom)
            ->setRoles($roles);
        $this->assertCount(1, $utilisateur->getRoles());
        $this->assertEquals($prenom, $utilisateur->getPrenom());
        $this->assertEquals($mail, $utilisateur->getEmail());
        $this->assertEquals($mdp, $utilisateur->getPassword());
        $this->assertCount(1, $utilisateur->getRoles());
        $this->assertEquals($nom, $utilisateur->getNom());
        $this->assertNull($utilisateur->getId());
        $this->assertEquals($nom, $utilisateur->getNom());
    }
}
