<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'item_id',
        'content'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public static function createFromRequest(array $attributes)
    {
        return self::create([
            'user_id' => $attributes['user_id'],
            'item_id' => $attributes['item_id'],
            'content' => $attributes['content'],
        ]);
    }
}
