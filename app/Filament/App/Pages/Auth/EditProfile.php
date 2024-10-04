<?php

declare(strict_types=1);

namespace App\Filament\App\Pages\Auth;

use App\Data\ManagedNotification;
use App\Models\Enums\NotificationGroup;
use App\Services\NotificationService;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;
use Illuminate\Support\Collection;

class EditProfile extends BaseEditProfile
{
    public function form(Form $form): Form
    {
        $notifications = NotificationService::configurableNotifications();

        return $form
            ->schema([
                Tabs::make()
                    ->tabs([
                        Tabs\Tab::make('Profile')
                            ->icon('heroicon-o-user')
                            ->schema([
                                $this->getNameFormComponent(),
                                $this->getEmailFormComponent(),
                                $this->getPasswordFormComponent(),
                                $this->getPasswordConfirmationFormComponent(),
                            ]),
                        Tabs\Tab::make('Notifications')
                            ->icon('heroicon-o-bell-alert')
                            ->schema($notifications->map(function (Collection $notifications, $group) {
                                $group = NotificationGroup::from($group);

                                return Section::make($group->getLabel())
                                    ->description($group->getDescription())
                                    ->icon($group->getIcon())
                                    ->schema($notifications->map(function (ManagedNotification $notification) {
                                        return CheckboxList::make($notification->notificationClass)
                                            ->label($notification->title)
                                            ->helperText($notification->description)
                                            ->columns(4)
                                            ->gridDirection('row')
                                            ->bulkToggleable()
                                            ->options([
                                                'sms' => 'SMS',
                                                'mail' => 'Email',
                                                'broadcast' => 'Live',
                                                'database' => 'Dashboard',
                                            ]);
                                    })->toArray());
                            })->toArray()),
                    ]),
            ]);
    }
}
