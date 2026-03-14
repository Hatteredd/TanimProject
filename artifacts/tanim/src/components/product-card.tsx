import { Link } from "wouter";
import { Product } from "@workspace/api-client-react";
import { Button } from "@/components/ui/button";
import { ShoppingCart, Star } from "lucide-react";
import { Badge } from "@/components/ui/badge";

interface ProductCardProps {
  product: Product;
  onAddToCart?: (productId: number) => void;
  isAdding?: boolean;
}

export function ProductCard({ product, onAddToCart, isAdding }: ProductCardProps) {
  const isOutOfStock = product.stock_quantity <= 0;

  return (
    <div className="group flex flex-col bg-card rounded-2xl border border-border/60 overflow-hidden shadow-sm hover:shadow-xl hover:border-primary/30 transition-all duration-300">
      <Link href={`/products/${product.id}`} className="relative aspect-square overflow-hidden block">
        {isOutOfStock && (
          <div className="absolute inset-0 bg-white/60 backdrop-blur-[2px] z-10 flex items-center justify-center">
            <Badge variant="secondary" className="text-sm px-3 py-1 font-semibold bg-white/90 text-foreground shadow-lg border-muted">
              Out of Stock
            </Badge>
          </div>
        )}
        <div className="absolute top-3 left-3 z-10">
          <Badge className="bg-white/90 text-primary hover:bg-white font-semibold shadow-sm backdrop-blur-md">
            {product.category_name || "Farm Fresh"}
          </Badge>
        </div>
        {/* Farm produce photo placeholder */}
        <img
          src={product.image_url || `https://images.unsplash.com/photo-1595858643806-0d1b31fb5e74?w=800&q=80&sig=${product.id}`}
          alt={product.name}
          className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
        />
      </Link>
      
      <div className="p-5 flex flex-col flex-grow">
        <div className="flex justify-between items-start mb-2">
          <Link href={`/products/${product.id}`}>
            <h3 className="font-display font-bold text-lg leading-tight line-clamp-2 hover:text-primary transition-colors">
              {product.name}
            </h3>
          </Link>
          <div className="flex items-center gap-1 bg-secondary/10 text-secondary-foreground px-2 py-1 rounded-md text-xs font-bold shrink-0">
            <Star className="h-3 w-3 fill-secondary text-secondary" />
            {product.average_rating ? product.average_rating.toFixed(1) : "New"}
          </div>
        </div>

        <p className="text-sm text-muted-foreground mb-4">By {product.farmer_name}</p>

        <div className="mt-auto flex items-center justify-between pt-4 border-t border-border/50">
          <div>
            <span className="text-xl font-bold text-foreground">${product.price.toFixed(2)}</span>
            <span className="text-sm text-muted-foreground"> / {product.unit}</span>
          </div>
          
          {onAddToCart && (
            <Button
              size="icon"
              className="rounded-full shadow-md shadow-primary/20 hover:scale-105 transition-transform"
              disabled={isOutOfStock || isAdding}
              onClick={() => onAddToCart(product.id)}
            >
              <ShoppingCart className="h-4 w-4" />
            </Button>
          )}
        </div>
      </div>
    </div>
  );
}
