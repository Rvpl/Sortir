<?php

namespace App\Tests\Entity;

use App\Entity\Lieu;
use App\Entity\Ville;
use PHPUnit\Framework\TestCase;

class LieuEntityTest extends TestCase
{
    public function test(): void
    {
        $lieu = (new Lieu())
            ->setNom("jesaispas")
            ->setLatitude('1')
            ->setLongitude('1')
            ->setRue('Nantes');
        $this->assertEquals("jesaispas", $lieu->getNom());
        $this->assertEquals("1", $lieu->getLongitude());
        $this->assertEquals("1", $lieu->getLatitude());
        $this->assertEquals("Nantes", $lieu->getRue());
        $this->assertNull($lieu->getId());
    }
}
