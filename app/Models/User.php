<?php

namespace App\Models;

use App\Traits\RequestOrderable;
use Cache;
use Eloquent;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Auth\Authenticatable as AuthenticableTrait;
use Jenssegers\Mongodb\Eloquent\Model;
use Str;
use Config;

/**
 * Class User
 * @package App\Models
 * @property int discord_id
 * @property int experience_amount
 * @property int discord_dm_id
 * @property string email
 * @property string avatar
 * @property string username
 * @property string tag
 * @property string role_color
 * @property boolean is_discord_verified
 * @property boolean is_banned
 * @property boolean is_bot
 * @property boolean accept_cgu
 * @property string personal_access_token
 * @property Collection tokens
 * @method static orderable(Request $request): Builder
 * @method static filter(array $all): Builder
 */
class User extends Model implements Authenticatable
{
    use AuthenticableTrait;
    use Notifiable;
    use Filterable;
    use RequestOrderable;

    protected $collection = 'users';

    /**
     * The primary key of the user.
     * @var string
     */
    protected $primaryKey = 'discord_id';

    /**
     * Disable auto increment
     * @var bool
     */
    public $incrementing = false;


    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'discord_id',
        'avatar',
        'email',
        'username',
        'tag',
    ];

    /**
     * The relationships that should always be loaded.
     * @var array
     */
    protected $with = [
    ];

    /**
     * The attributes that should be cast to native types.
     * @var array
     */
    protected $casts = [
        'is_banned' => 'boolean',
        'is_discord_verified' => 'boolean',
        'accept_cgu' => 'boolean',
        'is_bot' => 'boolean'
    ];

    /**
     * The attributes that should be casted as datetime.
     * @var array
     */
    protected $dates = [
        'last_activity',
        'created_at',
        'deleted_at',
    ];

    /**
     * Return the owner key
     * @inheritDoc
     */
    public function ownerKey($owner)
    {
        return $this->discord_id;
    }

    /**
     * The "booting" method of the model.
     * @return void
     */
    public static function boot(): void
    {
        parent::boot();
    }

    /**
     * Return the unique identifier of the user
     * @return integer
     */
    // public function getIdAttribute(): int
    // {
    //     return $this->discord_id;
    // }

    /**
     * Get all tokens of user.
     * @return HasMany
     */
    public function tokens(): HasMany
    {
        return $this->hasMany(Token::class, 'discord_id');
    }

    /**
     * Return the personal token of the user.
     * @return Token|null
     */
    public function personalToken(): ?Token
    {
        return $this->tokens->firstWhere('is_personal', '=', '1');
    }

    /**
     * Appending access_token attribute to the current User.
     * @return string|null
     */
    public function getPersonalAccessTokenAttribute(): ?string
    {
        return $this->personalToken() !== null ? $this->personalToken()->access_token : null;
    }
}
