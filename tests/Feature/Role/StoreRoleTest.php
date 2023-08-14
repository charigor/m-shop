<?php

namespace Tests\Feature\Role;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;
class StoreRoleTest extends TestCase
{
    use RefreshDatabase,WithFaker;
    /**
     * A basic test example.
     */
    private string $admin_role;
    private string $name;
    public function setUp(): void
    {
        parent::setUp();
        $this->name = $this->faker->name();
        $this->admin_role = 'admin';
    }
    public function test_admin_role_has_access_to_the_role_store_page(): void
    {
        $user = User::factory()->create();
        $role = Role::create( ['name' =>  $this->admin_role]);
        $user->assignRole($role->name);

        $response = $this->actingAs($user)->post(route('role.store',[
            'name' => $this->name
        ]));
        $this->assertDatabaseHas('roles',[
            'name' => $this->name
        ]);

        $response->assertRedirect(route('role.edit',++$role->id));
    }
    public function test_custom_validation_rule()
    {
        $user = User::factory()->create();
        $role = Role::create( ['name' =>  $this->admin_role]);
        $user->assignRole($role->name);
        $response = $this->actingAs($user)->post(route('role.store',[
            'name' => 'qqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqq'
        ]));
        $response->assertSessionHasErrors([
            'name' => 'The name field must not be greater than 30 characters.',
        ])->assertStatus(302);
    }
}
