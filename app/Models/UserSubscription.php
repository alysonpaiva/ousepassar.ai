<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSubscription extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "user_subscriptions";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "user_id",
        "guru_subscription_id",
        "guru_transaction_id",
        "guru_product_id",
        "guru_plan_name",
        "status",
        "started_at",
        "expires_at",
        "canceled_at",
        "last_event_at",
        "webhook_payload",
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        "started_at" => "datetime",
        "expires_at" => "datetime",
        "canceled_at" => "datetime",
        "last_event_at" => "datetime",
        "webhook_payload" => "array", // Cast JSON payload to array
    ];

    /**
     * Get the user that owns the subscription.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include active subscriptions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where("status", "active")
            ->where(function ($q) {
                $q->whereNull("expires_at")
                    ->orWhere("expires_at", ">", now());
            });
    }

    /**
     * Check if the subscription is currently active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === "active" && (is_null($this->expires_at) || $this->expires_at->isFuture());
    }
}
