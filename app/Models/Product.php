<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'title',
        'description',
        'slug',
        'gender',
        'price',
        'stock',
        'sizes',
        'tags',
        'user_id'
    ];

    protected $casts = [
        'sizes' => 'array',
        'tags' => 'array'
    ];

    public function getSizesAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    public function getTagsAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }
}
