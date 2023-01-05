<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Spatie\Url\Url;

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
     * @return mixed|null
     */
    public function getUrlAttribute()
    {
        return optional($this->domain, function () {
            return Url::fromString(Str::endsWith($this->domain, config('tenancy.central_domains')) ? $this->domain : $this->domain.config('app.base_url'))
                      ->withScheme(config('app.scheme'))
                      ->__toString();
        });
    }
}
