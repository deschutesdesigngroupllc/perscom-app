<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin Eloquent|User
 */
trait HasProfilePhoto
{
    public function updateProfilePhoto(UploadedFile $photo, $storagePath = 'profile-photos'): void
    {
        tap($this->profile_photo, function ($previous) use ($photo, $storagePath): void {
            $this->forceFill([
                'profile_photo' => $photo->storePublicly(
                    $storagePath, ['disk' => $this->profilePhotoDisk()]
                ),
            ])->save();

            if ($previous) {
                Storage::disk($this->profilePhotoDisk())->delete($previous);
            }
        });
    }

    public function deleteProfilePhoto(): void
    {
        if (is_null($this->profile_photo)) {
            return;
        }

        Storage::disk($this->profilePhotoDisk())->delete($this->profile_photo);

        $this->forceFill([
            'profile_photo' => null,
        ])->save();
    }

    public function profilePhotoUrl(): Attribute
    {
        return Attribute::get(fn (): string => $this->profile_photo
            ? Storage::disk($this->profilePhotoDisk())->url($this->profile_photo)
            : $this->defaultProfilePhotoUrl())->shouldCache();
    }

    protected function defaultProfilePhotoUrl(): string
    {
        $name = trim(collect(explode(' ', $this->name))->map(fn ($segment): string => mb_substr((string) $segment, 0, 1))->join(' '));

        return 'https://ui-avatars.com/api/?name='.urlencode($name).'&color=2563eb&background=eff6ff';
    }

    protected function profilePhotoDisk(): string
    {
        return 's3';
    }
}
