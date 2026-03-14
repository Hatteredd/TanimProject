import { useParams, useLocation } from "wouter";
import { useGetProduct, useAddToCart, getGetCartQueryKey, useListReviews } from "@workspace/api-client-react";
import { useQueryClient } from "@tanstack/react-query";
import { Navbar } from "@/components/navbar";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { ShoppingCart, Star, MapPin, Tractor, ArrowLeft } from "lucide-react";
import { useToast } from "@/hooks/use-toast";
import { format } from "date-fns";

export default function ProductDetail() {
  const params = useParams();
  const id = parseInt(params.id || "0");
  const [, setLocation] = useLocation();
  const { toast } = useToast();
  const queryClient = useQueryClient();

  const { data: product, isLoading } = useGetProduct(id, { query: { enabled: id > 0 } });
  const { data: reviewsData } = useListReviews({ product_id: id }, { query: { enabled: id > 0 } });
  const addToCartMutation = useAddToCart();

  const handleAddToCart = async () => {
    if (!product) return;
    try {
      await addToCartMutation.mutateAsync({ data: { product_id: product.id, quantity: 1 } });
      queryClient.invalidateQueries({ queryKey: getGetCartQueryKey() });
      toast({ title: "Added to cart", description: `${product.name} added to your cart.` });
    } catch (err: any) {
      toast({ title: "Error", description: "Please login as a buyer to add items to cart", variant: "destructive" });
    }
  };

  if (isLoading) {
    return <div className="min-h-screen bg-background"><Navbar /><div className="p-20 text-center">Loading...</div></div>;
  }

  if (!product) {
    return <div className="min-h-screen bg-background"><Navbar /><div className="p-20 text-center">Product not found</div></div>;
  }

  const isOutOfStock = product.stock_quantity <= 0;

  return (
    <div className="min-h-screen bg-gray-50 flex flex-col">
      <Navbar />
      
      <main className="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
        <Button variant="ghost" className="mb-6 -ml-4 text-muted-foreground" onClick={() => setLocation("/products")}>
          <ArrowLeft className="mr-2 h-4 w-4" /> Back to Marketplace
        </Button>

        <div className="bg-white rounded-3xl shadow-sm border overflow-hidden">
          <div className="grid grid-cols-1 md:grid-cols-2">
            {/* Product Image */}
            <div className="relative h-96 md:h-full min-h-[400px] bg-gray-100">
              {/* vegetable closeup */}
              <img 
                src={product.image_url || `https://images.unsplash.com/photo-1595858643806-0d1b31fb5e74?w=1000&q=80&sig=${product.id}`}
                alt={product.name}
                className="absolute inset-0 w-full h-full object-cover"
              />
              {isOutOfStock && (
                <div className="absolute inset-0 bg-white/50 backdrop-blur-sm flex items-center justify-center">
                  <Badge variant="secondary" className="text-lg px-6 py-2 shadow-xl">Out of Stock</Badge>
                </div>
              )}
            </div>

            {/* Product Info */}
            <div className="p-8 md:p-12 flex flex-col">
              <Badge className="w-fit mb-4">{product.category_name || "Produce"}</Badge>
              <h1 className="text-4xl font-display font-bold text-foreground mb-4">{product.name}</h1>
              
              <div className="flex items-center gap-4 mb-6">
                <span className="text-3xl font-bold text-primary">${product.price.toFixed(2)}</span>
                <span className="text-lg text-muted-foreground">per {product.unit}</span>
              </div>

              <div className="flex items-center gap-2 mb-8 bg-secondary/10 w-fit px-3 py-1.5 rounded-lg">
                <Star className="h-5 w-5 fill-secondary text-secondary" />
                <span className="font-bold text-secondary-foreground">{product.average_rating?.toFixed(1) || "New"}</span>
                <span className="text-sm text-muted-foreground ml-1">({product.review_count} reviews)</span>
              </div>

              <p className="text-gray-600 text-lg mb-8 leading-relaxed">
                {product.description || "Freshly harvested produce from local farms."}
              </p>

              <div className="bg-gray-50 rounded-2xl p-6 mb-8 border border-gray-100">
                <h3 className="font-bold mb-4 flex items-center gap-2"><Tractor className="h-5 w-5 text-primary" /> Farmer Details</h3>
                <p className="font-semibold text-lg">{product.farmer_name}</p>
                {product.farm_name && <p className="text-muted-foreground flex items-center gap-1 mt-1"><MapPin className="h-4 w-4" /> {product.farm_name}</p>}
                <p className="text-sm text-muted-foreground mt-4 pt-4 border-t border-gray-200">
                  <span className="font-semibold text-foreground">{product.stock_quantity}</span> {product.unit}s currently available in stock.
                </p>
              </div>

              <div className="mt-auto pt-6">
                <Button 
                  size="lg" 
                  className="w-full h-14 text-lg rounded-xl shadow-lg shadow-primary/20"
                  disabled={isOutOfStock || addToCartMutation.isPending}
                  onClick={handleAddToCart}
                >
                  <ShoppingCart className="mr-2 h-5 w-5" /> 
                  {addToCartMutation.isPending ? "Adding..." : isOutOfStock ? "Out of Stock" : "Add to Cart"}
                </Button>
              </div>
            </div>
          </div>
        </div>

        {/* Reviews Section */}
        <div className="mt-16 mb-24 max-w-4xl">
          <h2 className="text-2xl font-display font-bold mb-8">Customer Reviews</h2>
          
          {reviewsData?.reviews && reviewsData.reviews.length > 0 ? (
            <div className="space-y-6">
              {reviewsData.reviews.map(review => (
                <div key={review.id} className="bg-white p-6 rounded-2xl shadow-sm border">
                  <div className="flex items-center justify-between mb-4">
                    <div>
                      <p className="font-bold">{review.reviewer_name}</p>
                      <p className="text-xs text-muted-foreground">{format(new Date(review.created_at), 'MMMM d, yyyy')}</p>
                    </div>
                    <div className="flex">
                      {[...Array(5)].map((_, i) => (
                        <Star key={i} className={`h-4 w-4 ${i < review.rating ? 'fill-secondary text-secondary' : 'text-gray-200'}`} />
                      ))}
                    </div>
                  </div>
                  {review.comment && <p className="text-gray-700">{review.comment}</p>}
                </div>
              ))}
            </div>
          ) : (
            <div className="bg-white p-12 rounded-2xl border border-dashed text-center text-muted-foreground">
              No reviews yet. Be the first to review this product after purchasing!
            </div>
          )}
        </div>
      </main>
    </div>
  );
}
