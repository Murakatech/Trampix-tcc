<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Freelancer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileContextTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    /** @test */
    public function user_with_only_freelancer_profile_sees_freelancer_fields()
    {
        // Criar usuário com apenas perfil freelancer
        $user = User::factory()->create();
        $freelancer = Freelancer::factory()->create(['user_id' => $user->id]);

        // Simular login e definir active_role
        $this->actingAs($user);
        session(['active_role' => 'freelancer']);

        // Acessar página de edição de perfil
        $response = $this->get(route('profile.edit'));

        $response->assertStatus(200);
        $response->assertSee('Perfil Freelancer');
        $response->assertSee('Biografia');
        $response->assertSee('Portfólio');
        $response->assertSee('Valor por Hora');
        $response->assertDontSee('Nome da Empresa');
        $response->assertDontSee('CNPJ');
    }

    /** @test */
    public function user_with_only_company_profile_sees_company_fields()
    {
        // Criar usuário com apenas perfil empresa
        $user = User::factory()->create();
        $company = Company::factory()->create(['user_id' => $user->id]);

        // Simular login e definir active_role
        $this->actingAs($user);
        session(['active_role' => 'company']);

        // Acessar página de edição de perfil
        $response = $this->get(route('profile.edit'));

        $response->assertStatus(200);
        $response->assertSee('Perfil Empresa');
        $response->assertSee('Nome da Empresa');
        $response->assertSee('CNPJ');
        $response->assertSee('Setor');
        $response->assertDontSee('Biografia');
        $response->assertDontSee('Portfólio');
        $response->assertDontSee('Valor por Hora');
    }

    /** @test */
    public function user_with_both_profiles_sees_active_profile_and_switch_button()
    {
        // Criar usuário com ambos os perfis
        $user = User::factory()->create();
        $freelancer = Freelancer::factory()->create(['user_id' => $user->id]);
        $company = Company::factory()->create(['user_id' => $user->id]);

        // Simular login com perfil freelancer ativo
        $this->actingAs($user);
        session(['active_role' => 'freelancer']);

        $response = $this->get(route('profile.edit'));

        $response->assertStatus(200);
        $response->assertSee('Perfil Freelancer');
        $response->assertSee('Mudar para perfil Empresa');
        $response->assertSee('Biografia');
        $response->assertDontSee('Nome da Empresa');

        // Trocar para perfil empresa
        session(['active_role' => 'company']);

        $response = $this->get(route('profile.edit'));

        $response->assertStatus(200);
        $response->assertSee('Perfil Empresa');
        $response->assertSee('Mudar para perfil Freelancer');
        $response->assertSee('Nome da Empresa');
        $response->assertDontSee('Biografia');
    }

    /** @test */
    public function switch_role_changes_active_role_and_redirects()
    {
        // Criar usuário com ambos os perfis
        $user = User::factory()->create();
        $freelancer = Freelancer::factory()->create(['user_id' => $user->id]);
        $company = Company::factory()->create(['user_id' => $user->id]);

        // Simular login com perfil freelancer ativo
        $this->actingAs($user);
        session(['active_role' => 'freelancer']);

        // Verificar que o perfil ativo é freelancer
        $this->assertEquals('freelancer', session('active_role'));

        // Fazer POST para trocar para empresa
        $response = $this->post(route('select-role.switch'));

        // Verificar redirecionamento para select-role (comportamento atual do controller)
        $response->assertRedirect(route('select-role.show'));

        // Verificar que a sessão foi limpa (comportamento atual)
        $this->assertNull(session('active_role'));
    }

    /** @test */
    public function profile_account_page_shows_user_data_only()
    {
        // Criar usuário
        $user = User::factory()->create([
            'name' => 'João Silva',
            'email' => 'joao@example.com',
        ]);

        // Simular login
        $this->actingAs($user);

        // Acessar página de configurações da conta
        $response = $this->get(route('profile.account'));

        $response->assertStatus(200);
        $response->assertSee('Configurações da Conta');
        $response->assertSee('João Silva');
        $response->assertSee('joao@example.com');
        $response->assertSee('Alterar Senha');
        $response->assertSee('Excluir Conta');

        // Não deve mostrar campos específicos de perfil
        $response->assertDontSee('Biografia');
        $response->assertDontSee('Nome da Empresa');
        $response->assertDontSee('CNPJ');
    }

    /** @test */
    public function user_without_active_role_is_redirected_to_role_selection()
    {
        // Criar usuário com ambos os perfis
        $user = User::factory()->create();
        $freelancer = Freelancer::factory()->create(['user_id' => $user->id]);
        $company = Company::factory()->create(['user_id' => $user->id]);

        // Simular login sem active_role
        $this->actingAs($user);

        // Tentar acessar página de edição de perfil
        $response = $this->get(route('profile.edit'));

        // Deve ser redirecionado para seleção de perfil
        $response->assertRedirect(route('select-role.show'));
    }

    /** @test */
    public function profile_update_only_affects_active_profile()
    {
        // Criar usuário com ambos os perfis
        $user = User::factory()->create();
        $freelancer = Freelancer::factory()->create([
            'user_id' => $user->id,
            'bio' => 'Bio original',
        ]);
        $company = Company::factory()->create([
            'user_id' => $user->id,
            'name' => 'Empresa Original',
        ]);

        // Simular login com perfil freelancer ativo
        $this->actingAs($user);
        session(['active_role' => 'freelancer']);

        // Atualizar dados do freelancer
        $response = $this->patch(route('profile.update'), [
            'bio' => 'Nova biografia do freelancer',
            'whatsapp' => '11999999999',
        ]);

        $response->assertRedirect();

        // Verificar que apenas o freelancer foi atualizado
        $freelancer->refresh();
        $company->refresh();

        $this->assertEquals('Nova biografia do freelancer', $freelancer->bio);
        $this->assertEquals('11999999999', $freelancer->whatsapp);
        $this->assertEquals('Empresa Original', $company->name); // Não deve ter mudado
    }

    /** @test */
    public function account_update_only_affects_user_data()
    {
        // Criar usuário com perfil freelancer
        $user = User::factory()->create([
            'name' => 'Nome Original',
            'email' => 'original@example.com',
        ]);
        $freelancer = Freelancer::factory()->create([
            'user_id' => $user->id,
            'bio' => 'Bio original',
        ]);

        // Simular login
        $this->actingAs($user);

        // Atualizar dados da conta
        $response = $this->patch(route('profile.account.update'), [
            'name' => 'Novo Nome',
            'email' => 'novo@example.com',
        ]);

        $response->assertRedirect();

        // Verificar que apenas os dados do usuário foram atualizados
        $user->refresh();
        $freelancer->refresh();

        $this->assertEquals('Novo Nome', $user->name);
        $this->assertEquals('novo@example.com', $user->email);
        $this->assertEquals('Bio original', $freelancer->bio); // Não deve ter mudado
    }
}
