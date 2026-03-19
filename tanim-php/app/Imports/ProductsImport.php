<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class ProductsImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    private int $userId;
    public int $imported = 0;

    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            if (empty($row['name'])) continue;

            Product::create([
                'user_id'      => $this->userId,
                'name'         => $row['name'],
                'category'     => $row['category'] ?? 'Uncategorized',
                'brand'        => $row['brand'] ?? null,
                'type'         => $row['type'] ?? null,
                'description'  => $row['description'] ?? '',
                'price'        => (float) ($row['price'] ?? 0),
                'unit'         => $row['unit'] ?? 'piece',
                'stock'        => (int) ($row['stock'] ?? 0),
                'farm_location'=> $row['farm_location'] ?? null,
                'harvest_date' => !empty($row['harvest_date']) ? $row['harvest_date'] : null,
                'is_active'    => true,
            ]);

            $this->imported++;
        }
    }
}
