<?php

namespace App\Models;

use App\Models\Forms\Submission;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'color',
    ];

    /**
     * @var string[]
     */
    public static $colors = [
        'bg-sky-100 text-sky-600' => 'Blue',
        'bg-gray-100 text-gray-600' => 'Gray',
        'bg-green-100 text-green-600' => 'Green',
        'bg-red-100 text-red-600' => 'Red',
        'bg-white-100 text-black-600' => 'White',
        'bg-yellow-100 text-yellow-600' => 'Yellow',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function users()
    {
        return $this->morphedByMany(User::class, 'model', 'model_has_statuses');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function submissions()
    {
        return $this->morphedByMany(Submission::class, 'model', 'model_has_statuses');
    }
}
