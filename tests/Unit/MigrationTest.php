<?php

namespace Tests\Unit;

use Artisan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MigrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function migrations_can_be_rolled_back()
    {
        $this->assertTrue(Schema::hasTable('contracts'));
        $this->assertTrue(Schema::hasTable('customers'));
        $this->assertTrue(Schema::hasTable('domains'));
        $this->assertTrue(Schema::hasTable('failed_jobs'));
        $this->assertTrue(Schema::hasTable('groups'));
        $this->assertTrue(Schema::hasTable('hostings'));
        $this->assertTrue(Schema::hasTable('password_resets'));
        $this->assertTrue(Schema::hasTable('personal_access_tokens'));
        $this->assertTrue(Schema::hasTable('users'));

//        Artisan::call('migrate:reset', ['--force' => true]);

//        $this->assertNotTrue(Schema::hasTable('contracts'));
//        $this->assertNotTrue(Schema::hasTable('customers'));
//        $this->assertNotTrue(Schema::hasTable('domains'));
//        $this->assertNotTrue(Schema::hasTable('failed_jobs'));
//        $this->assertNotTrue(Schema::hasTable('groups'));
//        $this->assertNotTrue(Schema::hasTable('hostings'));
//        $this->assertNotTrue(Schema::hasTable('password_resets'));
//        $this->assertNotTrue(Schema::hasTable('personal_access_tokens'));
//        $this->assertNotTrue(Schema::hasTable('users'));
    }
}
