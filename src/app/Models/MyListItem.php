<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MyListItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'item_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public static function myListedItem(User $user, Item $item)
    {
        return MyListItem::where('user_id', $user->id)
            ->where('item_id', $item->id)
            ->first();
    }

    public static function add(User $user,  Item $item)
    {
        return self::firstOrCreate([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    public static function remove(User $user, Item $item)
    {
        $existing = self::myListedItem($user, $item);

        if ($existing) {
            $existing->delete();
            return true;
        }

        return false;
    }
}
