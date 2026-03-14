import { useLocation } from "wouter";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { useGetCart, useCreateOrder, getGetCartQueryKey, getListOrdersQueryKey } from "@workspace/api-client-react";
import { useQueryClient } from "@tanstack/react-query";
import { Navbar } from "@/components/navbar";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { RadioGroup, RadioGroupItem } from "@/components/ui/radio-group";
import { Textarea } from "@/components/ui/textarea";
import { useToast } from "@/hooks/use-toast";

const checkoutSchema = z.object({
  shipping_address: z.string().min(10, "Please provide a complete shipping address"),
  payment_method: z.enum(["cod", "online"]),
  notes: z.string().optional()
});

type CheckoutForm = z.infer<typeof checkoutSchema>;

export default function Checkout() {
  const [, setLocation] = useLocation();
  const { toast } = useToast();
  const queryClient = useQueryClient();
  const { data: cart } = useGetCart();
  const createOrderMutation = useCreateOrder();

  const form = useForm<CheckoutForm>({
    resolver: zodResolver(checkoutSchema),
    defaultValues: {
      payment_method: "cod"
    }
  });

  const onSubmit = async (data: CheckoutForm) => {
    try {
      const order = await createOrderMutation.mutateAsync({ data });
      queryClient.invalidateQueries({ queryKey: getGetCartQueryKey() });
      queryClient.invalidateQueries({ queryKey: getListOrdersQueryKey() });
      toast({ title: "Order Confirmed!", description: "Your order has been placed successfully." });
      setLocation(`/orders/${order.id}`);
    } catch (err: any) {
      toast({ title: "Checkout failed", description: err.message, variant: "destructive" });
    }
  };

  if (!cart || cart.items.length === 0) {
    setLocation("/cart");
    return null;
  }

  return (
    <div className="min-h-screen bg-gray-50 flex flex-col">
      <Navbar />
      
      <main className="flex-1 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 w-full">
        <h1 className="text-3xl font-display font-bold mb-8">Checkout</h1>
        
        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div className="md:col-span-2">
            <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-8">
              
              <div className="bg-white p-6 rounded-2xl border shadow-sm">
                <h2 className="text-xl font-bold mb-6">Shipping Details</h2>
                <div className="space-y-4">
                  <div className="space-y-2">
                    <Label htmlFor="shipping_address">Delivery Address</Label>
                    <Textarea 
                      id="shipping_address" 
                      placeholder="Street address, city, state, zip" 
                      className="resize-none h-24"
                      {...form.register("shipping_address")} 
                    />
                    {form.formState.errors.shipping_address && (
                      <p className="text-sm text-destructive">{form.formState.errors.shipping_address.message}</p>
                    )}
                  </div>
                  <div className="space-y-2">
                    <Label htmlFor="notes">Delivery Notes (Optional)</Label>
                    <Input id="notes" placeholder="e.g. Leave at front door" {...form.register("notes")} />
                  </div>
                </div>
              </div>

              <div className="bg-white p-6 rounded-2xl border shadow-sm">
                <h2 className="text-xl font-bold mb-6">Payment Method</h2>
                <RadioGroup 
                  defaultValue="cod" 
                  onValueChange={(v) => form.setValue("payment_method", v as any)}
                  className="space-y-3"
                >
                  <div className="flex items-center space-x-3 border p-4 rounded-xl hover:bg-gray-50 cursor-pointer">
                    <RadioGroupItem value="cod" id="cod" />
                    <Label htmlFor="cod" className="flex-1 font-semibold cursor-pointer">Cash on Delivery (COD)</Label>
                  </div>
                  <div className="flex items-center space-x-3 border p-4 rounded-xl hover:bg-gray-50 cursor-pointer">
                    <RadioGroupItem value="online" id="online" />
                    <Label htmlFor="online" className="flex-1 font-semibold cursor-pointer">Online Payment (Card/Wallet)</Label>
                  </div>
                </RadioGroup>
              </div>

              <Button 
                type="submit" 
                size="lg" 
                className="w-full h-14 text-lg rounded-xl shadow-lg shadow-primary/20"
                disabled={createOrderMutation.isPending}
              >
                {createOrderMutation.isPending ? "Processing..." : `Place Order - $${cart.total.toFixed(2)}`}
              </Button>
            </form>
          </div>

          <div className="md:col-span-1">
            <div className="bg-white p-6 rounded-2xl border shadow-sm sticky top-24">
              <h3 className="font-bold mb-4">Order Summary</h3>
              <div className="space-y-3 mb-6">
                {cart.items.map(item => (
                  <div key={item.id} className="flex justify-between text-sm">
                    <span className="text-muted-foreground truncate pr-4">{item.quantity}x {item.product_name}</span>
                    <span className="font-medium">${item.subtotal.toFixed(2)}</span>
                  </div>
                ))}
              </div>
              <div className="border-t pt-4 flex justify-between items-center">
                <span className="font-bold">Total</span>
                <span className="font-bold text-xl text-primary">${cart.total.toFixed(2)}</span>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  );
}
