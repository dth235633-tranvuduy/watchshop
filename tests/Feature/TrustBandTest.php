<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrustBandTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_shows_animated_trust_band_with_typewriter_and_commitments(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('id="typewriter"', false)
            ->assertSee('Hàng chính hãng 100%')
            ->assertSee('Đền 20 lần nếu bán hàng fake')
            ->assertSee('Bảo hành chính hãng')
            ->assertSee('Lên đến 5 năm toàn quốc')
            ->assertSee('Thương hiệu uy tín')
            ->assertSee('trust-marquee', false)
            ->assertSee('Đổi trả trong 7 ngày');
    }
}
