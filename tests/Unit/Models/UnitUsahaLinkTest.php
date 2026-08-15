<?php

namespace Tests\Unit\Models;

use App\Models\UnitUsahaLink;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnitUsahaLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_inactive_link_is_not_displayable(): void
    {
        $link = UnitUsahaLink::create([
            'label' => 'Unit Usaha',
            'external_url' => 'https://example.com',
            'is_active' => false,
        ]);

        $this->assertFalse($link->isDisplayable());
    }

    public function test_active_link_without_url_is_not_displayable(): void
    {
        $link = UnitUsahaLink::create([
            'label' => 'Unit Usaha',
            'external_url' => null,
            'is_active' => true,
        ]);

        $this->assertFalse($link->isDisplayable());
    }

    public function test_active_link_with_url_is_displayable(): void
    {
        $link = UnitUsahaLink::create([
            'label' => 'Unit Usaha',
            'external_url' => 'https://example.com',
            'is_active' => true,
        ]);

        $this->assertTrue($link->isDisplayable());
    }
}