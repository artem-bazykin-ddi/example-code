<?php
declare(strict_types=1);

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\UserViewedActivity
 *
 * @property-read Activity $activity
 * @property-read User $user
 * @method static Builder|UserViewedActivity newModelQuery()
 * @method static Builder|UserViewedActivity newQuery()
 * @method static Builder|UserViewedActivity query()
 * @mixin Eloquent
 * @property int $id
 * @property int $user_id
 * @property int $activity_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|UserViewedActivity whereActivityId($value)
 * @method static Builder|UserViewedActivity whereCreatedAt($value)
 * @method static Builder|UserViewedActivity whereId($value)
 * @method static Builder|UserViewedActivity whereUpdatedAt($value)
 * @method static Builder|UserViewedActivity whereUserId($value)
 */
class UserViewedActivity extends Model
{
    protected $table = 'user_viewed_activities';



    /**
     * @return BelongsTo
     */
    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
