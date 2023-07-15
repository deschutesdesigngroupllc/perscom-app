<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Spatie\Url\Url;

/**
 * App\Models\Domain
 *
 * @property int $id
 * @property string $domain
 * @property int $tenant_id
 * @property int $is_custom_subdomain
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string|null $host
 * @property-read string|null $url
 * @property-read \App\Models\Tenant $tenant
 *
 * @method static \Database\Factories\DomainFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Domain newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Domain newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Domain query()
 * @method static \Illuminate\Database\Eloquent\Builder|Domain whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Domain whereDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Domain whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Domain whereIsCustomSubdomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Domain whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Domain whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Domain extends \Stancl\Tenancy\Database\Models\Domain
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $appends = ['host', 'url'];

    public static function generateSubdomain(): string
    {
        return Str::lower(Str::random(8));
    }

    public function getUrlAttribute(): ?string
    {
        return optional($this->host, static function ($host) {
            return rtrim(Url::fromString($host)->withScheme(config('app.scheme'))->__toString(), '/');
        });
    }

    public function getHostAttribute(): ?string
    {
        return optional($this->domain, static function ($domain) {
            return Url::fromString(Str::endsWith($domain, config('tenancy.central_domains')) ? $domain
                : $domain.config('app.base_url'))->__toString();
        });
    }
}
