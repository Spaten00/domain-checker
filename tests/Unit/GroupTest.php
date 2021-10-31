<?php

namespace Tests\Unit;

use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
use Illuminate\Database\Eloquent\Collection;

class GroupTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function groups_table_has_expected_columns()
    {
        $expectedColumns = [
            'id',
            'created_at',
            'updated_at',
            'deleted_at',
            'name',
        ];
        $this->assertTrue(Schema::hasColumns('groups', $expectedColumns));
        // check that no other columns are created
        $this->assertSame(Schema::getColumnListing('groups'), $expectedColumns);
    }

    /** @test */
    public function a_group_has_many_users()
    {
        /** @var Group $group */
        $group = Group::factory()->create();
        $user = User::factory()->create(['group_id' => $group->id]);

        $this->assertTrue($group->users->contains($user));
        $this->assertEquals(1, $group->users->count());
        $this->assertInstanceOf(Collection::class, $group->users);
    }
}
