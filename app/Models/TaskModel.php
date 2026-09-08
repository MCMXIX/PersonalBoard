<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskModel extends Model
{
    use SoftDeletes;

    /**
     * @var string
     */
    protected $table = 'tasks';

    /**
     * @var array
     */
    protected $fillable = [
        'board_id',
        'name',
        'type',
        'priority'
    ];

    /**
     * Cast created at date to format date
     * @return array
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime:M d, Y h:i A',
        ];
    }
}