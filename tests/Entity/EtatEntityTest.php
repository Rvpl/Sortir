<?php

namespace App\Tests\Entity;

use App\Entity\Etat;
use PHPUnit\Framework\TestCase;

class EtatEntityTest extends TestCase
{
    public function test(): void
    {
        $category = (new Etat())
            ->setLibelle("créée");
        $this->assertEquals("créée", $category->getLibelle());
    }
}
