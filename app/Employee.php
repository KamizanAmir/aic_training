<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Employee extends Model
{
    protected $fillable = [
        'emp_id',
        'emp_name',
        'department',
        'labour_type',
        'status',
        'trainer_emp',
        'training_type',
        'training_hours',
        'training_date',
        'expired_date',
    ];
}
