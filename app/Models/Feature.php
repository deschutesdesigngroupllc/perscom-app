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
 * @property float|null $price
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

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getRows(): array
    {
        $premiumFeatures = config('spark.premium_features');

        return [
            [
                'id' => 1,
                'name' => 'Advanced Notifications',
                'description' => 'Upgrade your PERSCOM experience with advanced notifications - allowing you to send real-time information using channels such as Discord, SMS, or FCM.',
                'price_id' => data_get($premiumFeatures, AdvancedNotificationsFeature::class),
                'price' => 5,
                'term' => 'monthly',
                'feature' => AdvancedNotificationsFeature::class,
            ],
        ];
    }

    /**
     * @return Attribute<bool, void>
     */
    public function enabled(): Attribute
    {
        return Attribute::get(fn () => true);
    }

    /**
     * @return string[]
     */
    protected function casts(): array
    {
        return [
            'price' => 'float',
            'enabled' => 'boolean',
            'term' => ProductTerm::class,
        ];
    }
}
