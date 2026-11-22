<?php

declare(strict_types=1);

namespace Tests\Feature\Central\Config;

use Illuminate\Support\Facades\Storage;
use Tests\Feature\Central\CentralTestCase;

class GoogleFontsTest extends CentralTestCase
{
    public function test_google_fonts_are_served_from_a_root_relative_url(): void
    {
        $this->assertSame('google-fonts', config('google-fonts.disk'));
        $this->assertSame('/storage/fonts/abc/inter.ttf', Storage::disk('google-fonts')->url('fonts/abc/inter.ttf'));
    }

    public function test_the_fonts_disk_is_not_scoped_per_tenant(): void
    {
        $this->assertNotContains('google-fonts', config('tenancy.filesystem.disks'));
    }
}
