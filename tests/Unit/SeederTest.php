<?php

namespace Tests\Unit;


use App\Models\Group;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeederTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_seeders_create_expected_results()
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertTrue(Group::find(1)->name === 'user');
        $this->assertTrue(Group::find(2)->name === 'administrator');

        $this->assertTrue(User::count() === 3);
    }
}
