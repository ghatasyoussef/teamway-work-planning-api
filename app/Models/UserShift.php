<?php

namespace App\Models;

use App\Models\Traits\OwnerScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * UserShift: The user shift model.
 */
class UserShift extends Model
{
    use HasFactory;
    use OwnerScopeTrait;
    protected $table = 'user_shifts';

    protected $fillable = [
        'worker_id',
        'shift_number',
        'day',
    ];

    /**
     * user: The relatioship between the shift and the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'worker_id');
    }
}
