<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Award;
use App\Models\AwardCategory;
use App\Models\Category;
use App\Models\Competency;
use App\Models\CompetencyCategory;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\Form;
use App\Models\FormCategory;
use App\Models\Position;
use App\Models\PositionCategory;
use App\Models\Qualification;
use App\Models\QualificationCategory;
use App\Models\Rank;
use App\Models\RankCategory;
use App\Models\Specialty;
use App\Models\SpecialtyCategory;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @mixin Eloquent
 */
trait HasCategories
{
    public function categories(): BelongsToMany
    {
        $relationship = $this->belongsToMany(Category::class, $this->getTable().'_categories')
            ->where('resource', $this::class)
            ->withPivot('order')
            ->withTimestamps();

        $using = match (static::class) {
            Award::class => AwardCategory::class,
            Competency::class => CompetencyCategory::class,
            Document::class => DocumentCategory::class,
            Form::class => FormCategory::class,
            Position::class => PositionCategory::class,
            Qualification::class => QualificationCategory::class,
            Rank::class => RankCategory::class,
            Specialty::class => SpecialtyCategory::class,
            default => null,
        };

        if (! is_null($using)) {
            return $relationship->as($using);
        }

        return $relationship;
    }

    public function categoryPivot(): HasOne
    {
        $related = match (static::class) {
            Award::class => AwardCategory::class,
            Competency::class => CompetencyCategory::class,
            Document::class => DocumentCategory::class,
            Form::class => FormCategory::class,
            Position::class => PositionCategory::class,
            Qualification::class => QualificationCategory::class,
            Rank::class => RankCategory::class,
            Specialty::class => SpecialtyCategory::class,
            default => null,
        };

        return $this->hasOne($related)
            ->ofMany();
    }
}
