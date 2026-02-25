<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $table = 'assets'; // nama tabel
    protected $guarded = ['id']; // field yang tidak boleh diisi

    public function logs()
    {
        return $this->hasMany(AssetLog::class)->latest();
    }
}
