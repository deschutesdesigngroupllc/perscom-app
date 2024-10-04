<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\NotificationCanBeManaged;
use App\Data\ManagedNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class NotificationService
{
    /**
     * @return Collection<ManagedNotification>
     */
    public static function configurableNotifications(): Collection
    {
        return Cache::rememberForever('managed_notifications', function () {
            $finder = new Finder;
            $finder->files()->in(
                dirs: [
                    app_path('Notifications/Tenant'),
                    app_path('Notifications/User'),
                ]
            )->name('*.php');

            return collect($finder)
                ->map(function (SplFileInfo $fileInfo) {
                    $className = basename($fileInfo->getFilename(), '.php');

                    $namespace = '';
                    if (preg_match('/namespace\s+([^;]+)/', file_get_contents($fileInfo->getRealPath()), $matches)) {
                        $namespace = trim($matches[1]);
                    }

                    return $namespace
                        ? $namespace.'\\'.$className
                        : $className;
                })
                ->reject(function (string $class) {
                    if (! class_exists($class)) {
                        return true;
                    }

                    if (! is_subclass_of($class, NotificationCanBeManaged::class)) {
                        return true;
                    }

                    return false;
                })
                ->map(function (string|NotificationCanBeManaged $class) {
                    return ManagedNotification::from([
                        'group' => $class::notificationGroup(),
                        'title' => $class::notificationTitle(),
                        'description' => $class::notificationDescription(),
                        'notificationClass' => $class,
                    ]);
                })
                ->groupBy(fn (ManagedNotification $managedNotification) => $managedNotification->group);
        });
    }
}
