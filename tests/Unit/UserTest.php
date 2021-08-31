<?php

namespace Tests\Unit;

use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function users_database_has_expected_columns()
    {
        $expectedColumns = [
            'id',
            'group_id',
            'first_name',
            'last_name',
            'email',
            'email_verified_at',
            'password',
            'remember_token',
            'created_at',
            'updated_at',
            'deleted_at',
        ];

        $this->assertTrue(Schema::hasColumns('users', $expectedColumns));
        // check that no other columns are created
        $this->assertTrue(Schema::getColumnListing('users') === $expectedColumns);
    }

    /** @test */
    public function a_user_belongs_to_a_group()
    {
        /** @var Group $group */
        $group = Group::factory()->create();
        /** @var User $user */
        $user = User::factory()->create(['group_id' => $group->id]);

        $this->assertEquals(1, $user->group->count());
        $this->assertInstanceOf(Group::class, $user->group);
    }
}
