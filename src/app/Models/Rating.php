<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'rater_id',
        'rated_user_id',
        'item_id',
        'rating',
    ];

    public function rater()
    {
        return $this->belongsTo('App\Models\User', 'rater_id');
    }

    public function ratedUser()
    {
        return $this->belongsTo('App\Models\User', 'rated_user_id');
    }

    public function item()
    {
        return $this->belongsTo('App\Models\Item');
    }
}
