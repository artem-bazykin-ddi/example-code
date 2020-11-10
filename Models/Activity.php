<?php
declare(strict_types=1);

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Activity
 *
 * @property-read User $user
 * @method static Builder|Activity newModelQuery()
 * @method static Builder|Activity newQuery()
 * @method static Builder|Activity query()
 * @mixin Eloquent
 * @property int $id
 * @property string $type
 * @property mixed $payloads
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $user_id
 * @method static Builder|Activity whereCreatedAt($value)
 * @method static Builder|Activity whereId($value)
 * @method static Builder|Activity wherePayloads($value)
 * @method static Builder|Activity whereRelated($value)
 * @method static Builder|Activity whereType($value)
 * @method static Builder|Activity whereUpdatedAt($value)
 * @method static Builder|Activity whereUserId($value)
 * @property string|null $message
 * @method static Builder|Activity whereMessage($value)
 * @property string|null $messageMe
 * @property string|null $messageCompany
 * @method static Builder|Activity whereMessageCompany($value)
 * @method static Builder|Activity whereMessageMe($value)
 * @property int $viewed
 * @method static Builder|Activity whereViewed($value)
 * @property-read Collection|User[] $viewers
 * @property-read int|null $viewers_count
 * @method static Builder|Activity notViewed(User $user)
 * @property-read Collection|UserViewedActivity[] $viewObjects
 * @property-read int|null $view_objects_count
 * @method static Builder|Activity ofUser(User $user)
 * @property string|null $messageme
 * @property string|null $messagecompany
 */
class Activity extends Model
{
    protected $table = 'activity';



    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'type',
        'payloads'
    ];

    /**
     * @param Builder $builder
     * @param User $user
     *
     * @return Builder
     */
    public function scopeNotViewed(Builder $builder, User $user): Builder
    {
        $viewedIds = UserViewedActivity::where('user_id', $user->id)
            ->pluck('activity_id')
            ->toArray();

        return $builder->whereNotIn('id', $viewedIds)
            ->where('user_id', '<>', $user->id)
            ->whereHas('user', static function (Builder $builder) use ($user) {
                $builder->where('client_id', $user->client_id);
            });
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsToMany
     */
    public function viewers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_viewed_activities');
    }

    /**
     * @return HasMany
     */
    public function viewObjects(): HasMany
    {
        return $this->hasMany(UserViewedActivity::class);
    }

    /**
     * @param Builder $builder
     * @param User $user
     *
     * @return Builder
     */
    public function scopeOfUser(Builder $builder, User $user): Builder
    {
        return $builder->where($this->user()->getForeignKeyName(), $user->id);
    }
}
