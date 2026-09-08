<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BoardModel extends Model
{
    use SoftDeletes;

    /**
     * @var string
     */
    protected $table = 'boards';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'slug'
    ];
}