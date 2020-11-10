<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;


/**
 * App\Models\UserActivity
 *
 * @property int $id
 * @property int $user_id
 * @property string $type
 * @property mixed $meta
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|UserActivity newModelQuery()
 * @method static Builder|UserActivity newQuery()
 * @method static Builder|UserActivity query()
 * @method static Builder|UserActivity whereCreatedAt($value)
 * @method static Builder|UserActivity whereId($value)
 * @method static Builder|UserActivity whereMeta($value)
 * @method static Builder|UserActivity whereType($value)
 * @method static Builder|UserActivity whereUpdatedAt($value)
 * @method static Builder|UserActivity whereUserId($value)
 * @mixin Eloquent
 * @property int $viewed
 * @method static Builder|UserActivity whereViewed($value)
 */
class UserActivity extends Model
{

}
