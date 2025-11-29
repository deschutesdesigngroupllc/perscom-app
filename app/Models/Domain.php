<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\DomainObserver;
use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Spatie\Url\Url;
use Stancl\Tenancy\Database\Concerns\CentralConnection;
use Stancl\Tenancy\Database\Models\Domain as BaseDomain;

/**
 * @property int $id
 * @property string $domain
 * @property int $tenant_id
 * @property bool $is_custom_subdomain
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string $host
 * @property-read Tenant $tenant
 * @property-read string $url
 *
 * @method static \Database\Factories\DomainFactory factory($count = null, $state = [])
 * @method static Builder<static>|Domain newModelQuery()
 * @method static Builder<static>|Domain newQuery()
 * @method static Builder<static>|Domain query()
 * @method static Builder<static>|Domain whereCreatedAt($value)
 * @method static Builder<static>|Domain whereDomain($value)
 * @method static Builder<static>|Domain whereId($value)
 * @method static Builder<static>|Domain whereIsCustomSubdomain($value)
 * @method static Builder<static>|Domain whereTenantId($value)
 * @method static Builder<static>|Domain whereUpdatedAt($value)
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
            get: fn (): string => optional($this->host, static fn (string $host): string => rtrim(Url::fromString($host)->withScheme(config('app.scheme'))->__toString(), '/')),
        )->shouldCache();
    }

    /**
     * @return Attribute<string, never>
     */
    public function host(): Attribute
    {
        return Attribute::make(
            get: fn (): string => optional($this->domain, static fn (string $domain): string => Url::fromString(Str::endsWith($domain, config('tenancy.central_domains'))
                ? $domain
                : $domain.config('app.base_url'))->__toString())
        )->shouldCache();
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_custom_subdomain' => 'boolean',
        ];
    }
}
