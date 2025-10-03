<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Freelancer;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_account_validation_rejects_duplicate_email(): void
    {
        $userA = User::factory()->create(['email' => 'a@example.com']);
        $userB = User::factory()->create(['email' => 'b@example.com']);

        $response = $this->actingAs($userB)->patch(route('profile.update'), [
            'section' => 'account',
            'name' => 'User B',
            'email' => 'a@example.com', // duplicado
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_account_validation_requires_valid_email_and_name(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->patch(route('profile.update'), [
            'section' => 'account',
            'name' => '',
            'email' => 'invalid-email',
        ]);

        $response->assertSessionHasErrors(['name', 'email']);
    }

    public function test_freelancer_validation_rejects_invalid_fields(): void
    {
        $user = User::factory()->create();
        // criar perfil para cair no caminho de update
        $freelancer = Freelancer::create([
            'user_id' => $user->id,
            'bio' => 'Ok',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->patch(route('profile.update'), [
            'section' => 'freelancer',
            'portfolio_url' => 'not-a-url',
            'hourly_rate' => -10,
            'location' => str_repeat('a', 101),
        ]);

        $response->assertSessionHasErrors(['portfolio_url', 'hourly_rate', 'location']);
    }

    public function test_freelancer_validation_rejects_invalid_cv_mime(): void
    {
        $user = User::factory()->create();
        $freelancer = Freelancer::create([
            'user_id' => $user->id,
            'bio' => 'Ok',
            'is_active' => true,
        ]);

        Storage::fake('public');
        // Evitar dependência do GD usando create() ao invés de image()
        $invalidCv = UploadedFile::fake()->create('cv.png', 10, 'image/png');

        $response = $this->actingAs($user)->patch(route('profile.update'), [
            'section' => 'freelancer',
            'cv' => $invalidCv,
        ]);

        $response->assertSessionHasErrors(['cv']);
    }

    public function test_freelancer_remove_cv_sets_cv_url_to_null(): void
    {
        $user = User::factory()->create();
        Storage::fake('public');

        $freelancer = Freelancer::create([
            'user_id' => $user->id,
            'bio' => 'Ok',
            'cv_url' => 'cvs/existing.pdf',
            'is_active' => true,
        ]);

        // criar arquivo falso
        Storage::disk('public')->put('cvs/existing.pdf', 'content');

        $response = $this->actingAs($user)->patch(route('profile.update'), [
            'section' => 'freelancer',
            'remove_cv' => '1',
        ]);

        $response->assertSessionHasNoErrors()->assertRedirect(route('profile.edit'));
        $freelancer->refresh();
        $this->assertNull($freelancer->cv_url);
        Storage::disk('public')->assertMissing('cvs/existing.pdf');
    }

    public function test_company_validation_rejects_invalid_fields(): void
    {
        $user = User::factory()->create();
        $company = Company::create([
            'user_id' => $user->id,
            'name' => 'Empresa OK',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->patch(route('profile.update'), [
            'section' => 'company',
            'name' => '',
            'website' => 'notaurl',
            'employees_count' => 0,
            'founded_year' => 1700,
        ]);

        $response->assertSessionHasErrors(['name', 'website', 'employees_count', 'founded_year']);
    }

    public function test_company_validation_unique_cnpj_ignores_current_company(): void
    {
        $user = User::factory()->create();
        $ownerCompany = Company::create([
            'user_id' => $user->id,
            'name' => 'Minha',
            'cnpj' => '11.222.333/0001-44',
            'is_active' => true,
        ]);

        // outra empresa com mesmo cnpj
        Company::create([
            'user_id' => User::factory()->create()->id,
            'name' => 'Outra',
            'cnpj' => '55.666.777/0001-88',
            'is_active' => true,
        ]);

        // atualizar mantendo o mesmo CNPJ do próprio — deve passar
        $responseOk = $this->actingAs($user)->patch(route('profile.update'), [
            'section' => 'company',
            'name' => 'Minha Atualizada',
            'cnpj' => '11.222.333/0001-44',
        ]);
        $responseOk->assertSessionHasNoErrors();

        // tentar usar CNPJ de outra empresa — deve falhar
        $responseFail = $this->actingAs($user)->patch(route('profile.update'), [
            'section' => 'company',
            'name' => 'Minha Atualizada 2',
            'cnpj' => '55.666.777/0001-88',
        ]);
        $responseFail->assertSessionHasErrors(['cnpj']);
    }
}