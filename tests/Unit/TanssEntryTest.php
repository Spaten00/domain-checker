<?php

namespace Tests\Unit;

use App\Models\TanssEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\TestCase;

class TanssEntryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_checked_if_the_entry_is_expired()
    {
        $expiredEntry = new TanssEntry();
        $expiredEntry->contract_end = "2015-12-09";
        self::assertTrue($expiredEntry->isExpired());

        $aliveEntry = new TanssEntry();
        $aliveEntry->contract_end = "2122-12-09";
        self::assertFalse($aliveEntry->isExpired());
    }

    /** @test */
    public function it_can_be_checked_if_the_entry_will_expire_soon()
    {
        $expiringEntry = new TanssEntry();
        $expiringEntry->contract_end = now()->addDays(1);
        self::assertTrue($expiringEntry->willExpireSoon());

        $aliveEntry = new TanssEntry();
        $aliveEntry->contract_end = now()->addDays(31);
        self::assertFalse($aliveEntry->willExpireSoon());
    }
}
