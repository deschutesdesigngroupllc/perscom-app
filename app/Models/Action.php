<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Nova\Actions\ActionEvent;

/**
 * App\Models\Action
 *
 * @property int $id
 * @property string $batch_id
 * @property int $user_id
 * @property string $name
 * @property string $actionable_type
 * @property int $actionable_id
 * @property string $target_type
 * @property int $target_id
 * @property string $model_type
 * @property int|null $model_id
 * @property string $fields
 * @property string $status
 * @property string $exception
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property array|null $original
 * @property array|null $changes
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $target
 * @property-read \App\Models\Admin|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Action newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Action newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Action query()
 * @method static \Illuminate\Database\Eloquent\Builder|Action whereActionableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Action whereActionableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Action whereBatchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Action whereChanges($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Action whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Action whereException($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Action whereFields($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Action whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Action whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Action whereModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Action whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Action whereOriginal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Action whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Action whereTargetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Action whereTargetType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Action whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Action whereUserId($value)
 * @mixin \Eloquent
 */
class Action extends ActionEvent
{
    use HasFactory;
}
