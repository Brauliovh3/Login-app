<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'title',
        'message',
        'type',
        'user_id',
        'read_status',
    ];
    
    protected $casts = [
        'read_status' => 'boolean',
    ];
    
    /**
     * Relación con el usuario
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
