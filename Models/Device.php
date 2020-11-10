<?php
declare(strict_types=1);

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Device
 *
 * @property-read Collection|User[] $users
 * @property-read int|null $users_count
 * @method static Builder|Device newModelQuery()
 * @method static Builder|Device newQuery()
 * @method static Builder|Device query()
 * @mixin Eloquent
 * @property int $id
 * @property string $device_id
 * @property string $device_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Device whereCreatedAt($value)
 * @method static Builder|Device whereDeviceId($value)
 * @method static Builder|Device whereDeviceToken($value)
 * @method static Builder|Device whereId($value)
 * @method static Builder|Device whereUpdatedAt($value)
 * @property string $type
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereType($value)
 * @property string|null $registration_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereRegistrationId($value)
 */
class Device extends Model
{


    /**
     * @var array
     */
    protected $fillable = [
        'device_id',
        'type',
        'device_token'
    ];

    /**
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'devices_users');
    }
}
