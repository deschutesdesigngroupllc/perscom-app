<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

/**
 * App\Models\Banner
 *
 * @property int $id
 * @property string $message
 * @property string|null $link_url
 * @property string|null $link_text
 * @property string|null $background_color
 * @property string|null $text_color
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Database\Factories\BannerFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Banner newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Banner newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Banner query()
 * @method static \Illuminate\Database\Eloquent\Builder|Banner whereBackgroundColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Banner whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Banner whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Banner whereLinkText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Banner whereLinkUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Banner whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Banner whereTextColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Banner whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Banner extends Model
{
    use CentralConnection;
    use HasFactory;
}
