<?php
declare(strict_types=1);

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\EmailVerification
 *
 * @property int $id
 * @property string $token
 * @property string $expired_at
 * @property int $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|EmailVerification newModelQuery()
 * @method static Builder|EmailVerification newQuery()
 * @method static Builder|EmailVerification query()
 * @method static Builder|EmailVerification whereActive($value)
 * @method static Builder|EmailVerification whereCreatedAt($value)
 * @method static Builder|EmailVerification whereExpiredAt($value)
 * @method static Builder|EmailVerification whereId($value)
 * @method static Builder|EmailVerification whereToken($value)
 * @method static Builder|EmailVerification whereUpdatedAt($value)
 * @mixin Eloquent
 * @property int $user_id
 * @method static Builder|EmailVerification whereUserId($value)
 * @property-read User $user
 * @property string $host
 * @method static Builder|EmailVerification whereHost($value)
 */
class EmailVerification extends Model
{


    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
