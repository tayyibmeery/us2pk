<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryPayment extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id', 'month', 'year', 'amount', 'paid_date', 'notes'];

    protected $casts = [
        'paid_date' => 'date',
        'amount' => 'float',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
