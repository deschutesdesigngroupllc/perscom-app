<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Actions\Auth;

use App\Actions\Auth\CreateNewUser;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use Tests\Feature\Tenant\TenantTestCase;

class CreateNewUserTest extends TenantTestCase
{
    public function test_it_creates_a_user_with_a_hashed_password(): void
    {
        Notification::fake();

        $email = $this->faker->unique()->safeEmail();

        $user = CreateNewUser::handle([
            'name' => 'John Doe',
            'email' => $email,
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertSame('John Doe', $user->name);
        $this->assertSame($email, $user->email);
        $this->assertTrue(Hash::check('Password123!', $user->password));

        $this->assertDatabaseHas(User::class, [
            'name' => 'John Doe',
            'email' => $email,
        ]);
    }

    public function test_it_throws_a_validation_exception_when_input_is_invalid(): void
    {
        $this->expectException(ValidationException::class);

        CreateNewUser::handle([
            'name' => '',
            'email' => 'not-an-email',
            'password' => '123',
        ]);
    }

    public function test_it_throws_a_validation_exception_for_a_duplicate_email(): void
    {
        $existing = User::factory()->createQuietly();

        $this->expectException(ValidationException::class);

        CreateNewUser::handle([
            'name' => 'Jane Doe',
            'email' => $existing->email,
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);
    }
}
