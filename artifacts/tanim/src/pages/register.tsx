import { useState } from "react";
import { Link, useLocation } from "wouter";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { useRegister, getGetMeQueryKey } from "@workspace/api-client-react";
import { useQueryClient } from "@tanstack/react-query";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Leaf } from "lucide-react";
import { useToast } from "@/hooks/use-toast";
import { RadioGroup, RadioGroupItem } from "@/components/ui/radio-group";

const registerSchema = z.object({
  name: z.string().min(2, "Name is required"),
  email: z.string().email("Valid email required"),
  password: z.string().min(6, "Password must be at least 6 characters"),
  role: z.enum(["buyer", "farmer"]),
  phone: z.string().optional(),
  address: z.string().optional(),
  farm_name: z.string().optional(),
  farm_location: z.string().optional(),
});

type RegisterForm = z.infer<typeof registerSchema>;

export default function Register() {
  const [, setLocation] = useLocation();
  const { toast } = useToast();
  const queryClient = useQueryClient();
  const [role, setRole] = useState<"buyer" | "farmer">("buyer");
  
  const form = useForm<RegisterForm>({
    resolver: zodResolver(registerSchema),
    defaultValues: { name: "", email: "", password: "", role: "buyer" },
  });

  const registerMutation = useRegister();

  const onSubmit = async (data: RegisterForm) => {
    try {
      await registerMutation.mutateAsync({ data });
      queryClient.invalidateQueries({ queryKey: getGetMeQueryKey() });
      toast({ title: "Account created!", description: "Welcome to Tanim." });
      setLocation(data.role === 'farmer' ? "/farmer/dashboard" : "/products");
    } catch (error: any) {
      toast({
        title: "Registration failed",
        description: error.message || "Could not create account",
        variant: "destructive",
      });
    }
  };

  return (
    <div className="min-h-screen bg-background flex">
      <div className="flex-1 flex flex-col justify-center px-4 sm:px-6 lg:flex-none lg:px-20 xl:px-24 py-12">
        <div className="mx-auto w-full max-w-md">
          <div className="flex items-center gap-2 mb-8 cursor-pointer" onClick={() => setLocation("/")}>
            <div className="bg-primary/10 p-2 rounded-xl">
              <Leaf className="h-6 w-6 text-primary" />
            </div>
            <span className="font-display font-bold text-2xl text-primary">Tanim</span>
          </div>

          <h2 className="text-3xl font-display font-bold text-foreground">Create an account</h2>
          <p className="mt-2 text-sm text-muted-foreground">
            Already have an account?{' '}
            <Link href="/login" className="font-semibold text-primary hover:underline">
              Sign in
            </Link>
          </p>

          <div className="mt-8">
            <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-5">
              
              <div className="space-y-3 mb-6">
                <Label>I want to join as a:</Label>
                <RadioGroup 
                  defaultValue="buyer" 
                  onValueChange={(val) => {
                    setRole(val as any);
                    form.setValue("role", val as any);
                  }}
                  className="grid grid-cols-2 gap-4"
                >
                  <div>
                    <RadioGroupItem value="buyer" id="buyer" className="peer sr-only" />
                    <Label
                      htmlFor="buyer"
                      className="flex flex-col items-center justify-between rounded-xl border-2 border-muted bg-transparent p-4 hover:bg-muted/50 peer-data-[state=checked]:border-primary peer-data-[state=checked]:bg-primary/5 cursor-pointer"
                    >
                      <span className="font-bold text-lg mb-1">Buyer</span>
                      <span className="text-xs text-muted-foreground text-center">I want to buy fresh produce</span>
                    </Label>
                  </div>
                  <div>
                    <RadioGroupItem value="farmer" id="farmer" className="peer sr-only" />
                    <Label
                      htmlFor="farmer"
                      className="flex flex-col items-center justify-between rounded-xl border-2 border-muted bg-transparent p-4 hover:bg-muted/50 peer-data-[state=checked]:border-primary peer-data-[state=checked]:bg-primary/5 cursor-pointer"
                    >
                      <span className="font-bold text-lg mb-1">Farmer</span>
                      <span className="text-xs text-muted-foreground text-center">I want to sell my crops</span>
                    </Label>
                  </div>
                </RadioGroup>
              </div>

              <div className="space-y-2">
                <Label htmlFor="name">Full Name</Label>
                <Input id="name" className="h-11 rounded-xl" {...form.register("name")} />
              </div>

              <div className="space-y-2">
                <Label htmlFor="email">Email</Label>
                <Input id="email" type="email" className="h-11 rounded-xl" {...form.register("email")} />
              </div>

              <div className="space-y-2">
                <Label htmlFor="password">Password</Label>
                <Input id="password" type="password" className="h-11 rounded-xl" {...form.register("password")} />
              </div>

              {role === 'farmer' && (
                <div className="space-y-5 pt-4 border-t">
                  <h3 className="font-semibold text-lg">Farm Details</h3>
                  <div className="space-y-2">
                    <Label htmlFor="farm_name">Farm Name</Label>
                    <Input id="farm_name" className="h-11 rounded-xl" {...form.register("farm_name")} />
                  </div>
                  <div className="space-y-2">
                    <Label htmlFor="farm_location">Farm Location</Label>
                    <Input id="farm_location" className="h-11 rounded-xl" {...form.register("farm_location")} />
                  </div>
                </div>
              )}

              <Button 
                type="submit" 
                className="w-full h-12 rounded-xl text-base font-semibold shadow-lg shadow-primary/20 mt-6"
                disabled={registerMutation.isPending}
              >
                {registerMutation.isPending ? "Creating account..." : "Create Account"}
              </Button>
            </form>
          </div>
        </div>
      </div>
      
      <div className="hidden lg:block relative w-0 flex-1">
        {/* farm wide shot */}
        <img
          className="absolute inset-0 h-full w-full object-cover"
          src="https://images.unsplash.com/photo-1500382017468-9049fed747ef?w=1600&q=80"
          alt="Agriculture fields"
        />
        <div className="absolute inset-0 bg-primary/20 backdrop-blur-[2px] mix-blend-multiply" />
      </div>
    </div>
  );
}
