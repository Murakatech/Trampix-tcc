<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Freelancer;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_update_freelancer_profile_via_unified_flow(): void
    {
        $user = User::factory()->create();
        $freelancer = Freelancer::create([
            'user_id' => $user->id,
            'bio' => 'Bio antiga',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->patch(route('profile.update'), [
            'section' => 'freelancer',
            'bio' => 'Bio atualizada',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $freelancer->refresh();
        $this->assertSame('Bio atualizada', $freelancer->bio);
    }

    public function test_non_owner_cannot_update_freelancer_via_resource_route(): void
    {
        $owner = User::factory()->create();
        $attacker = User::factory()->create();
        $freelancer = Freelancer::create([
            'user_id' => $owner->id,
            'bio' => 'Bio do dono',
            'is_active' => true,
        ]);

        $response = $this->actingAs($attacker)->patch(route('freelancers.update', $freelancer), [
            'bio' => 'Hacked',
        ]);

        $response->assertForbidden();
        $freelancer->refresh();
        $this->assertSame('Bio do dono', $freelancer->bio);
    }

    public function test_admin_can_update_any_freelancer_via_resource_route(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $owner = User::factory()->create();
        $freelancer = Freelancer::create([
            'user_id' => $owner->id,
            'bio' => 'Bio original',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->patch(route('freelancers.update', $freelancer), [
            'bio' => 'Atualizada pelo admin',
        ]);

        $response->assertRedirect(route('freelancers.edit', $freelancer));
        $freelancer->refresh();
        $this->assertSame('Atualizada pelo admin', $freelancer->bio);
    }

    public function test_owner_can_update_company_profile_via_unified_flow(): void
    {
        $user = User::factory()->create();
        $company = Company::create([
            'user_id' => $user->id,
            'name' => 'Empresa Antiga',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->patch(route('profile.update'), [
            'section' => 'company',
            'name' => 'Empresa Atualizada',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $company->refresh();
        $this->assertSame('Empresa Atualizada', $company->name);
    }

    public function test_non_owner_cannot_update_company_via_resource_route(): void
    {
        $owner = User::factory()->create();
        $attacker = User::factory()->create();
        $company = Company::create([
            'user_id' => $owner->id,
            'name' => 'Empresa do dono',
            'is_active' => true,
        ]);

        $response = $this->actingAs($attacker)->patch(route('companies.update', $company), [
            'name' => 'Hacked Co',
        ]);

        $response->assertForbidden();
        $company->refresh();
        $this->assertSame('Empresa do dono', $company->name);
    }

    public function test_admin_can_update_any_company_via_resource_route(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $owner = User::factory()->create();
        $company = Company::create([
            'user_id' => $owner->id,
            'name' => 'Empresa original',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->patch(route('companies.update', $company), [
            'name' => 'Empresa atualizada pelo admin',
        ]);

        $response->assertRedirect(route('companies.edit', $company));
        $company->refresh();
        $this->assertSame('Empresa atualizada pelo admin', $company->name);
    }

    public function test_user_can_create_freelancer_profile_when_none_exists(): void
    {
        $user = User::factory()->create();

        $this->assertNull($user->freelancer);

        $response = $this->actingAs($user)->patch(route('profile.update'), [
            'section' => 'freelancer',
            'bio' => 'Nova bio criada',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $user->refresh();
        $this->assertNotNull($user->freelancer);
        $this->assertSame('Nova bio criada', $user->freelancer->bio);
    }

    public function test_user_can_create_company_profile_when_none_exists(): void
    {
        $user = User::factory()->create();

        $this->assertNull($user->company);

        $response = $this->actingAs($user)->patch(route('profile.update'), [
            'section' => 'company',
            'name' => 'Minha Nova Empresa',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $user->refresh();
        $this->assertNotNull($user->company);
        $this->assertSame('Minha Nova Empresa', $user->company->name);
    }
}