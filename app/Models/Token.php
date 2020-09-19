<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Jenssegers\Mongodb\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Str;

/**
 * @property string id
 * @property bool is_revokable
 * @property bool is_personal
 * @property int discord_id
 * @property Carbon expires_at
 * @property Carbon created_at
 * @property Carbon deleted_at
 * @property RefreshToken refreshToken
 * @property string access_token
 */
class Token extends Model
{
    use UsesUuid;
    
    protected $collection = 'tokens';

    /**
     * Mass assignable attributes
     * @var array
     */
    protected $fillable = [
        'is_revokable',
        'is_personal',
        'access_token',
        'discord_id'
    ];

    /**
     * The attributes that should be cast to native types.
     * @var array
     */
    protected $casts = [
        'is_revokable' => 'boolean',
        'is_personal' => 'boolean'
    ];

    /**
     * The attributes that should be casted as datetime.
     * @var array
     */
    protected $dates = [
        'expires_at',
        'created_at',
        'deleted_at'
    ];

    /**
     * The "booting" method of the model.
     * @return void
     */
    public static function boot(): void
    {
        parent::boot();
        self::created(static function (Token $token) {
            // Only create refresh token for personal token
            $token->expires_at = Carbon::now()->addDays(7);
            $token->save();
        });
    }

    /**
     * Get the owner of the token.
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'discord_id', 'discord_id');
    }

    /**
     * Scope a query to only include only revokable tokens.
     * @param  Builder $query
     * @return Builder
     */
    public function scopeIsRevokable($query): Builder
    {
        return $query->where('is_revokable', '=', '1');
    }

    /**
     * Scope a query to only include only personal tokens.
     * @param  Builder $query
     * @return Builder
     */
    public function scopeIsPersonal($query): Builder
    {
        return $query->where('is_personal', '=', '1');
    }

    /**
     * Method used to refresh a token without deleting it.
     * @param int|null $minutes
     * @return void
     */
    public function extendTime(int $minutes = null): void
    {
        $this->expires_at = $minutes !== null && is_int($minutes)
            ? Carbon::now()->addMinutes($minutes)
            : Carbon::now()->addDays(7) ;
        $this->access_token = Str::random(110);
        $this->save();
    }

    /**
     * Determine if the token is expired or not.
     * @return bool
     * @throws Exception
     */
    public function isExpired(): bool
    {
        if ($this->expires_at === null) {
            return false;
        }

        return now()->greaterThan(new Carbon($this->expires_at));
    }

    /**
     * Determine if the token is not expired.
     * @return bool
     * @throws Exception
     */
    public function isNotExpired(): bool
    {
        return !$this->isExpired();
    }
}
