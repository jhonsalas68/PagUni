<?php

namespace App\Models\Chat;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class UserOnlineStatus extends Model
{
    use HasFactory;

    protected $table = 'user_online_status';

    protected $fillable = [
        'user_id',
        'user_type',
        'last_activity',
        'status',
    ];

    protected $casts = [
        'last_activity' => 'datetime',
    ];

    public function user(): MorphTo
    {
        return $this->morphTo();
    }

    public static function updateUserStatus($userId, $userType, $status = 'online')
    {
        return static::updateOrCreate(
            ['user_id' => $userId, 'user_type' => $userType],
            [
                'last_activity' => now(),
                'status' => $status,
            ]
        );
    }

    public static function getUsersOnlineStatus($userIds, $userType)
    {
        return static::where('user_type', $userType)
            ->whereIn('user_id', $userIds)
            ->where('last_activity', '>=', now()->subMinutes(5)) // Considerado online si actividad en últimos 5 min
            ->pluck('status', 'user_id');
    }

    public static function isUserOnline($userId, $userType)
    {
        $status = static::where('user_id', $userId)
            ->where('user_type', $userType)
            ->where('last_activity', '>=', now()->subMinutes(5))
            ->first();

        return $status && $status->status === 'online';
    }
}