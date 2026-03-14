import { Link } from "wouter";
import { Button } from "@/components/ui/button";
import { ArrowRight, ShieldCheck, Tractor, Truck } from "lucide-react";
import { Navbar } from "@/components/navbar";

export default function Home() {
  return (
    <div className="min-h-screen bg-background flex flex-col">
      <Navbar />
      
      <main className="flex-1">
        {/* Hero Section */}
        <section className="relative pt-24 pb-32 lg:pt-36 lg:pb-40 overflow-hidden">
          <div className="absolute inset-0 z-0">
            <img 
              src={`${import.meta.env.BASE_URL}images/hero-bg.png`} 
              alt="Farm hero" 
              className="w-full h-full object-cover"
            />
            <div className="absolute inset-0 bg-gradient-to-r from-black/80 via-black/50 to-transparent" />
          </div>
          
          <div className="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="max-w-2xl">
              <h1 className="text-4xl md:text-5xl lg:text-6xl font-display font-bold text-white leading-tight mb-6">
                Fresh from the farm,<br />
                <span className="text-primary">direct to your door.</span>
              </h1>
              <p className="text-lg md:text-xl text-gray-200 mb-8 max-w-lg">
                Tanim connects local farmers directly with buyers. Cut out the middleman, ensure fair prices, and get the freshest produce available.
              </p>
              <div className="flex flex-col sm:flex-row gap-4">
                <Link href="/products">
                  <Button size="lg" className="rounded-xl text-base px-8 h-14 shadow-xl shadow-primary/30 font-semibold w-full sm:w-auto">
                    Shop Produce <ArrowRight className="ml-2 h-5 w-5" />
                  </Button>
                </Link>
                <Link href="/register">
                  <Button variant="outline" size="lg" className="rounded-xl text-base px-8 h-14 bg-white/10 text-white border-white/20 hover:bg-white/20 backdrop-blur-md w-full sm:w-auto">
                    Join as a Farmer
                  </Button>
                </Link>
              </div>
            </div>
          </div>
        </section>

        {/* Features Section */}
        <section className="py-24 bg-white">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="text-center mb-16">
              <h2 className="text-3xl font-display font-bold text-foreground">Why choose Tanim?</h2>
              <p className="mt-4 text-lg text-muted-foreground">We are rebuilding the agricultural supply chain.</p>
            </div>
            
            <div className="grid grid-cols-1 md:grid-cols-3 gap-10">
              {[
                {
                  title: "Direct Sourcing",
                  desc: "Buyers purchase directly from farmers, ensuring maximum freshness and traceability.",
                  icon: Tractor,
                },
                {
                  title: "Fair Pricing",
                  desc: "Without middlemen taking a cut, farmers earn more and buyers pay less.",
                  icon: ShieldCheck,
                },
                {
                  title: "Efficient Logistics",
                  desc: "Streamlined order management and tracking from farm gate to your front door.",
                  icon: Truck,
                }
              ].map((feature, i) => (
                <div key={i} className="bg-background rounded-2xl p-8 border border-border/50 hover:shadow-xl transition-shadow duration-300 group">
                  <div className="bg-primary/10 w-16 h-16 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                    <feature.icon className="h-8 w-8 text-primary" />
                  </div>
                  <h3 className="text-xl font-bold text-foreground mb-3">{feature.title}</h3>
                  <p className="text-muted-foreground leading-relaxed">{feature.desc}</p>
                </div>
              ))}
            </div>
          </div>
        </section>
      </main>
    </div>
  );
}
