<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_path'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function storeProfile(array $attributes)
    {
        $this->fill($attributes);
        $this->save();
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function purchasedItems()
    {
        return Item::whereIn('id', $this->purchases()->pluck('item_id'));
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function latestAddress()
    {
        return $this->hasOne(Address::class)->latestOfMany();
    }

    public function storeAddress(array $attributes)
    {
        return $this->addresses()->create($attributes);
    }

    public function myListItems()
    {
        return $this->hasMany(MyListItem::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
