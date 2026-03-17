<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = ['type', 'label', 'amount', 'expense_date', 'recurring', 'recurring_period', 'notes'];
    protected $casts = ['expense_date' => 'date', 'recurring' => 'boolean', 'amount' => 'decimal:2'];

    public static function types(): array {
        return [
            'electricity' => '⚡ Electricity',
            'water'       => '💧 Water',
            'internet'    => '🌐 Internet',
            'maintenance' => '🔧 Maintenance',
            'salary'      => '💼 Salaries',
            'delivery'    => '🚚 Deliveries',
            'restock'     => '📦 Restock',
            'upcoming_stock' => '🔮 Upcoming Stock',
            'other'       => '📋 Other',
        ];
    }
}
