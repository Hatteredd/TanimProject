<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // Get existing users
        $buyers = User::where('role', 'buyer')->get();
        $admin = User::where('role', 'admin')->first();
        
        if ($buyers->isEmpty()) {
            return; // No buyers to create orders for
        }

        $products = DB::table('products')->get();
        
        // Create sample orders over the past 3 months
        foreach ($buyers as $buyer) {
            $orderCount = rand(2, 5); // Each buyer has 2-5 orders
            
            for ($i = 0; $i < $orderCount; $i++) {
                $orderDate = Carbon::now()->subDays(rand(1, 90));
                $status = ['pending', 'confirmed', 'processing', 'shipped', 'delivered'][array_rand(['pending', 'confirmed', 'processing', 'shipped', 'delivered'])];
                
                // Create order
                $order = Order::create([
                    'user_id' => $buyer->id,
                    'order_number' => 'ORD-' . strtoupper(uniqid()),
                    'total_amount' => 0, // Will be calculated
                    'status' => $status,
                    'shipping_address' => $this->generateRandomAddress(),
                    'contact_number' => $this->generateRandomContactNumber(),
                    'notes' => $this->generateRandomNotes(),
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate,
                ]);

                // Add 2-5 random products to each order
                $selectedProducts = $products->random(rand(2, 5));
                $totalAmount = 0;
                
                foreach ($selectedProducts as $product) {
                    $quantity = rand(1, 5);
                    $unitPrice = $product->price;
                    $itemTotal = $unitPrice * $quantity;
                    $totalAmount += $itemTotal;
                    
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'created_at' => $orderDate,
                        'updated_at' => $orderDate,
                    ]);
                }
                
                // Update order total
                $order->update(['total_amount' => $totalAmount]);
            }
        }
        
        // Create some recent orders for better analytics
        $recentOrders = Order::where('status', 'delivered')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->get();
            
        // Update some orders to different statuses for variety
        Order::where('status', 'pending')->limit(5)->update(['status' => 'confirmed']);
        Order::where('status', 'confirmed')->limit(3)->update(['status' => 'processing']);
        Order::where('status', 'processing')->limit(4)->update(['status' => 'shipped']);
    }
    
    private function generateRandomContactNumber(): ?string
    {
        $numbers = [
            null,
            '0917-123-4567',
            '0928-987-6543',
            '0915-555-1234',
            '0922-777-8888',
            null,
            '0918-999-0000',
            '0927-333-1111',
            null,
        ];
        
        return $numbers[array_rand($numbers)];
    }
    
    private function generateRandomAddress(): string
    {
        $addresses = [
            '123 Katipunan Street, Quezon City, Metro Manila',
            '456 Rizal Avenue, Makati City, Metro Manila',
            '789 Bonifacio Street, Pasig City, Metro Manila',
            '321 Magsaysay Street, Mandaluyong City, Metro Manila',
            '654 Shaw Boulevard, Mandaluyong City, Metro Manila',
            '987 Taft Avenue, Manila City, Metro Manila',
            '147 EDSA, Quezon City, Metro Manila',
            '258 Ayala Avenue, Makati City, Metro Manila',
            '369 Orchard Road, San Juan City, Metro Manila',
        ];
        
        return $addresses[array_rand($addresses)];
    }
    
    private function generateRandomNotes(): ?string
    {
        $notes = [
            null,
            'Please deliver before 6 PM',
            'Leave at the guard',
            'Call me upon arrival',
            'Handle with care',
            'Urgent delivery needed',
            null,
            null,
        ];
        
        return $notes[array_rand($notes)];
    }
}
