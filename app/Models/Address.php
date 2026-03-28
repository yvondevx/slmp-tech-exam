<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'street', 'suite', 'city', 'zipcode', 'lat', 'lng'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
