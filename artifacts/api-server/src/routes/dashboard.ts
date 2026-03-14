import { Router } from "express";
import { db, ordersTable, orderItemsTable, productsTable, usersTable, notificationsTable } from "@workspace/db";
import { eq, and, sql, gte, lte } from "drizzle-orm";
import { requireAuth, requireRole, type AuthRequest } from "../lib/auth.js";

const router = Router();

router.get("/stats", requireAuth, requireRole("admin"), async (_req, res) => {
  try {
    const allOrders = await db.select().from(ordersTable);
    const allProducts = await db.select().from(productsTable).where(eq(productsTable.is_active, true));
    const allUsers = await db.select().from(usersTable);

    const totalRevenue = allOrders
      .filter(o => o.status !== "cancelled")
      .reduce((sum, o) => sum + parseFloat(o.total_amount), 0);

    const now = new Date();
    const startOfMonth = new Date(now.getFullYear(), now.getMonth(), 1);

    const thisMonthOrders = allOrders.filter(o =>
      new Date(o.created_at) >= startOfMonth && o.status !== "cancelled"
    );
    const revenueThisMonth = thisMonthOrders.reduce((sum, o) => sum + parseFloat(o.total_amount), 0);

    const lowStockCount = allProducts.filter(p => p.stock_quantity <= p.low_stock_threshold).length;

    res.json({
      total_revenue: totalRevenue,
      total_orders: allOrders.filter(o => o.status !== "cancelled").length,
      total_products: allProducts.length,
      total_users: allUsers.length,
      total_farmers: allUsers.filter(u => u.role === "farmer").length,
      total_buyers: allUsers.filter(u => u.role === "buyer").length,
      pending_orders: allOrders.filter(o => o.status === "pending").length,
      low_stock_count: lowStockCount,
      revenue_this_month: revenueThisMonth,
      orders_this_month: thisMonthOrders.length,
    });
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: "Internal Server Error" });
  }
});

router.get("/sales-by-year", requireAuth, requireRole("admin"), async (req, res) => {
  try {
    const year = parseInt((req.query.year as string) || String(new Date().getFullYear()));

    const orders = await db.select().from(ordersTable)
      .where(and(
        sql`EXTRACT(YEAR FROM ${ordersTable.created_at}) = ${year}`,
        sql`${ordersTable.status} != 'cancelled'`
      ));

    const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    const data = months.map((month, idx) => {
      const monthOrders = orders.filter(o => new Date(o.created_at).getMonth() === idx);
      return {
        month,
        month_num: idx + 1,
        revenue: monthOrders.reduce((sum, o) => sum + parseFloat(o.total_amount), 0),
        orders: monthOrders.length,
      };
    });

    res.json({ year, data });
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: "Internal Server Error" });
  }
});

router.get("/sales-by-product", requireAuth, requireRole("admin"), async (req, res) => {
  try {
    const year = parseInt((req.query.year as string) || String(new Date().getFullYear()));

    const orderItems = await db.select({
      item: orderItemsTable,
      order: ordersTable,
    })
      .from(orderItemsTable)
      .leftJoin(ordersTable, eq(orderItemsTable.order_id, ordersTable.id))
      .where(and(
        sql`EXTRACT(YEAR FROM ${ordersTable.created_at}) = ${year}`,
        sql`${ordersTable.status} != 'cancelled'`
      ));

    const productMap = new Map<number, { name: string; revenue: number; sold: number }>();
    for (const row of orderItems) {
      const key = row.item.product_id;
      const revenue = parseFloat(row.item.unit_price) * row.item.quantity;
      const existing = productMap.get(key);
      if (existing) {
        existing.revenue += revenue;
        existing.sold += row.item.quantity;
      } else {
        productMap.set(key, { name: row.item.product_name, revenue, sold: row.item.quantity });
      }
    }

    const totalRevenue = Array.from(productMap.values()).reduce((sum, p) => sum + p.revenue, 0);

    const products = Array.from(productMap.entries())
      .map(([product_id, data]) => ({
        product_id,
        product_name: data.name,
        total_revenue: data.revenue,
        total_sold: data.sold,
        percentage: totalRevenue > 0 ? (data.revenue / totalRevenue) * 100 : 0,
      }))
      .sort((a, b) => b.total_revenue - a.total_revenue)
      .slice(0, 10);

    res.json({ year, products });
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: "Internal Server Error" });
  }
});

router.get("/low-stock", requireAuth, requireRole("admin"), async (_req, res) => {
  try {
    const products = await db.select({
      product: productsTable,
      farmer: { name: usersTable.name },
    })
      .from(productsTable)
      .leftJoin(usersTable, eq(productsTable.farmer_id, usersTable.id))
      .where(eq(productsTable.is_active, true));

    const lowStock = products
      .filter(r => r.product.stock_quantity <= r.product.low_stock_threshold)
      .map(r => ({
        id: r.product.id,
        name: r.product.name,
        stock_quantity: r.product.stock_quantity,
        low_stock_threshold: r.product.low_stock_threshold,
        farmer_name: r.farmer?.name || "",
        unit: r.product.unit,
      }));

    res.json(lowStock);
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: "Internal Server Error" });
  }
});

// Farmer stats endpoint
router.get("/farmer-stats", requireAuth, requireRole("farmer"), async (req: AuthRequest, res) => {
  try {
    const farmerId = req.user!.id;

    const products = await db.select().from(productsTable).where(eq(productsTable.farmer_id, farmerId));
    const productIds = products.map(p => p.id);

    // Get order items for farmer's products
    let totalRevenue = 0;
    const orderSet = new Set<number>();

    for (const productId of productIds) {
      const items = await db.select({ item: orderItemsTable, order: ordersTable })
        .from(orderItemsTable)
        .leftJoin(ordersTable, eq(orderItemsTable.order_id, ordersTable.id))
        .where(and(
          eq(orderItemsTable.product_id, productId),
          sql`${ordersTable.status} != 'cancelled'`
        ));

      for (const row of items) {
        totalRevenue += parseFloat(row.item.unit_price) * row.item.quantity;
        orderSet.add(row.item.order_id);
      }
    }

    const lowStockProducts = products.filter(p => p.stock_quantity <= p.low_stock_threshold).length;
    const unreadNotifications = await db.select().from(notificationsTable)
      .where(and(eq(notificationsTable.user_id, farmerId), eq(notificationsTable.is_read, false)));

    res.json({
      total_products: products.length,
      total_revenue: totalRevenue,
      total_orders: orderSet.size,
      low_stock_products: lowStockProducts,
      unread_notifications: unreadNotifications.length,
    });
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: "Internal Server Error" });
  }
});

export default router;
