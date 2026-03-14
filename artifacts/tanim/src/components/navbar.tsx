import { Link, useLocation } from "wouter";
import { useGetMe, useGetCart } from "@workspace/api-client-react";
import { Button } from "@/components/ui/button";
import { ShoppingCart, Leaf, User as UserIcon, Menu } from "lucide-react";
import { Badge } from "@/components/ui/badge";
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger, DropdownMenuSeparator } from "@/components/ui/dropdown-menu";
import { useLogout } from "@workspace/api-client-react";
import { useQueryClient } from "@tanstack/react-query";
import { getGetMeQueryKey } from "@workspace/api-client-react";

export function Navbar() {
  const [location, setLocation] = useLocation();
  const { data: user } = useGetMe({ query: { retry: false } });
  const { data: cart } = useGetCart({ query: { retry: false, enabled: !!user && user.role === 'buyer' } });
  const queryClient = useQueryClient();
  const logoutMutation = useLogout();

  const handleLogout = async () => {
    await logoutMutation.mutateAsync();
    queryClient.setQueryData(getGetMeQueryKey(), null);
    setLocation("/login");
  };

  return (
    <nav className="sticky top-0 z-50 w-full border-b bg-white/80 backdrop-blur-lg shadow-sm">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex justify-between items-center h-16">
          <div className="flex items-center gap-2 cursor-pointer" onClick={() => setLocation("/")}>
            <div className="bg-primary/10 p-2 rounded-xl">
              <Leaf className="h-6 w-6 text-primary" />
            </div>
            <span className="font-display font-bold text-2xl text-primary tracking-tight">Tanim</span>
          </div>

          <div className="hidden md:flex space-x-8 items-center">
            <Link href="/products" className="text-sm font-semibold text-muted-foreground hover:text-primary transition-colors">
              Marketplace
            </Link>
            {user?.role === 'farmer' && (
              <Link href="/farmer/dashboard" className="text-sm font-semibold text-muted-foreground hover:text-primary transition-colors">
                Farmer Portal
              </Link>
            )}
            {user?.role === 'admin' && (
              <Link href="/admin/dashboard" className="text-sm font-semibold text-muted-foreground hover:text-primary transition-colors">
                Admin Dashboard
              </Link>
            )}
          </div>

          <div className="flex items-center gap-4">
            {user?.role === 'buyer' && (
              <Link href="/cart">
                <Button variant="ghost" size="icon" className="relative hover:bg-primary/5">
                  <ShoppingCart className="h-5 w-5 text-foreground" />
                  {cart && cart.item_count > 0 && (
                    <Badge className="absolute -top-1 -right-1 h-5 w-5 flex items-center justify-center p-0 text-xs bg-primary text-primary-foreground border-2 border-white rounded-full">
                      {cart.item_count}
                    </Badge>
                  )}
                </Button>
              </Link>
            )}

            {user ? (
              <DropdownMenu>
                <DropdownMenuTrigger asChild>
                  <Button variant="ghost" className="gap-2 pl-2 pr-4 bg-muted/50 hover:bg-muted rounded-full border border-border/50">
                    <div className="h-7 w-7 rounded-full bg-primary/20 flex items-center justify-center">
                      <UserIcon className="h-4 w-4 text-primary" />
                    </div>
                    <span className="text-sm font-medium hidden sm:block">{user.name}</span>
                  </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end" className="w-56 rounded-xl shadow-xl p-2">
                  <div className="p-2 border-b mb-2">
                    <p className="font-semibold text-sm truncate">{user.name}</p>
                    <p className="text-xs text-muted-foreground truncate">{user.email}</p>
                  </div>
                  <DropdownMenuItem onClick={() => setLocation("/orders")} className="rounded-lg cursor-pointer">
                    My Orders
                  </DropdownMenuItem>
                  <DropdownMenuSeparator />
                  <DropdownMenuItem onClick={handleLogout} className="text-destructive focus:text-destructive rounded-lg cursor-pointer">
                    Logout
                  </DropdownMenuItem>
                </DropdownMenuContent>
              </DropdownMenu>
            ) : (
              <div className="flex items-center gap-2">
                <Button variant="ghost" onClick={() => setLocation("/login")} className="hidden sm:inline-flex font-semibold">
                  Sign In
                </Button>
                <Button onClick={() => setLocation("/register")} className="rounded-xl font-semibold shadow-lg shadow-primary/20">
                  Get Started
                </Button>
              </div>
            )}
          </div>
        </div>
      </div>
    </nav>
  );
}
