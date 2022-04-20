<?php

namespace App\Tests\Entity;

use App\Entity\Etat;
use PHPUnit\Framework\TestCase;

class EtatEntityTest extends TestCase
{
    public function test(): void
    {
        $etat = (new Etat())
            ->setLibelle("créée");
        $this->assertEquals("créée", $etat->getLibelle());
    }
}
