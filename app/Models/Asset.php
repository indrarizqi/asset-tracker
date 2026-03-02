<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $table = 'assets'; // nama tabel
    protected $guarded = ['id']; // field yang tidak boleh diisi

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expiry_date' => 'date',
    ];

    public function logs()
    {
        return $this->hasMany(AssetLog::class)->latest();
    }

    public function transactions()
    {
        return $this->hasMany(AssetTransaction::class)->latest('borrowed_at');
    }

    public function activeTransaction()
    {
        return $this->hasOne(AssetTransaction::class)
            ->whereNull('returned_at')
            ->latestOfMany('borrowed_at');
    }

    public function attachments()
    {
        return $this->hasMany(AssetAttachment::class)->latest();
    }
}
