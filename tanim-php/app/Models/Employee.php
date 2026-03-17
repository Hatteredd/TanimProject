<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = ['name', 'position', 'department', 'base_salary', 'bonus', 'hire_date', 'status', 'notes'];
    protected $casts = ['hire_date' => 'date', 'base_salary' => 'decimal:2', 'bonus' => 'decimal:2'];

    /** 13th month pay = (sum of basic salary earned this calendar year) / 12 */
    public function thirteenthMonth(): float
    {
        $monthsWorkedThisYear = min(12, Carbon::parse($this->hire_date)->diffInMonths(now()) + 1);
        return round(($this->base_salary * $monthsWorkedThisYear) / 12, 2);
    }

    public function yearsOfService(): float
    {
        return round(Carbon::parse($this->hire_date)->diffInMonths(now()) / 12, 1);
    }
}
