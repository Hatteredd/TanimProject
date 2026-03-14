import { useState } from "react";
import { useListProducts, useGetCategories, useAddToCart, getGetCartQueryKey, useListCategories } from "@workspace/api-client-react";
import { useQueryClient } from "@tanstack/react-query";
import { Navbar } from "@/components/navbar";
import { ProductCard } from "@/components/product-card";
import { Input } from "@/components/ui/input";
import { Search, Filter } from "lucide-react";
import { useToast } from "@/hooks/use-toast";

export default function Products() {
  const [search, setSearch] = useState("");
  const [categoryId, setCategoryId] = useState<number | undefined>();
  
  const { data: productsData, isLoading } = useListProducts({ 
    search: search || undefined, 
    category_id: categoryId 
  });
  
  const { data: categories } = useListCategories();
  
  const addToCartMutation = useAddToCart();
  const queryClient = useQueryClient();
  const { toast } = useToast();

  const handleAddToCart = async (productId: number) => {
    try {
      await addToCartMutation.mutateAsync({ data: { product_id: productId, quantity: 1 } });
      queryClient.invalidateQueries({ queryKey: getGetCartQueryKey() });
      toast({ title: "Added to cart", description: "Item successfully added to your cart." });
    } catch (err: any) {
      toast({ title: "Error", description: "Please login to add items to cart", variant: "destructive" });
    }
  };

  return (
    <div className="min-h-screen bg-gray-50/50 flex flex-col">
      <Navbar />
      
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1 w-full flex flex-col md:flex-row gap-8">
        {/* Sidebar Filters */}
        <div className="w-full md:w-64 shrink-0">
          <div className="bg-white rounded-2xl p-6 border shadow-sm sticky top-24">
            <h2 className="font-display font-bold text-lg flex items-center gap-2 mb-6">
              <Filter className="w-5 h-5" /> Filters
            </h2>
            
            <div className="space-y-6">
              <div>
                <h3 className="text-sm font-semibold text-muted-foreground mb-3 uppercase tracking-wider">Search</h3>
                <div className="relative">
                  <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                  <Input 
                    placeholder="Search produce..." 
                    className="pl-9 h-11 bg-gray-50/50"
                    value={search}
                    onChange={(e) => setSearch(e.target.value)}
                  />
                </div>
              </div>

              <div>
                <h3 className="text-sm font-semibold text-muted-foreground mb-3 uppercase tracking-wider">Categories</h3>
                <div className="space-y-2">
                  <button 
                    className={`w-full text-left px-3 py-2 rounded-lg text-sm transition-colors ${!categoryId ? 'bg-primary/10 text-primary font-semibold' : 'hover:bg-gray-100 text-gray-600'}`}
                    onClick={() => setCategoryId(undefined)}
                  >
                    All Produce
                  </button>
                  {categories?.map(cat => (
                    <button 
                      key={cat.id}
                      className={`w-full text-left px-3 py-2 rounded-lg text-sm transition-colors flex justify-between items-center ${categoryId === cat.id ? 'bg-primary/10 text-primary font-semibold' : 'hover:bg-gray-100 text-gray-600'}`}
                      onClick={() => setCategoryId(cat.id)}
                    >
                      {cat.name}
                      <span className="text-xs opacity-60">{cat.product_count}</span>
                    </button>
                  ))}
                </div>
              </div>
            </div>
          </div>
        </div>

        {/* Product Grid */}
        <div className="flex-1">
          <div className="mb-6 flex items-center justify-between">
            <h1 className="text-2xl font-display font-bold">Marketplace</h1>
            <p className="text-sm text-muted-foreground">{productsData?.total || 0} products found</p>
          </div>

          {isLoading ? (
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
              {[1,2,3,4,5,6].map(i => (
                <div key={i} className="bg-white rounded-2xl h-80 animate-pulse border"></div>
              ))}
            </div>
          ) : productsData?.products?.length === 0 ? (
            <div className="text-center py-24 bg-white rounded-2xl border border-dashed">
              <Leaf className="mx-auto h-12 w-12 text-muted-foreground opacity-20 mb-4" />
              <h3 className="text-lg font-bold text-gray-900">No products found</h3>
              <p className="text-muted-foreground mt-1">Try adjusting your search or filters.</p>
            </div>
          ) : (
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
              {productsData?.products.map(product => (
                <ProductCard 
                  key={product.id} 
                  product={product} 
                  onAddToCart={handleAddToCart}
                  isAdding={addToCartMutation.isPending}
                />
              ))}
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
