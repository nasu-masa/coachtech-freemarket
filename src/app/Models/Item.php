<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'price',
        'brand',
        'condition',
        'status',
        'image_path'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function purchase()
    {
        return $this->hasOne(Purchase::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function myListItems()
    {
        return $this->hasMany(MyListItem::class);
    }

    public function isLikedBy($userId)
    {
        return $this->myListItems()
            ->where('user_id', $userId)
            ->exists();
    }

    public function likesCount()
    {
        return $this->my_list_item_count ?? $this->myListItems()->count();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function latestComment()
    {
        return $this->hasOne(Comment::class)->latestOfMany();
    }

    public function commentsCount()
    {
        return $this->comments_count ?? $this->comments()->count();
    }

    public function scopeSearch($query, $keyword)
    {
        if (!empty($keyword)) {
            return $query->where('name', 'like', '%' . $keyword . '%');
        }

        return $query;
    }

    public static function createFromAttributes(array $attributes)
    {
        $path = $attributes['image']->store('items', 'public');

        unset($attributes['image']);
        $attributes['image_path'] = $path;

        return self::create($attributes);
    }
}
