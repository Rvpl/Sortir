<?php

namespace App\Tests\Entity;

use App\Entity\Sortie;
use PHPUnit\Framework\TestCase;

class SortieEntityTest extends TestCase
{
    public function testSortieEntity(): void
    {
        $nom='cc';
        $dateHeureDebut=new \DateTime();
        $duree="00:00:00";
        $dateLimiteInscription=new \DateTime();
        $nbInscriptionMax=7;
        $infoSortie='je sais pas trop quoi mettre';
        $sortie=(new Sortie())
            ->setNom($nom)
            ->setDateHeureDebut()
            ->setDateLimiteInscription()
            ->setDuree()
            ->setNbInscriptionMax($nbInscriptionMax)
            ->setInfoSortie($infoSortie);
        $this->assertEquals($nom, $sortie->getNom());
        $this->assertEquals($dateHeureDebut, $sortie->getDateHeureDebut());
        $this->assertEquals($dateLimiteInscription, $sortie->getDateLimiteInscription());
        $this->assertEquals($nbInscriptionMax, $sortie->getNbInscriptionMax());
        $this->assertEquals($nom, $sortie->getNom());
        $this->assertEquals($infoSortie, $sortie->getInfoSortie());
        $this->assertNull($sortie->getId());
    }
}
