<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'message',
        'is_read',
        'type',
        'related_id',
        'related_type',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime',
    ];

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Mark as read
    public function markAsRead()
    {
        $this->update(['is_read' => true]);
        return $this;
    }

    // Helper to create notifications
    public static function notify($userId, $title, $message, $type = 'general', $relatedId = null, $relatedType = null)
    {
        return self::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'related_id' => $relatedId,
            'related_type' => $relatedType,
            'is_read' => false,
        ]);
    }

    // Helper to notify multiple users
    public static function notifyMultiple($userIds, $title, $message, $type = 'general', $relatedId = null, $relatedType = null)
    {
        foreach ((array) $userIds as $userId) {
            self::notify($userId, $title, $message, $type, $relatedId, $relatedType);
        }
    }
}

