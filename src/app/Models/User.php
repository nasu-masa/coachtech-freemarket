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

    public static function storeRegister(array $attributes)
    {
        $user = self::create([
            'name'     => $attributes['name'],
            'email'    => $attributes['email'],
            'password' => $attributes['password']
        ]);

        return $user;
    }

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
        return Item::whereIn(
            'id',
            $this->purchases()->pluck('item_id')
        )->get();
    }

    public function purchaseItem(Item $item, string $paymentMethod): Purchase
    {
        $item->update(['status' => 'sold']);

        return $this->purchases()->create([
            'item_id'        => $item->id,
            'address_id'     => $this->latestAddress->id,
            'payment_method' => $paymentMethod,
            'purchased_at'   => now(),
        ]);
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
        return $this->addresses()->create([
            'postal_code' => $attributes['postal_code'],
            'address'     => $attributes['address'],
            'building'    => $attributes['building'],
        ]);
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
