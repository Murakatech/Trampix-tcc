<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Freelancer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleSelectionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Teste: Usuário apenas freelancer vê tela de seleção
     */
    public function test_user_with_only_freelancer_profile_sees_selection_screen(): void
    {
        // Criar usuário com perfil freelancer
        $user = User::factory()->create();
        $freelancer = Freelancer::factory()->create([
            'user_id' => $user->id,
            'is_active' => true,
        ]);

        // Fazer login
        $this->actingAs($user);

        // Acessar tela de seleção
        $response = $this->get('/select-role');

        // Verificar que a view é exibida
        $response->assertStatus(200);
        $response->assertViewHas('hasFreelancer', true);
        $response->assertViewHas('hasCompany', false);
    }

    /** @test */
    public function user_with_only_company_profile_sees_selection_screen()
    {
        // Criar usuário com apenas perfil empresa
        $user = User::factory()->create();
        $company = Company::factory()->create(['user_id' => $user->id]);

        // Fazer login
        $this->actingAs($user);

        // Acessar tela de seleção
        $response = $this->get(route('select-role.show'));

        // Verificar que a view é exibida
        $response->assertStatus(200);
        $response->assertViewIs('auth.select_role');
        $response->assertViewHas('hasFreelancer', false);
        $response->assertViewHas('hasCompany', true);
    }

    /** @test */
    public function user_without_profiles_sees_selection_screen()
    {
        // Criar usuário sem perfis
        $user = User::factory()->create();

        // Fazer login
        $this->actingAs($user);

        // Acessar tela de seleção
        $response = $this->get(route('select-role.show'));

        // Verificar que a view é exibida
        $response->assertStatus(200);
        $response->assertViewIs('auth.select_role');
        $response->assertViewHas('hasFreelancer', false);
        $response->assertViewHas('hasCompany', false);
    }

    /**
     * Teste: Usuário com ambos os perfis redireciona para seleção de perfil
     */
    public function test_user_with_both_profiles_redirects_to_role_selection(): void
    {
        // Criar usuário com ambos os perfis
        $user = User::factory()->create();
        $freelancer = Freelancer::factory()->create([
            'user_id' => $user->id,
            'is_active' => true,
        ]);
        $company = Company::factory()->create([
            'user_id' => $user->id,
            'is_active' => true,
        ]);

        // Fazer login
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        // Verificar redirecionamento para seleção de perfil
        $response->assertRedirect('/select-role');

        // Verificar que active_role não foi definido na sessão
        $this->assertNull(session('active_role'));
    }

    /**
     * Teste: Usuário sem perfis é redirecionado para seleção de perfil
     */
    public function test_user_without_profiles_redirects_to_profile_selection(): void
    {
        // Criar usuário sem perfis
        $user = User::factory()->create();

        // Fazer login
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        // Verificar redirecionamento para seleção de perfil (criação)
        $response->assertRedirect('/profile-selection');

        // Verificar que active_role não foi definido na sessão
        $this->assertNull(session('active_role'));
    }

    /**
     * Teste: Tela de seleção de perfil é exibida corretamente
     */
    public function test_role_selection_page_displays_correctly(): void
    {
        // Criar usuário com ambos os perfis
        $user = User::factory()->create();
        $freelancer = Freelancer::factory()->create([
            'user_id' => $user->id,
            'is_active' => true,
        ]);
        $company = Company::factory()->create([
            'user_id' => $user->id,
            'is_active' => true,
        ]);

        // Autenticar usuário
        $this->actingAs($user);

        // Acessar página de seleção
        $response = $this->get('/select-role');

        $response->assertStatus(200);
        $response->assertSee('Selecione como deseja continuar');
        $response->assertSee('Freelancer');
        $response->assertSee('Empresa');
    }

    /**
     * Teste: Seleção de perfil freelancer funciona corretamente
     */
    public function test_selecting_freelancer_role_works(): void
    {
        // Criar usuário com ambos os perfis
        $user = User::factory()->create();
        $freelancer = Freelancer::factory()->create([
            'user_id' => $user->id,
            'is_active' => true,
        ]);
        $company = Company::factory()->create([
            'user_id' => $user->id,
            'is_active' => true,
        ]);

        // Autenticar usuário
        $this->actingAs($user);

        // Selecionar perfil freelancer
        $response = $this->post('/select-role', [
            'role' => 'freelancer',
        ]);

        // Verificar redirecionamento para dashboard
        $response->assertRedirect('/dashboard');

        // Verificar que active_role foi definido na sessão
        $this->assertEquals('freelancer', session('active_role'));
    }

    /**
     * Teste: Seleção de perfil empresa funciona corretamente
     */
    public function test_selecting_company_role_works(): void
    {
        // Criar usuário com ambos os perfis
        $user = User::factory()->create();
        $freelancer = Freelancer::factory()->create([
            'user_id' => $user->id,
            'is_active' => true,
        ]);
        $company = Company::factory()->create([
            'user_id' => $user->id,
            'is_active' => true,
        ]);

        // Autenticar usuário
        $this->actingAs($user);

        // Selecionar perfil empresa
        $response = $this->post('/select-role', [
            'role' => 'company',
        ]);

        // Verificar redirecionamento para dashboard
        $response->assertRedirect('/dashboard');

        // Verificar que active_role foi definido na sessão
        $this->assertEquals('company', session('active_role'));
    }

    /**
     * Teste: Troca de perfil funciona corretamente
     */
    public function test_switching_roles_works(): void
    {
        // Criar usuário com ambos os perfis
        $user = User::factory()->create();
        $freelancer = Freelancer::factory()->create([
            'user_id' => $user->id,
            'is_active' => true,
        ]);
        $company = Company::factory()->create([
            'user_id' => $user->id,
            'is_active' => true,
        ]);

        // Autenticar usuário e definir perfil inicial
        $this->actingAs($user);
        session(['active_role' => 'freelancer']);

        // Trocar perfil
        $response = $this->post('/switch-role');

        // Verificar redirecionamento para seleção de perfil
        $response->assertRedirect('/select-role');

        // Verificar que active_role foi removido da sessão
        $this->assertNull(session('active_role'));
    }

    /**
     * Teste: Usuário sem perfil válido não pode selecionar perfil inexistente
     */
    public function test_user_cannot_select_nonexistent_profile(): void
    {
        // Criar usuário apenas com perfil freelancer
        $user = User::factory()->create();
        $freelancer = Freelancer::factory()->create([
            'user_id' => $user->id,
            'is_active' => true,
        ]);

        // Autenticar usuário
        $this->actingAs($user);

        // Primeiro acessar a tela de seleção para ter uma página anterior
        $this->get(route('select-role.show'));

        // Tentar selecionar perfil empresa (que não existe)
        $response = $this->post(route('select-role.select'), [
            'role' => 'company',
        ]);

        // Verificar redirecionamento de volta com erro
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Você não possui perfil de empresa.');
    }

    /**
     * Teste: Usuário com active_role válido existente é redirecionado corretamente no login
     */
    public function test_user_with_existing_valid_active_role_redirects_correctly(): void
    {
        // Criar usuário com ambos os perfis
        $user = User::factory()->create();
        $freelancer = Freelancer::factory()->create([
            'user_id' => $user->id,
            'is_active' => true,
        ]);
        $company = Company::factory()->create([
            'user_id' => $user->id,
            'is_active' => true,
        ]);

        // Simular sessão existente com active_role
        session(['active_role' => 'company']);

        // Fazer login
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        // Verificar redirecionamento direto para dashboard
        $response->assertRedirect('/dashboard');

        // Verificar que active_role foi mantido na sessão
        $this->assertEquals('company', session('active_role'));
    }

    /**
     * Teste: Middleware ShareActiveRole compartilha dados corretos com views
     */
    public function test_middleware_shares_correct_data_with_views(): void
    {
        // Criar usuário com ambos os perfis
        $user = User::factory()->create();
        $freelancer = Freelancer::factory()->create([
            'user_id' => $user->id,
            'is_active' => true,
        ]);
        $company = Company::factory()->create([
            'user_id' => $user->id,
            'is_active' => true,
        ]);

        // Autenticar usuário e definir perfil ativo
        $this->actingAs($user);
        session(['active_role' => 'freelancer']);

        // Acessar a tela de seleção de perfil (que usa o middleware)
        $response = $this->get(route('select-role.show'));

        // Verificar que os dados foram compartilhados
        $response->assertViewHas('activeRole', 'freelancer');
        $response->assertViewHas('hasFreelancerProfile', true);
        $response->assertViewHas('hasCompanyProfile', true);
        $response->assertViewHas('canSwitchRoles', true);
        $response->assertViewHas('currentUser');
    }
}
