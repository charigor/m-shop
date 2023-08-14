<?php

namespace Tests\Feature\Role;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;
class IndexRoleTest extends TestCase
{
    use RefreshDatabase,WithFaker;
    /**
     * A basic test example.
     */
    public function test_admin_role_has_access_to_the_role_index_page(): void
    {
        $user = User::factory()->create();
        $role = Role::create( ['name' => 'admin']);
        $user->assignRole($role->name);
        $response = $this->actingAs($user)->get(route('role.index'));
        $response->assertStatus(200);
    }

    public function test_any_role_except_admin_has_not_access_to_the_role_index_page(): void
    {
        $user =  User::factory()->create();
        $role1 = Role::create(['name' => 'manager']);
        $role2 = Role::create(['name' => 'user']);
        $role3 = Role::create(['name' => 'default']);
        $role4 = Role::create(['name' => $this->faker->title]);
        $role5 = Role::create(['name' => '']);
        $user->assignRole($role1->name,$role2->name,$role3->name,$role4->name,$role5->name);
        $response = $this->actingAs($user)->get(route('role.index'));
        $response->assertStatus(403);
    }

    public function test_user_without_role_doesnt_have_access_to_the_role_index_page(): void
    {
        $user =  User::factory()->create();
        $response = $this->actingAs($user)->get(route('role.index'));
        $response->assertStatus(403);
    }

    public function test_user_without_auth_doesnt_have_access_to_the_role_index_page(): void
    {
        $response = $this->get(route('role.index'));
        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
    public function test_inertia_component_of_the_role_index_page(): void
    {
        $user = User::factory()->create();
        $role = Role::create( ['name' => 'admin']);
        $user->assignRole($role->name);
        $this->actingAs($user)->get(route('role.index'))
            ->assertInertia(fn (Assert $page) => $page
                ->component('Roles/Index')
                ->has('roles')
                ->has('table_search')
                ->has('table_filter')
            );
    }
}
