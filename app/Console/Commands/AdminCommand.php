<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

use function Laravel\Prompts\confirm;
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
                'delete' => 'Delete an admin',
            ]
        );

        return match ($operation) {
            'create' => $this->createAdmin(),
            'password' => $this->updatePassword(),
            'delete' => $this->deleteAdmin(),
            default => static::INVALID
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

        $password = Hash::make(password(
            label: 'Please provide the admin\'s password:',
            required: true,
            validate: [
                'password' => new Password(8),
            ],
            hint: 'Minimum characters required: 8'
        ));

        Admin::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);

        info('The admin has been successfully created.');

        return static::SUCCESS;
    }

    protected function updatePassword(): int
    {
        $id = search(
            label: 'Please select the admin:',
            options: fn ($value) => $value !== ''
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

        info('The admin\'s password been successfully updated.');

        return static::SUCCESS;
    }

    protected function deleteAdmin(): int
    {
        $id = search(
            label: 'Please select the admin:',
            options: fn ($value) => $value !== ''
                ? Admin::where('name', 'like', "%$value%")->pluck('name', 'id')->all()
                : []
        );

        $confirmed = confirm(
            label: 'Are you sure you want to delete the admin?',
            default: false,
            yes: 'Yes, delete the admin',
            no: 'No, do not delete the admin',
        );

        if ($confirmed) {
            Admin::where('id', '=', $id)->delete();

            info('The admin has been successfully deleted.');
        }

        return static::SUCCESS;
    }
}
