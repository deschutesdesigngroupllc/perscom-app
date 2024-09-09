<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

use function Laravel\Prompts\info;
use function Laravel\Prompts\password;
use function Laravel\Prompts\search;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class AdminCommand extends Command implements PromptsForMissingInput
{
    protected $signature = 'perscom:admin';

    protected $description = 'Performs common actions on a admin user.';

    public function handle(): int
    {
        $operation = select(
            label: 'Please select an operation:',
            options: [
                'create' => 'Create a new admin',
                'password' => 'Update an admin\'s password',
            ]
        );

        return match ($operation) {
            'create' => $this->createAdmin(),
            'password' => $this->updatePassword(),
        };
    }

    protected function createAdmin(): int
    {
        $name = text(
            label: 'Please provide the admin\'s name:',
            required: true,
            validate: [
                'name' => 'max:255|string',
            ]
        );

        $email = text(
            label: 'Please provide the admin\'s email:',
            required: true,
            validate: [
                'email' => 'max:255|email|unique:admins,email',
            ]
        );

        $password = password(
            label: 'Please provide the admin\'s password:',
            required: true,
            validate: [
                'password' => new Password(8),
            ],
            hint: 'Minimum characters required: 8'
        );

        Admin::create(compact('name', 'email', 'password'));

        info('The admin has been successfully created.');

        return static::SUCCESS;
    }

    protected function updatePassword(): int
    {
        $id = search(
            label: 'Please select the admin:',
            options: fn ($value) => strlen($value) > 0
                ? Admin::where('name', 'like', "%$value%")->pluck('name', 'id')->all()
                : []
        );

        $password = password(
            label: 'Please provide the admin\'s password:',
            required: true,
            validate: [
                'password' => new Password(8),
            ],
            hint: 'Minimum characters required: 8'
        );

        Admin::findOrFail($id)->forceFill([
            'password' => Hash::make($password),
        ])->save();

        info('The admin\'s password been successfully update.');

        return static::SUCCESS;
    }
}
