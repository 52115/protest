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

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function profile()
    {
        return $this->hasOne('App\Models\Profile');
    }

    public function likes()
    {
        return $this->hasMany('App\Models\Like');
    }

    public function comments()
    {
        return $this->hasMany('App\Models\Comment');
    }  

    public function items()
    {
        return $this->hasMany('App\Models\Item');
    }

    public function buyerTransactions()
    {
        return $this->hasMany('App\Models\Transaction', 'buyer_id');
    }

    public function sellerTransactions()
    {
        return $this->hasMany('App\Models\Transaction', 'seller_id');
    }

    public function transactionMessages()
    {
        return $this->hasMany('App\Models\TransactionMessage');
    }

    public function ratingsGiven()
    {
        return $this->hasMany('App\Models\Rating', 'rater_id');
    }

    public function ratingsReceived()
    {
        return $this->hasMany('App\Models\Rating', 'rated_user_id');
    }

    public function getAverageRating()
    {
        $ratings = $this->ratingsReceived()->get();
        if ($ratings->count() === 0) {
            return null;
        }
        $average = $ratings->avg('rating');
        return round($average);
    }
}
