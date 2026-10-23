<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'description', 'price', 'tone'])]
class Product extends Model
{
    public function priceForHumans(): string
    {
        return money($this->price);
    }
}
