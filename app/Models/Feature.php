<?php

declare(strict_types=1);

namespace App\Models;

use App\Features\AdvancedNotificationsFeature;
use App\Models\Enums\ProductTerm;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Sushi\Sushi;

/**
 * @property int $id
 * @property string|null $name
 * @property string|null $description
 * @property string|null $price_id
 * @property int|null $price
 * @property ProductTerm|null $term
 * @property string|null $feature
 * @property-read mixed $enabled
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Feature newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Feature newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Feature query()
 * @method static \Illuminate\Database\Eloquent\Builder|Feature whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feature whereFeature($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feature whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feature whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feature wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feature wherePriceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feature whereTerm($value)
 *
 * @mixin \Eloquent
 */
class Feature extends Model
{
    use Sushi;

    protected $appends = [
        'enabled',
    ];

    public function getRows(): array
    {
        $addons = config('spark.addons');

        return [
            [
                'id' => 1,
                'name' => 'Advanced Notifications',
                'description' => 'Upgrade your PERSCOM experience with advanced notifications - allowing you to send real-time information using channels such as Discord, SMS, or FCM.',
                'price_id' => data_get($addons, AdvancedNotificationsFeature::class),
                'price' => 5,
                'term' => 'monthly',
                'feature' => AdvancedNotificationsFeature::class,
            ],
        ];
    }

    public function enabled(): Attribute
    {
        return Attribute::get(fn () => true);
    }

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'term' => ProductTerm::class,
        ];
    }
}
