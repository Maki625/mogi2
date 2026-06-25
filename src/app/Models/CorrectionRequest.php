<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorrectionRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'attendance_id',
        'clock_in',
        'clock_out',
        'reason',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
    'clock_in' => 'datetime',
    'clock_out' => 'datetime',
    'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function correctionWorkbreaks()
    {
        return $this->hasMany(CorrectionWorkbreak::class);
    }

    public function approvedBy()
    {
    return $this->belongsTo(User::class, 'approved_by');
    }

}
