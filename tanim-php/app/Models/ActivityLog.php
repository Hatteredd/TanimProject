<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id', 'action', 'subject_type', 'subject_id',
        'description', 'ip_address', 'user_agent', 'properties',
    ];

    protected $casts = ['properties' => 'array'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function record(string $action, string $description, $subject = null, array $properties = []): void
    {
        static::create([
            'user_id'      => auth()->id(),
            'action'       => $action,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id'   => $subject?->id,
            'description'  => $description,
            'ip_address'   => request()->ip(),
            'user_agent'   => request()->userAgent(),
            'properties'   => $properties ?: null,
        ]);
    }

    public function actionColor(): string
    {
        return match($this->action) {
            'login'   => '#2563eb',
            'logout'  => '#6b7280',
            'create'  => '#16a34a',
            'update'  => '#d97706',
            'delete'  => '#dc2626',
            'export'  => '#7c3aed',
            'import'  => '#0891b2',
            default   => '#6b7280',
        };
    }
}
