import { useGetDashboardStats, useGetSalesByYear, useGetSalesByProduct, useListOrders } from "@workspace/api-client-react";
import { Navbar } from "@/components/navbar";
import { Card } from "@/components/ui/card";
import { DollarSign, Users, Package, ShoppingBag } from "lucide-react";
import { BarChart, Bar, XAxis, YAxis, Tooltip, ResponsiveContainer, PieChart, Pie, Cell } from "recharts";

const COLORS = ['#16a34a', '#eab308', '#0ea5e9', '#f97316', '#a855f7'];

export default function AdminDashboard() {
  const { data: stats } = useGetDashboardStats();
  const { data: yearlySales } = useGetSalesByYear({ year: new Date().getFullYear() });
  const { data: productSales } = useGetSalesByProduct({ year: new Date().getFullYear() });

  const statCards = [
    { title: "Total Revenue", value: `$${stats?.total_revenue?.toFixed(2) || '0.00'}`, icon: DollarSign },
    { title: "Total Orders", value: stats?.total_orders || 0, icon: ShoppingBag },
    { title: "Active Users", value: stats?.total_users || 0, icon: Users },
    { title: "Products Listed", value: stats?.total_products || 0, icon: Package },
  ];

  return (
    <div className="min-h-screen bg-gray-50 flex flex-col">
      <Navbar />
      
      <main className="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
        <h1 className="text-3xl font-display font-bold mb-8 text-foreground">Admin Dashboard</h1>

        {/* Stats Grid */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
          {statCards.map((s, i) => (
            <Card key={i} className="p-6 border-border/50 shadow-sm rounded-2xl hover:shadow-md transition-shadow">
              <div className="flex justify-between items-start">
                <div>
                  <p className="text-sm font-medium text-muted-foreground mb-1">{s.title}</p>
                  <h3 className="text-2xl font-bold text-foreground">{s.value}</h3>
                </div>
                <div className="bg-primary/10 p-3 rounded-xl">
                  <s.icon className="h-5 w-5 text-primary" />
                </div>
              </div>
            </Card>
          ))}
        </div>

        {/* Charts */}
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <Card className="lg:col-span-2 p-6 rounded-3xl border-border/50 shadow-sm">
            <h3 className="text-lg font-bold mb-6 font-display">Revenue Overview ({new Date().getFullYear()})</h3>
            <div className="h-80 w-full">
              <ResponsiveContainer width="100%" height="100%">
                <BarChart data={yearlySales?.data || []}>
                  <XAxis dataKey="month" axisLine={false} tickLine={false} fontSize={12} tickMargin={10} />
                  <YAxis axisLine={false} tickLine={false} fontSize={12} tickFormatter={(v) => `$${v}`} />
                  <Tooltip 
                    cursor={{ fill: 'rgba(22, 163, 74, 0.05)' }}
                    contentStyle={{ borderRadius: '12px', border: 'none', boxShadow: '0 4px 6px -1px rgb(0 0 0 / 0.1)' }}
                  />
                  <Bar dataKey="revenue" fill="hsl(var(--primary))" radius={[6, 6, 0, 0]} />
                </BarChart>
              </ResponsiveContainer>
            </div>
          </Card>

          <Card className="p-6 rounded-3xl border-border/50 shadow-sm">
            <h3 className="text-lg font-bold mb-6 font-display">Sales by Product</h3>
            <div className="h-80 w-full flex items-center justify-center">
              {productSales?.products && productSales.products.length > 0 ? (
                <ResponsiveContainer width="100%" height="100%">
                  <PieChart>
                    <Pie
                      data={productSales.products}
                      cx="50%"
                      cy="50%"
                      innerRadius={60}
                      outerRadius={80}
                      paddingAngle={5}
                      dataKey="total_revenue"
                    >
                      {productSales.products.map((_, index) => (
                        <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
                      ))}
                    </Pie>
                    <Tooltip contentStyle={{ borderRadius: '12px' }} formatter={(val: number) => `$${val.toFixed(2)}`} />
                  </PieChart>
                </ResponsiveContainer>
              ) : (
                <p className="text-muted-foreground text-sm">Not enough data</p>
              )}
            </div>
            <div className="mt-4 flex flex-wrap gap-2 justify-center">
              {productSales?.products.slice(0, 4).map((p, i) => (
                <div key={p.product_id} className="flex items-center text-xs">
                  <div className="w-3 h-3 rounded-full mr-1" style={{ backgroundColor: COLORS[i % COLORS.length] }} />
                  {p.product_name}
                </div>
              ))}
            </div>
          </Card>
        </div>
      </main>
    </div>
  );
}
