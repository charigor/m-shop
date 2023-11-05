<?php

namespace Tests\Feature\Role;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CreateRoleTest extends TestCase
{
    use RefreshDatabase,WithFaker;

    /**
     * A basic test example.
     */
    public function test_admin_role_has_access_to_the_role_create_page(): void
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'admin']);
        $user->assignRole($role->name);
        $response = $this->actingAs($user)->get(route('role.create'));
        $response->assertStatus(200);
    }

    public function test_any_role_except_admin_has_not_access_to_the_role_create_page(): void
    {
        $user = User::factory()->create();
        $role1 = Role::create(['name' => 'manager']);
        $role2 = Role::create(['name' => 'user']);
        $role3 = Role::create(['name' => 'default']);
        $role4 = Role::create(['name' => $this->faker->title]);
        $role5 = Role::create(['name' => '']);
        $user->assignRole($role1->name, $role2->name, $role3->name, $role4->name, $role5->name);
        $response = $this->actingAs($user)->get(route('role.create'));
        $response->assertStatus(403);
    }

    public function test_user_without_role_doesnt_have_access_to_the_role_create_page(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('role.create'));
        $response->assertStatus(403);
    }

    public function test_user_without_auth_doesnt_have_access_to_the_role_create_page(): void
    {
        $response = $this->get(route('role.create'));
        $response->assertStatus(302);
        $response->assertRedirect('login');
    }

    public function test_inertia_component_of_the_role_create_page(): void
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'admin']);
        $user->assignRole($role->name);
        $this->actingAs($user)->get(route('role.create'))
            ->assertInertia(fn (Assert $page) => $page
                ->component('Roles/Create')->has('role', fn (Assert $page) => $page
                ->has('id')
                ->has('name')
                ->has('created_at')
                ->has('permissions')
                ));
    }
}
