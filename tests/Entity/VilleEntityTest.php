<?php

namespace App\Tests\Entity;

use App\Entity\Ville;
use PHPUnit\Framework\TestCase;

class VilleEntityTest extends TestCase
{
    public function testVilleEntity(): void
    {
        $nom='Nice';
        $codePostal='44441';
        $ville=(new Ville())
        ->setCodePostal($codePostal)
        ->setNom($nom);
        $this->assertEquals($nom, $ville->getNom());
        $this->assertEquals($codePostal, $ville->getCodePostal());
        $this->assertNull($ville->getId());

    }
}
