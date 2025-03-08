<?php

declare(strict_types=1);

namespace App\Models;

use App\Features\AdvancedNotificationsFeature;
use App\Features\EnhancedAIFeature;
use App\Features\GoogleCalendarSyncFeature;
use App\Models\Enums\ProductTerm;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Sushi\Sushi;

/**
 * @property int $id
 * @property string|null $name
 * @property string|null $description
 * @property string|null $monthly_id
 * @property string|null $yearly_id
 * @property int|null $price_monthly
 * @property int|null $price_yearly
 * @property string|null $feature
 * @property ProductTerm $term
 * @property-read mixed $enabled
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Feature newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Feature newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Feature query()
 * @method static \Illuminate\Database\Eloquent\Builder|Feature whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feature whereFeature($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feature whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feature whereMonthlyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feature whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feature wherePriceMonthly($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feature wherePriceYearly($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feature whereYearlyId($value)
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
                'description' => 'Upgrade your user experience with advanced notifications - allowing you to send real-time information using channels such as Discord, SMS, or FCM.',
                'monthly_id' => data_get($premiumFeatures, AdvancedNotificationsFeature::class.'.monthly_id'),
                'yearly_id' => data_get($premiumFeatures, AdvancedNotificationsFeature::class.'.yearly_id'),
                'price_monthly' => 15,
                'price_yearly' => 165,
                'feature' => AdvancedNotificationsFeature::class,
            ],
            [
                'id' => 2,
                'name' => 'Google Calendar Sync (Coming Soon)',
                'description' => 'Keep your Google Calendar up-to-date using our advanced calendar and events syncing technology.',
                'monthly_id' => data_get($premiumFeatures, GoogleCalendarSyncFeature::class.'.monthly_id'),
                'yearly_id' => data_get($premiumFeatures, GoogleCalendarSyncFeature::class.'.yearly_id'),
                'price_monthly' => 5,
                'price_yearly' => 55,
                'feature' => GoogleCalendarSyncFeature::class,
            ],
            [
                'id' => 3,
                'name' => 'Enhanced AI (Coming Soon)',
                'description' => 'Use AI to accelerate your productivity by providing an extra set of hands while managing your personnel data.',
                'monthly_id' => data_get($premiumFeatures, EnhancedAIFeature::class.'.monthly_id'),
                'yearly_id' => data_get($premiumFeatures, EnhancedAIFeature::class.'.yearly_id'),
                'price_monthly' => 10,
                'price_yearly' => 110,
                'feature' => EnhancedAIFeature::class,
            ],
        ];
    }

    /**
     * @return Attribute<bool, never>
     */
    public function enabled(): Attribute
    {
        return Attribute::get(fn (): true => true);
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
