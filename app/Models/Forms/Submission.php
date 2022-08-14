<?php

namespace App\Models\Forms;

use App\Casts\SubmissionCast;
use App\Models\Field;
use App\Models\User;
use App\Traits\HasStatuses;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Laravel\Nova\Actions\Actionable;

class Submission extends Model
{
    use Actionable;
    use HasFactory;
    use HasStatuses;

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['form', 'user'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'form_id', 'data'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'data' => AsCollection::class,
    ];

    /**
     * @param  string  $key
     *
     * @return mixed
     */
    public function getAttribute($key)
    {
        if (Str::startsWith($key, 'field_')) {
            $fieldId = self::getFieldIdFromKey($key);

            $attribute = collect($this->data)->get($fieldId);

            return $this->castAttribute($key, $attribute);
        }

        return parent::getAttribute($key);
    }

    /**
     * @param  string  $key
     * @param  mixed   $value
     *
     * @return mixed
     */
    public function setAttribute($key, $value)
    {
        if (Str::startsWith($key, 'field_')) {
            $fieldId = self::getFieldIdFromKey($key);

            $attribute = collect($this->data)->put($fieldId, $value);

            return parent::setAttribute('data', $attribute);
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * @param  string  $key
     *
     * @return array|\ArrayAccess|mixed|string
     */
    protected function getCastType($key)
    {
        if (Str::startsWith($key, 'field_')) {
            $fieldId = self::getFieldIdFromKey($key);

            $field = $this->form?->fields->where('id', $fieldId)->first();

            if ($field && Arr::exists(Field::$fieldTypes, $field->type)) {
                return Arr::get(Field::$fieldTypes, $field->type);
            }
            return 'string';
        }

        return parent::getCastType($key);
    }

    /**
     * @param $key
     *
     * @return string
     */
    public static function getFieldIdFromKey($key)
    {
        return substr($key, strpos($key, '_') + 1);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function form()
    {
        return $this->belongsTo(Form::class);
    }
}
