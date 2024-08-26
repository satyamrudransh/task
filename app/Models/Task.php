<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;

    protected $table='task';
    protected $primaryKey = 'id';

    protected $date = ['deleted_at'];

    protected $fillable = [
        'title','description','status','is_complete'
    ];
    
}
