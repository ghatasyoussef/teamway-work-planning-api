<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * User: User model.
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use SoftDeletes;

    // Later on: Add *MustVerifyEmail* to enforce email to be verified.

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * shifts: the relation for shifts for the user.
     *
     * @return HasMany relationship
     */
    public function shifts(): HasMany
    {
        return $this->hasMany(UserShift::class);
    }

    /**
     * scopeSelf: get the users scoped:
     *          If the user is admin, return all.
     *          If the user is not an admin, return only the user self.
     *
     * @param  mixed  $query
     * @return void
     */
    public function scopeSelf($query)
    {
        $user = auth()->user();
        if ($user->is_admin) {
            return;
        }

        $query->where('id', '=', $user->id);
    }
}
