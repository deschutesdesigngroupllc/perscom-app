<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

class Settings extends \Outl1ne\NovaSettings\Models\Settings
{
    use TenantConnection;
    use HasFactory;
}
