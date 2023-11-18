<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = ['name', 'description', 'resource'];

    public function awards(): BelongsToMany
    {
        return $this->belongsToMany(Award::class, 'awards_categories')
            ->withPivot('order')
            ->withTimestamps();
    }

    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(Document::class, 'documents_categories')
            ->withPivot('order')
            ->withTimestamps();
    }

    public function forms(): BelongsToMany
    {
        return $this->belongsToMany(Form::class, 'forms_categories')
            ->withPivot('order')
            ->withTimestamps();
    }

    public function qualifications(): BelongsToMany
    {
        return $this->belongsToMany(Qualification::class, 'qualifications_categories')
            ->withPivot('order')
            ->withTimestamps();
    }

    public function ranks(): BelongsToMany
    {
        return $this->belongsToMany(Rank::class, 'ranks_categories')
            ->withPivot('order')
            ->withTimestamps();
    }
}
