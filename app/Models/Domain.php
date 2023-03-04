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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed|null $url
 * @property-read mixed|null $host
 * @property-read \App\Models\Tenant $tenant
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Domain newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Domain newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Domain query()
 * @method static \Illuminate\Database\Eloquent\Builder|Domain whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Domain whereDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Domain whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Domain whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Domain whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Domain extends \Stancl\Tenancy\Database\Models\Domain
{
    use HasFactory;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['host', 'url'];

    /**
     * @return string
     */
    public static function generateSubdomain(): string
    {
        return Str::lower(Str::random(8));
    }

    /**
     * @return mixed|null
     */
    public function getUrlAttribute()
    {
        return optional($this->host, static function ($host) {
            return Url::fromString($host)->withScheme(config('app.scheme'))->__toString();
        });
    }

    /**
     * @return \Illuminate\Support\Optional|mixed|string
     */
    public function getHostAttribute()
    {
        return optional($this->domain, static function ($domain) {
            return Url::fromString(
                Str::endsWith($domain, config('tenancy.central_domains')) ? $domain : $domain.config('app.base_url')
            )->__toString();
        });
    }
}
