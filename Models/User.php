<?php
declare(strict_types=1);

namespace App\Models;

use App\Models\Interfaces\ConsecutiveDaysInterface;
use App\Models\Interfaces\ModelPermissionInterface;
use App\Models\Interfaces\SilScoreInterface;
use App\Models\Interfaces\StoreMagicTokenInterface;
use App\Models\Traits\PermissionTrait;
use DateTimeInterface;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Carbon;
use Laravel\Passport\HasApiTokens;
use LogicException;

/**
 * App\Models\User
 *
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @mixin Eloquent
 * @property int $id
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string|null $password
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $magic_token
 * @property string|null $token
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereEmailVerifiedAt($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereMagicToken($value)
 * @method static Builder|User whereName($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User whereToken($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @property string|null $first_name
 * @property string $last_name
 * @property string|null $job_role
 * @property string|null $job_dept
 * @property int|null $client_id
 * @property int $permission
 * @property string|null $tip_time
 * @property string|null $timezone
 * @method static Builder|User whereClientId($value)
 * @method static Builder|User whereFirstName($value)
 * @method static Builder|User whereJobDept($value)
 * @method static Builder|User whereJobRole($value)
 * @method static Builder|User whereLastName($value)
 * @method static Builder|User wherePermission($value)
 * @method static Builder|User whereTimezone($value)
 * @method static Builder|User whereTipTime($value)
 * @property-read Collection|UserReflection[] $reflections
 * @property-read Collection|ScheduleTopic[] $scheduledEvents
 * @property-read string $name
 * @property string|null $phone
 * @method static Builder|User wherePhone($value)
 * @property-read int|null $notifications_count
 * @property-read int|null $reflections_count
 * @property-read int|null $scheduled_events_count
 * @property-read Client|null $client
 * @property string|null $avatar
 * @method static Builder|User whereAvatar($value)
 * @property-read AboutMeSettings $aboutMeSettings
 * @property-read AboutMeSettings $activity
 * @property-read int|null $activity_count
 * @property int|null $sil_score
 * @property string|null $last_seen
 * @property int|null $sequence_days
 * @method static Builder|User whereLastSeen($value)
 * @method static Builder|User whereSequenceDays($value)
 * @method static Builder|User whereSilScore($value)
 * @method static Builder|User ofUser(User $user)
 * @method static Builder|User findByEmailAndPassword($email, $password)
 * @method static Builder|User findByAdminRoles()
 * @method static Builder|User getByEmail($email)
 * @property-read Collection|SilScore[] $silScoreActivity
 * @property-read int|null $sil_score_activity_count
 * @property-read Collection|Device[] $devices
 * @property-read int|null $devices_count
 * @method static Builder|User getByActivitySharing()
 * @property-read Collection|ContentItem[] $viewedTips
 * @property-read int|null $viewed_tips_count
 * @property string|null $last_tip_at
 * @method static Builder|User whereLastTipAt($value)
 * @property-read mixed $author_name
 * @property-read Collection|AboutMeSettings[] $settings
 * @property-read int|null $settings_count
 * @method static Builder|User practiceNotificationsOn()
 * @method static Builder|User randomActsNotificationsOn()
 * @method static Builder|User withRegisteredDevices()
 * @property string|null $onboarding_reflection
 * @method static Builder|User whereOnboardingReflection($value)
 * @property-read Collection|SilScore[] $silScores
 * @property-read int|null $sil_scores_count
 * @property string|null $onboarding_at
 * @method static Builder|User whereOnboardingAt($value)
 * @method static Builder|User byMagicToken($magicToken)
 * @property int $reset_magic_token
 * @method static Builder|User whereResetMagicToken($value)
 */
class User extends Authenticatable implements StoreMagicTokenInterface, ModelPermissionInterface, SilScoreInterface, ConsecutiveDaysInterface
{
    use Notifiable;
    use PermissionTrait;
    use HasApiTokens;

    public const DEFAULT_TIP_TIME = '08:00 PM';

    /**
     * @var string
     */
    protected $table = 'users';

    public static $liRoles = [self::LI_ADMIN, self::LI_CONTENT_EDITOR];
    public static $clientRoles = [self::CLIENT_ADMIN, self::APP_USER];

    public static $adminRoles = [self::LI_ADMIN, self::LI_CONTENT_EDITOR, self::CLIENT_ADMIN];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'magic_token',
        'phone',
        'job_role',
        'job_dept',
        'tip_time',
        'permission',
    ];

    /**
     * @var array
     */
    protected $dates = [
        'last_seen'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @param string $magicToken
     *
     * @return User|null
     */
    public static function getByMagicToken(string $magicToken): ?User
    {
        return self::byMagicToken($magicToken)->first();
    }

    /**
     * @param Builder $builder
     * @param string $magicToken
     *
     * @return Builder
     */
    public function scopeByMagicToken(Builder $builder, string $magicToken): Builder
    {
        return $builder->where('magic_token', $magicToken);
    }

    /**
     * @param DateTimeInterface $dateTime
     */
    public function setEmailVerifyAt(DateTimeInterface $dateTime): void
    {
        $this->attributes['email_verified_at'] = $dateTime->format('Y-m-d H:i:s');
    }

    /**
     * @param string $email
     *
     * @return Model|User|null
     */
    public static function findByEmail(string $email): ?User
    {
        return self::query()->where('email', $email)->first();
    }

    /**
     * @param string $token
     *
     * @return Model|User|null
     */
    public function findByToken(string $token): ?User
    {
        return self::query()->where('token', $token)->first();
    }

    /**
     * @param Builder $builder
     * @param string $email
     * @return Builder
     */
    public function scopeGetByEmail(Builder $builder, string $email): Builder
    {
        return $builder->where('email', $email);
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function scopeFindByAdminRoles(Builder $builder): Builder
    {
        return $builder->whereIn('permission', self::$adminRoles);
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function scopeGetByActivitySharing(Builder $builder): Builder
    {
        return $builder->whereHas('aboutMeSettings', static function (Builder $builder) {
            $builder->where('share_activity', true);
        })->orDoesntHave('aboutMeSettings');
    }

    /**
     * @return string
     */
    public function getMagicToken(): string
    {
        return $this->magic_token;
    }

    /**
     * @return HasMany
     */
    public function reflections(): HasMany
    {
        return $this->hasMany(UserReflection::class);
    }

    /**
     * @return HasMany
     */
    public function scheduledEvents(): HasMany
    {
        return $this->hasMany(ScheduleTopic::class);
    }

    /**
     * @return string
     */
    public function getNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * @param $value
     */
    public function setNameAttribute($value): void
    {
        $exploded = explode(' ', $value);
        $this->attributes['first_name'] = $exploded[0] ?? '';
        $this->attributes['last_name'] = $exploded[1] ?? '';
    }

    /**
     * @return array
     */
    public static function getAvailablePermissions(): array
    {
        return [
            self::LI_ADMIN,
            self::LI_CONTENT_EDITOR,
            self::CLIENT_ADMIN,
            self::APP_USER,
        ];
    }

    /**
     * @return array
     */
    public static function getPermissionsTitle(): array
    {
        return [
            self::LI_ADMIN => 'LI Admin',
            self::LI_CONTENT_EDITOR => 'LI Editor',
            self::CLIENT_ADMIN => 'Client Admin',
            self::APP_USER => 'Application User',
        ];
    }

    /**
     * @return string
     */
    public function getPermissionTitle(): string
    {
        if (!isset(self::getPermissionsTitle()[$this->permission])) {
            throw new LogicException('Could not resolve permission '.$this->permission);
        }
        return self::getPermissionsTitle()[$this->permission];
    }

    /**
     * @return BelongsTo
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * @return HasOne
     */
    public function aboutMeSettings(): HasOne
    {
        return $this->hasOne(AboutMeSettings::class);
    }

    /**
     * @return HasMany
     */
    public function activity(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * @return HasMany
     */
    public function silScores(): HasMany
    {
        return $this->hasMany(SilScore::class);
    }

    /**
     * @param Carbon $date
     */
    public function setLastSeen(Carbon $date): void
    {
        $this->last_seen = $date;
    }

    /**
     * @return Carbon|null
     */
    public function getLastSeen(): ?Carbon
    {
        return $this->last_seen;
    }

    /**
     * @param int $daysCount
     */
    public function setSequenceDays(int $daysCount): void
    {
        $this->sequence_days = $daysCount;
    }

    /**
     * @return int|null
     */
    public function getSequenceDays(): ?int
    {
        return $this->sequence_days;
    }

    /**
     * @param Builder $builder
     * @param User $user
     *
     * @return Builder
     */
    public function scopeOfUser(Builder $builder, User $user): Builder
    {
        if ($user->permission === self::CLIENT_ADMIN) {
            $builder->where('client_id', $user->client->id);
        }

        return $builder;
    }

    /**
     * @return BelongsToMany
     */
    public function devices(): BelongsToMany
    {
        return $this->belongsToMany(Device::class, 'devices_users');
    }

    /**
     * @return BelongsToMany
     */
    public function viewedTips(): BelongsToMany
    {
        return $this->belongsToMany(ContentItem::class, 'viewed_tips', 'user_id');
    }

    /**
     * @return string
     */
    public function getAuthorNameAttribute(): string
    {
        if (in_array($this->permission, self::$clientRoles, true)) {
            return $this->client->name;
        }

        return $this->name;
    }

    /**
     * @return HasMany
     */
    public function settings(): HasMany
    {
        return $this->hasMany(AboutMeSettings::class);
    }

    /**
     * @return string
     */
    public static function getConsecutiveDaysKey(): string
    {
        return 'sequence_days';
    }

    /**
     * @return string
     */
    public static function getLastSeenKey(): string
    {
        return 'last_seen';
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopePracticeNotificationsOn(Builder $builder): Builder
    {
        return $builder->where(static function(Builder $builder) {
            $builder->whereDoesntHave('settings')
                ->where(static function (Builder $builder) {
                    $builder->when(AboutMeSettings::ABOUT_ME_SETTINGS_DEFAULTS['event_practice_notification'] === false, static function (Builder $builder) {
                        $builder->whereNull('id');
                    });
                })->orWhereHas('settings', static function (Builder $builder) {
                    $builder->where('event_practice_notification', true);
                });
        });
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeRandomActsNotificationsOn(Builder $builder): Builder
    {
        return $builder->where(static function(Builder $builder) {
            $builder->whereDoesntHave('settings')
                ->where(static function (Builder $builder) {
                    $builder->when(AboutMeSettings::ABOUT_ME_SETTINGS_DEFAULTS['random_acts_notification'] === false, static function (Builder $builder) {
                        $builder->whereNull('id');
                    });
                })->orWhereHas('settings', static function (Builder $builder) {
                    $builder->where('random_acts_notification', true);
                });
        });
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeWithRegisteredDevices(Builder $builder): Builder
    {
        return $builder->whereHas('devices', static function (Builder $builder) {
            $builder->whereNotNull('registration_id');
        });
    }

    /**
     * @return HasMany
     */
    public function progresses(): HasMany
    {
        return $this->hasMany(ContentItemUserProgress::class);
    }
}
