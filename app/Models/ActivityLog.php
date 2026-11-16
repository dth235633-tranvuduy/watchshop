<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'action', 'description', 'route_name', 'method', 'url', 'ip_address', 'user_agent'])]
class ActivityLog extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function record(
        ?User $user,
        string $action,
        ?string $description = null,
        ?string $routeName = null,
        string $method = 'GET',
        string $url = '',
        ?string $ipAddress = null,
        ?string $userAgent = null,
    ): self {
        return self::create([
            'user_id' => $user?->id,
            'action' => $action,
            'description' => $description,
            'route_name' => $routeName,
            'method' => $method,
            'url' => $url,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);
    }
}
