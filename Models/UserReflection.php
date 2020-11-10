<?php
declare(strict_types=1);

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\UserReflection
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $content_item_id
 * @property string|null $input
 * @property int $skipped
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read ContentItem|null $contentItem
 * @property-read Program $program
 * @property-read Collection|SilScore[] $silScores
 * @property-read int|null $sil_scores_count
 * @property-read User $user
 * @method static Builder|UserReflection byUser(User $user)
 * @method static Builder|UserReflection newModelQuery()
 * @method static Builder|UserReflection newQuery()
 * @method static Builder|UserReflection notSkipped()
 * @method static Builder|UserReflection ofUserAndContentItemAndProgram(User $user, ContentItem $contentItem, Program $program)
 * @method static Builder|UserReflection query()
 * @method static Builder|UserReflection whereContentItemId($value)
 * @method static Builder|UserReflection whereCreatedAt($value)
 * @method static Builder|UserReflection whereId($value)
 * @method static Builder|UserReflection whereInput($value)
 * @method static Builder|UserReflection whereProgram(Program $program)
 * @method static Builder|UserReflection whereSkipped($value)
 * @method static Builder|UserReflection whereUpdatedAt($value)
 * @method static Builder|UserReflection whereUserId($value)
 * @method static Builder|UserReflection withSilScores(User $user)
 * @mixin Eloquent
 * @property int|null $program_id
 * @property-read \App\Models\ContentItemUserProgress $userProgress
 * @method static Builder|UserReflection whereProgramId($value)
 * @property int|null $user_progress_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserReflection whereUserProgressId($value)
 */
class UserReflection extends Model
{
    protected $fillable = [
        'user_id',
        'content_item_id',
        'program_id'
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function contentItem(): BelongsTo
    {
        return $this->belongsTo(ContentItem::class);
    }

    /**
     * @return BelongsTo
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * @return BelongsTo
     */
    public function userProgress(): BelongsTo
    {
        return $this->belongsTo(ContentItemUserProgress::class, 'user_progress_id');
    }

    /**
     * @return HasMany
     */
    public function silScores(): HasMany
    {
        return $this->hasMany(SilScore::class, 'reflection_id');
    }

    /**
     * @param Builder $builder
     * @param User $user
     * @return Builder
     */
    public function scopeByUser(Builder $builder, User $user): Builder
    {
        return $builder->where('user_id', $user->id);
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function scopeNotSkipped(Builder $builder): Builder
    {
        return $builder->where('skipped', 0)->whereNotNull('input')->where('input', '<>', '');
    }

    /**
     * @param Builder $builder
     * @param User $user
     * @param ContentItem $contentItem
     * @param Program $program
     *
     * @return Builder
     */
    public function scopeOfUserAndContentItemAndProgram(Builder $builder, User $user, ContentItem $contentItem, Program $program): Builder
    {
        return $builder
            ->where('user_id', $user->id)
            ->where('content_item_id', $contentItem->id)
            ->where('program_id', $program->id)
            ;
    }

    /**
     * @param Builder $builder
     * @param User $user
     *
     * @return Builder
     */
    public function scopeWithSilScores(Builder $builder, User $user): Builder
    {
        return $builder->whereHas('silScores', static function (Builder $builder) use ($user) {
            $builder->where('user_id', $user->id);
        });
    }
}
