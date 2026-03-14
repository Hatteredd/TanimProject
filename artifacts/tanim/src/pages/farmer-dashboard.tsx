import { useGetFarmerStats, useGetFarmerProducts } from "@workspace/api-client-react";
import { Navbar } from "@/components/navbar";
import { Card } from "@/components/ui/card";
import { Package, DollarSign, AlertTriangle, TrendingUp } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";

export default function FarmerDashboard() {
  const { data: stats } = useGetFarmerStats();
  const { data: productsData } = useGetFarmerProducts({ limit: 5 });

  const statCards = [
    { title: "Total Sales", value: `$${stats?.total_revenue?.toFixed(2) || '0.00'}`, icon: DollarSign },
    { title: "Orders Received", value: stats?.total_orders || 0, icon: TrendingUp },
    { title: "Active Products", value: stats?.total_products || 0, icon: Package },
    { title: "Low Stock Alerts", value: stats?.low_stock_products || 0, icon: AlertTriangle, alert: (stats?.low_stock_products || 0) > 0 },
  ];

  return (
    <div className="min-h-screen bg-gray-50 flex flex-col">
      <Navbar />
      
      <main className="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
        <div className="flex justify-between items-center mb-8">
          <h1 className="text-3xl font-display font-bold text-foreground">Farmer Portal</h1>
          <Button className="rounded-xl shadow-lg shadow-primary/20">Add New Product</Button>
        </div>

        {/* Stats Grid */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
          {statCards.map((s, i) => (
            <Card key={i} className={`p-6 border-border/50 shadow-sm rounded-2xl ${s.alert ? 'border-orange-200 bg-orange-50/50' : ''}`}>
              <div className="flex justify-between items-start">
                <div>
                  <p className="text-sm font-medium text-muted-foreground mb-1">{s.title}</p>
                  <h3 className={`text-2xl font-bold ${s.alert ? 'text-orange-600' : 'text-foreground'}`}>{s.value}</h3>
                </div>
                <div className={`${s.alert ? 'bg-orange-100' : 'bg-primary/10'} p-3 rounded-xl`}>
                  <s.icon className={`h-5 w-5 ${s.alert ? 'text-orange-600' : 'text-primary'}`} />
                </div>
              </div>
            </Card>
          ))}
        </div>

        <div className="bg-white rounded-3xl border shadow-sm overflow-hidden">
          <div className="p-6 border-b flex justify-between items-center bg-gray-50/50">
            <h3 className="font-bold text-lg font-display">Recent Products</h3>
            <Button variant="ghost" className="text-primary">View All</Button>
          </div>
          <div className="p-0">
            <table className="w-full text-left text-sm">
              <thead className="border-b bg-gray-50/50 text-muted-foreground">
                <tr>
                  <th className="font-medium p-4 pl-6">Product</th>
                  <th className="font-medium p-4">Price</th>
                  <th className="font-medium p-4">Stock</th>
                  <th className="font-medium p-4">Status</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-border/50">
                {productsData?.products?.map((prod) => (
                  <tr key={prod.id} className="hover:bg-gray-50/50 transition-colors">
                    <td className="p-4 pl-6 flex items-center gap-3">
                      <img 
                        src={prod.image_url || `https://images.unsplash.com/photo-1595858643806-0d1b31fb5e74?w=100&q=80&sig=${prod.id}`}
                        className="w-10 h-10 rounded-lg object-cover" 
                        alt=""
                      />
                      <span className="font-semibold">{prod.name}</span>
                    </td>
                    <td className="p-4 text-muted-foreground">${prod.price.toFixed(2)} / {prod.unit}</td>
                    <td className="p-4">
                      <span className={`font-semibold ${prod.stock_quantity <= prod.low_stock_threshold ? 'text-orange-600' : 'text-foreground'}`}>
                        {prod.stock_quantity}
                      </span>
                    </td>
                    <td className="p-4">
                      {prod.stock_quantity > 0 ? (
                        <Badge className="bg-green-100 text-green-800 hover:bg-green-100 border-green-200">Active</Badge>
                      ) : (
                        <Badge variant="secondary">Out of Stock</Badge>
                      )}
                    </td>
                  </tr>
                ))}
                {!productsData?.products?.length && (
                  <tr>
                    <td colSpan={4} className="p-8 text-center text-muted-foreground">No products listed yet.</td>
                  </tr>
                )}
              </tbody>
            </table>
          </div>
        </div>
      </main>
    </div>
  );
}
