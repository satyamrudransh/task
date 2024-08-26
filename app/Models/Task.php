<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;
    use HasFactory; // This trait enables the use of the factory

    protected $table='task';
    protected $primaryKey = 'id';

    protected $date = ['deleted_at'];

    protected $fillable = [
        'title','is_complete'
    ];
    
}
