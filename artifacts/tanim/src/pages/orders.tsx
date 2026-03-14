import { useLocation } from "wouter";
import { useListOrders, useGetMe } from "@workspace/api-client-react";
import { Navbar } from "@/components/navbar";
import { Badge } from "@/components/ui/badge";
import { format } from "date-fns";
import { Package, ArrowRight } from "lucide-react";
import { Button } from "@/components/ui/button";

export default function Orders() {
  const [, setLocation] = useLocation();
  const { data: user } = useGetMe();
  const { data: ordersData, isLoading } = useListOrders();

  const getStatusColor = (status: string) => {
    switch(status) {
      case 'delivered': return 'bg-green-100 text-green-800 border-green-200';
      case 'processing': 
      case 'shipped': return 'bg-blue-100 text-blue-800 border-blue-200';
      case 'cancelled': return 'bg-red-100 text-red-800 border-red-200';
      default: return 'bg-orange-100 text-orange-800 border-orange-200';
    }
  };

  return (
    <div className="min-h-screen bg-gray-50 flex flex-col">
      <Navbar />
      
      <main className="flex-1 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 w-full">
        <h1 className="text-3xl font-display font-bold mb-8">
          {user?.role === 'admin' ? "All Orders" : "My Orders"}
        </h1>
        
        {isLoading ? (
          <div className="space-y-4">
            {[1,2,3].map(i => <div key={i} className="h-24 bg-white rounded-2xl animate-pulse border" />)}
          </div>
        ) : !ordersData?.orders || ordersData.orders.length === 0 ? (
          <div className="bg-white p-12 rounded-3xl text-center border">
            <Package className="mx-auto h-12 w-12 text-muted-foreground opacity-30 mb-4" />
            <h2 className="text-xl font-bold mb-2">No orders found</h2>
            <p className="text-muted-foreground">You haven't placed any orders yet.</p>
          </div>
        ) : (
          <div className="space-y-4">
            {ordersData.orders.map(order => (
              <div 
                key={order.id} 
                className="bg-white p-6 rounded-2xl border shadow-sm hover:shadow-md transition-shadow flex flex-col sm:flex-row sm:items-center justify-between gap-4 cursor-pointer"
                onClick={() => setLocation(`/orders/${order.id}`)}
              >
                <div>
                  <div className="flex items-center gap-3 mb-2">
                    <span className="font-bold text-lg">Order #{order.id}</span>
                    <Badge variant="outline" className={`uppercase tracking-wider text-[10px] ${getStatusColor(order.status)}`}>
                      {order.status}
                    </Badge>
                  </div>
                  <div className="text-sm text-muted-foreground flex items-center gap-4">
                    <span>{format(new Date(order.created_at), 'MMM d, yyyy')}</span>
                    <span>•</span>
                    <span className="font-semibold text-foreground">${order.total_amount.toFixed(2)}</span>
                  </div>
                </div>
                
                <Button variant="ghost" className="shrink-0 text-primary hover:text-primary hover:bg-primary/5 self-start sm:self-auto">
                  View Details <ArrowRight className="ml-2 h-4 w-4" />
                </Button>
              </div>
            ))}
          </div>
        )}
      </main>
    </div>
  );
}
