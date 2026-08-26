<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Key extends Model
{
    protected $fillable = [
        'key','status','duration','expires_at','max_uses','used_count','note',
        'username','password','display_name','phone','device_name','device_id',
        'android_version','app_version','ip_address','last_login_at','created_by',
    ];
    protected $casts = [
        'expires_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}
