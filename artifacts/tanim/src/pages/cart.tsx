import { useLocation } from "wouter";
import { useGetCart, useUpdateCartItem, useRemoveFromCart, getGetCartQueryKey } from "@workspace/api-client-react";
import { useQueryClient } from "@tanstack/react-query";
import { Navbar } from "@/components/navbar";
import { Button } from "@/components/ui/button";
import { Trash2, Plus, Minus, ArrowRight, ShoppingBag } from "lucide-react";

export default function Cart() {
  const [, setLocation] = useLocation();
  const queryClient = useQueryClient();
  const { data: cart, isLoading } = useGetCart();
  const updateMutation = useUpdateCartItem();
  const removeMutation = useRemoveFromCart();

  const handleUpdateQuantity = async (productId: number, newQty: number) => {
    if (newQty < 1) return;
    await updateMutation.mutateAsync({ productId, data: { quantity: newQty } });
    queryClient.invalidateQueries({ queryKey: getGetCartQueryKey() });
  };

  const handleRemove = async (productId: number) => {
    await removeMutation.mutateAsync({ productId });
    queryClient.invalidateQueries({ queryKey: getGetCartQueryKey() });
  };

  if (isLoading) return <div className="min-h-screen bg-gray-50"><Navbar /></div>;

  return (
    <div className="min-h-screen bg-gray-50 flex flex-col">
      <Navbar />
      
      <main className="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 w-full">
        <h1 className="text-3xl font-display font-bold mb-8">Your Cart</h1>
        
        {!cart || cart.items.length === 0 ? (
          <div className="bg-white rounded-3xl p-16 text-center border shadow-sm max-w-2xl mx-auto mt-12">
            <div className="bg-primary/5 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6">
              <ShoppingBag className="h-10 w-10 text-primary" />
            </div>
            <h2 className="text-2xl font-bold mb-2">Your cart is empty</h2>
            <p className="text-muted-foreground mb-8">Looks like you haven't added any fresh produce yet.</p>
            <Button size="lg" className="rounded-xl px-8 h-12" onClick={() => setLocation("/products")}>
              Browse Marketplace
            </Button>
          </div>
        ) : (
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <div className="lg:col-span-2 space-y-4">
              {cart.items.map(item => (
                <div key={item.id} className="bg-white p-4 sm:p-6 rounded-2xl border shadow-sm flex flex-col sm:flex-row items-center gap-6">
                  <div className="h-24 w-24 bg-gray-100 rounded-xl overflow-hidden shrink-0">
                    <img 
                      src={item.product_image || `https://images.unsplash.com/photo-1595858643806-0d1b31fb5e74?w=200&q=80&sig=${item.product_id}`} 
                      alt={item.product_name} 
                      className="w-full h-full object-cover"
                    />
                  </div>
                  
                  <div className="flex-1 text-center sm:text-left">
                    <h3 className="font-bold text-lg leading-tight">{item.product_name}</h3>
                    <p className="text-sm text-muted-foreground mb-2">From {item.farmer_name}</p>
                    <div className="font-semibold text-primary">${item.price.toFixed(2)} <span className="text-muted-foreground font-normal">/ {item.unit}</span></div>
                  </div>

                  <div className="flex flex-col items-center gap-4">
                    <div className="flex items-center gap-3 bg-gray-50 border rounded-lg p-1">
                      <Button 
                        variant="ghost" 
                        size="icon" 
                        className="h-8 w-8 rounded-md" 
                        onClick={() => handleUpdateQuantity(item.product_id, item.quantity - 1)}
                        disabled={item.quantity <= 1 || updateMutation.isPending}
                      >
                        <Minus className="h-4 w-4" />
                      </Button>
                      <span className="w-8 text-center font-semibold">{item.quantity}</span>
                      <Button 
                        variant="ghost" 
                        size="icon" 
                        className="h-8 w-8 rounded-md"
                        onClick={() => handleUpdateQuantity(item.product_id, item.quantity + 1)}
                        disabled={item.quantity >= item.stock_quantity || updateMutation.isPending}
                      >
                        <Plus className="h-4 w-4" />
                      </Button>
                    </div>
                    <Button 
                      variant="ghost" 
                      size="sm" 
                      className="text-destructive hover:bg-destructive/10 hover:text-destructive"
                      onClick={() => handleRemove(item.product_id)}
                      disabled={removeMutation.isPending}
                    >
                      <Trash2 className="h-4 w-4 mr-2" /> Remove
                    </Button>
                  </div>
                  
                  <div className="w-full sm:w-24 text-right">
                    <p className="font-bold text-lg">${item.subtotal.toFixed(2)}</p>
                  </div>
                </div>
              ))}
            </div>

            <div className="lg:col-span-1">
              <div className="bg-white rounded-3xl p-6 border shadow-sm sticky top-24">
                <h3 className="font-display font-bold text-xl mb-6">Order Summary</h3>
                
                <div className="space-y-3 text-sm mb-6">
                  <div className="flex justify-between">
                    <span className="text-muted-foreground">Subtotal ({cart.item_count} items)</span>
                    <span className="font-medium">${cart.total.toFixed(2)}</span>
                  </div>
                  <div className="flex justify-between">
                    <span className="text-muted-foreground">Shipping</span>
                    <span className="font-medium">Calculated at checkout</span>
                  </div>
                </div>
                
                <div className="border-t pt-4 mb-8 flex justify-between items-center">
                  <span className="font-bold text-lg">Total</span>
                  <span className="font-bold text-2xl text-primary">${cart.total.toFixed(2)}</span>
                </div>

                <Button 
                  size="lg" 
                  className="w-full h-14 rounded-xl text-lg font-semibold shadow-lg shadow-primary/20"
                  onClick={() => setLocation("/checkout")}
                >
                  Proceed to Checkout <ArrowRight className="ml-2 h-5 w-5" />
                </Button>
              </div>
            </div>
          </div>
        )}
      </main>
    </div>
  );
}
