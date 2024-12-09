<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    protected $primaryKey = 'CategoryID';

    protected $fillable = [
        'CategoryName',
        'Description'
    ];

    public $timestamps = false;
}
