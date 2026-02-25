<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetLog extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Otomatis mengubah kolom JSON menjadi Array di PHP
    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
        'approved_at' => 'datetime',
    ];

    // Relasi ke tabel Assets
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    // Relasi ke user yang membuat request (Maker)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke Super Admin yang menyetujui (Checker)
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}