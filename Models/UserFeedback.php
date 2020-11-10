<?php
declare(strict_types=1);

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\UserFeedback
 *
 * @property int $id
 * @property int $user_id
 * @property int $content_item_id
 * @property int $reaction
 * @property string|null $response
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read ContentItem $contentItem
 * @property-read User $user
 * @method static Builder|UserFeedback newModelQuery()
 * @method static Builder|UserFeedback newQuery()
 * @method static Builder|UserFeedback query()
 * @method static Builder|UserFeedback whereContentItemId($value)
 * @method static Builder|UserFeedback whereCreatedAt($value)
 * @method static Builder|UserFeedback whereId($value)
 * @method static Builder|UserFeedback whereReaction($value)
 * @method static Builder|UserFeedback whereResponse($value)
 * @method static Builder|UserFeedback whereUpdatedAt($value)
 * @method static Builder|UserFeedback whereUserId($value)
 * @mixin Eloquent
 * @method static Builder|UserFeedback bookmarked(User $user)
 */
class UserFeedback extends Model
{
    protected $fillable = ['user_id', 'content_item_id', 'reaction'];



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
     * @param Builder $builder
     * @param User $user
     *
     * @return Builder
     */
    public function scopeBookmarked(Builder $builder, User $user): Builder
    {
        return $builder->where('reaction', 1)->where('user_id', $user->id);
    }
}
