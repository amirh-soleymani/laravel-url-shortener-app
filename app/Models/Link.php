<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'original_url',
        'shortener_url',
        'user_id',
        'count'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
