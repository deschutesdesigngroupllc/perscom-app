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
    protected $appends = ['url'];

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
        return optional($this->domain, function () {
            return Url::fromString(Str::endsWith($this->domain, config('tenancy.central_domains')) ? $this->domain
                : $this->domain.config('app.base_url'))->withScheme(config('app.scheme'))->__toString();
        });
    }
}
