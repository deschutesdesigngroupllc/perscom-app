<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\DomainObserver;
use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Spatie\Url\Url;
use Stancl\Tenancy\Database\Concerns\CentralConnection;
use Stancl\Tenancy\Database\Models\Domain as BaseDomain;

/**
 * @property int $id
 * @property string $domain
 * @property int $tenant_id
 * @property bool $is_custom_subdomain
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $host
 * @property-read Tenant $tenant
 * @property-read string $url
 *
 * @method static \Database\Factories\DomainFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domain newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domain newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domain query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domain whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domain whereDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domain whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domain whereIsCustomSubdomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domain whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domain whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
#[ObservedBy(DomainObserver::class)]
class Domain extends BaseDomain
{
    use CentralConnection;
    use ClearsResponseCache;
    use HasFactory;

    protected $fillable = [
        'domain',
        'tenant_id',
        'is_custom_subdomain',
        'created_at',
        'updated_at',
    ];

    protected $appends = [
        'host',
        'url',
    ];

    public static function generateSubdomain(): string
    {
        return Str::lower(Str::random(8));
    }

    /**
     * @return Attribute<string, never>
     */
    public function url(): Attribute
    {
        return Attribute::make(
            get: fn (): string => optional($this->host, static function ($host) {
                return rtrim(Url::fromString($host)->withScheme(config('app.scheme'))->__toString(), '/');
            }),
        )->shouldCache();
    }

    /**
     * @return Attribute<string, never>
     */
    public function host(): Attribute
    {
        return Attribute::make(
            get: fn (): string => optional($this->domain, static function ($domain) {
                return Url::fromString(Str::endsWith($domain, config('tenancy.central_domains'))
                    ? $domain
                    : $domain.config('app.base_url'))->__toString();
            })
        )->shouldCache();
    }

    /**
     * @return string[]
     */
    protected function casts(): array
    {
        return [
            'is_custom_subdomain' => 'boolean',
        ];
    }
}
