<?php

namespace App\Tests\Entity;

use App\Entity\Campus;
use PHPUnit\Framework\TestCase;

class CampusEntityTest extends TestCase
{
    public function test(): void
    {
        $category = (new Campus())
            ->setNom("jesaispas");
        $this->assertEquals("jesaispas", $category->getNom());
        $this->assertNull($category->getId());
    }
}
