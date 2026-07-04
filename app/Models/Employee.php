<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'position', 'monthly_salary', 'joining_date', 'status'];

    protected $casts = [
        'joining_date' => 'date',
        'monthly_salary' => 'float',
        'status' => 'boolean',
    ];

    public function salaryPayments()
    {
        return $this->hasMany(SalaryPayment::class);
    }
}
