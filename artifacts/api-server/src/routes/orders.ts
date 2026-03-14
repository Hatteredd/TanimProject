import { Router } from "express";
import { db, ordersTable, orderItemsTable, cartItemsTable, productsTable, usersTable, notificationsTable } from "@workspace/db";
import { eq, and, sql } from "drizzle-orm";
import { requireAuth, requireRole, type AuthRequest } from "../lib/auth.js";

const router = Router();

function formatOrder(order: any, buyerName: string) {
  return {
    id: order.id,
    user_id: order.user_id,
    buyer_name: buyerName,
    status: order.status,
    payment_method: order.payment_method,
    payment_status: order.payment_status,
    total_amount: parseFloat(order.total_amount),
    shipping_address: order.shipping_address,
    notes: order.notes,
    created_at: order.created_at,
    updated_at: order.updated_at,
  };
}

router.get("/", requireAuth, async (req: AuthRequest, res) => {
  try {
    const { status, page = "1", limit = "20" } = req.query as Record<string, string>;
    const pageNum = parseInt(page);
    const limitNum = parseInt(limit);
    const offset = (pageNum - 1) * limitNum;

    let allOrders: any[];
    if (req.user!.role === "admin") {
      allOrders = await db.select({
        order: ordersTable,
        buyer: { name: usersTable.name },
      })
        .from(ordersTable)
        .leftJoin(usersTable, eq(ordersTable.user_id, usersTable.id));
    } else {
      allOrders = await db.select({
        order: ordersTable,
        buyer: { name: usersTable.name },
      })
        .from(ordersTable)
        .leftJoin(usersTable, eq(ordersTable.user_id, usersTable.id))
        .where(eq(ordersTable.user_id, req.user!.id));
    }

    if (status) {
      allOrders = allOrders.filter(r => r.order.status === status);
    }

    const total = allOrders.length;
    const orders = allOrders.slice(offset, offset + limitNum).map(r => formatOrder(r.order, r.buyer?.name || ""));

    res.json({ orders, total, page: pageNum, limit: limitNum });
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: "Internal Server Error" });
  }
});

router.post("/", requireAuth, requireRole("buyer"), async (req: AuthRequest, res) => {
  try {
    const { shipping_address, payment_method, notes } = req.body;
    if (!shipping_address || !payment_method) {
      res.status(400).json({ error: "Bad Request", message: "shipping_address and payment_method are required" });
      return;
    }

    // Get cart items
    const cartRows = await db.select({
      cart: cartItemsTable,
      product: productsTable,
    })
      .from(cartItemsTable)
      .leftJoin(productsTable, eq(cartItemsTable.product_id, productsTable.id))
      .where(eq(cartItemsTable.user_id, req.user!.id));

    if (cartRows.length === 0) {
      res.status(400).json({ error: "Bad Request", message: "Cart is empty" });
      return;
    }

    // Validate stock
    for (const row of cartRows) {
      if (!row.product || row.product.stock_quantity < row.cart.quantity) {
        res.status(400).json({
          error: "Bad Request",
          message: `Insufficient stock for product: ${row.product?.name || "unknown"}`,
        });
        return;
      }
    }

    const totalAmount = cartRows.reduce((sum, row) => {
      return sum + (parseFloat(row.product?.price || "0") * row.cart.quantity);
    }, 0);

    // Create order
    const [order] = await db.insert(ordersTable).values({
      user_id: req.user!.id,
      status: "pending",
      payment_method,
      payment_status: payment_method === "online" ? "pending" : "pending",
      total_amount: String(totalAmount),
      shipping_address,
      notes: notes || null,
    }).returning();

    // Create order items and deduct stock
    for (const row of cartRows) {
      if (!row.product) continue;

      await db.insert(orderItemsTable).values({
        order_id: order.id,
        product_id: row.product.id,
        product_name: row.product.name,
        quantity: row.cart.quantity,
        unit_price: row.product.price,
        unit: row.product.unit,
      });

      // Deduct stock
      const newStock = row.product.stock_quantity - row.cart.quantity;
      await db.update(productsTable)
        .set({ stock_quantity: newStock, updated_at: new Date() })
        .where(eq(productsTable.id, row.product.id));

      // Check low stock and notify farmer
      if (newStock <= row.product.low_stock_threshold) {
        await db.insert(notificationsTable).values({
          user_id: row.product.farmer_id,
          type: "low_stock",
          title: "Low Stock Alert",
          message: `Product "${row.product.name}" stock is low. Only ${newStock} ${row.product.unit} remaining.`,
          related_id: row.product.id,
        });
      }
    }

    // Clear cart
    await db.delete(cartItemsTable).where(eq(cartItemsTable.user_id, req.user!.id));

    // Notify admin about new order
    const admins = await db.select({ id: usersTable.id }).from(usersTable).where(eq(usersTable.role, "admin"));
    for (const admin of admins) {
      await db.insert(notificationsTable).values({
        user_id: admin.id,
        type: "new_order",
        title: "New Order Received",
        message: `A new order #${order.id} has been placed for ₱${totalAmount.toFixed(2)}.`,
        related_id: order.id,
      });
    }

    const [buyer] = await db.select().from(usersTable).where(eq(usersTable.id, req.user!.id)).limit(1);
    res.status(201).json(formatOrder(order, buyer.name));
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: "Internal Server Error" });
  }
});

router.get("/:id", requireAuth, async (req: AuthRequest, res) => {
  try {
    const id = parseInt(req.params.id);
    const [row] = await db.select({
      order: ordersTable,
      buyer: { name: usersTable.name },
    })
      .from(ordersTable)
      .leftJoin(usersTable, eq(ordersTable.user_id, usersTable.id))
      .where(eq(ordersTable.id, id))
      .limit(1);

    if (!row) {
      res.status(404).json({ error: "Not Found" });
      return;
    }

    if (req.user!.role !== "admin" && row.order.user_id !== req.user!.id) {
      res.status(403).json({ error: "Forbidden" });
      return;
    }

    const items = await db.select().from(orderItemsTable).where(eq(orderItemsTable.order_id, id));

    res.json({
      ...formatOrder(row.order, row.buyer?.name || ""),
      items: items.map(item => ({
        id: item.id,
        product_id: item.product_id,
        product_name: item.product_name,
        quantity: item.quantity,
        unit_price: parseFloat(item.unit_price),
        subtotal: parseFloat(item.unit_price) * item.quantity,
        unit: item.unit,
      })),
    });
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: "Internal Server Error" });
  }
});

router.put("/:id/status", requireAuth, requireRole("admin"), async (req: AuthRequest, res) => {
  try {
    const id = parseInt(req.params.id);
    const { status, notes } = req.body;

    const [order] = await db.update(ordersTable)
      .set({ status, notes: notes || undefined, updated_at: new Date() })
      .where(eq(ordersTable.id, id))
      .returning();

    if (!order) {
      res.status(404).json({ error: "Not Found" });
      return;
    }

    // Notify buyer about status update
    await db.insert(notificationsTable).values({
      user_id: order.user_id,
      type: "order_status",
      title: "Order Status Updated",
      message: `Your order #${order.id} status has been updated to ${status}.`,
      related_id: order.id,
    });

    const [buyer] = await db.select().from(usersTable).where(eq(usersTable.id, order.user_id)).limit(1);
    res.json(formatOrder(order, buyer.name));
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: "Internal Server Error" });
  }
});

router.get("/:id/receipt", requireAuth, async (req: AuthRequest, res) => {
  try {
    const id = parseInt(req.params.id);
    const [row] = await db.select({
      order: ordersTable,
      buyer: usersTable,
    })
      .from(ordersTable)
      .leftJoin(usersTable, eq(ordersTable.user_id, usersTable.id))
      .where(eq(ordersTable.id, id))
      .limit(1);

    if (!row) {
      res.status(404).json({ error: "Not Found" });
      return;
    }

    if (req.user!.role !== "admin" && row.order.user_id !== req.user!.id) {
      res.status(403).json({ error: "Forbidden" });
      return;
    }

    res.json({
      receipt_url: `/api/orders/${id}/receipt/download`,
      order_id: id,
      message: "Receipt is available for download",
    });
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: "Internal Server Error" });
  }
});

// PDF receipt download endpoint
router.get("/:id/receipt/download", requireAuth, async (req: AuthRequest, res) => {
  try {
    const id = parseInt(req.params.id);
    const [row] = await db.select({
      order: ordersTable,
      buyer: usersTable,
    })
      .from(ordersTable)
      .leftJoin(usersTable, eq(ordersTable.user_id, usersTable.id))
      .where(eq(ordersTable.id, id))
      .limit(1);

    if (!row) {
      res.status(404).json({ error: "Not Found" });
      return;
    }

    if (req.user!.role !== "admin" && row.order.user_id !== req.user!.id) {
      res.status(403).json({ error: "Forbidden" });
      return;
    }

    const items = await db.select().from(orderItemsTable).where(eq(orderItemsTable.order_id, id));
    const buyer = row.buyer;
    const order = row.order;

    // Generate a simple HTML receipt as PDF-like content
    const html = `
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Order Receipt #${order.id}</title>
  <style>
    body { font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; color: #333; }
    .header { text-align: center; border-bottom: 2px solid #22c55e; padding-bottom: 20px; margin-bottom: 20px; }
    .logo { font-size: 28px; font-weight: bold; color: #16a34a; }
    .subtitle { color: #666; }
    .section { margin-bottom: 20px; }
    .section h3 { color: #16a34a; border-bottom: 1px solid #e5e7eb; padding-bottom: 5px; }
    table { width: 100%; border-collapse: collapse; }
    th { background: #f0fdf4; text-align: left; padding: 8px; border: 1px solid #d1fae5; }
    td { padding: 8px; border: 1px solid #e5e7eb; }
    .total-row { background: #f0fdf4; font-weight: bold; }
    .badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 12px; background: #dcfce7; color: #16a34a; }
    .footer { text-align: center; margin-top: 30px; color: #666; font-size: 12px; border-top: 1px solid #e5e7eb; padding-top: 15px; }
  </style>
</head>
<body>
  <div class="header">
    <div class="logo">🌾 Tanim</div>
    <div class="subtitle">Agricultural Supply Chain Platform</div>
    <h2>ORDER RECEIPT</h2>
    <p>Receipt #${order.id} | ${new Date(order.created_at).toLocaleDateString('en-PH', { year: 'numeric', month: 'long', day: 'numeric' })}</p>
  </div>

  <div class="section">
    <h3>Customer Information</h3>
    <p><strong>Name:</strong> ${buyer?.name || 'N/A'}</p>
    <p><strong>Email:</strong> ${buyer?.email || 'N/A'}</p>
    <p><strong>Phone:</strong> ${buyer?.phone || 'N/A'}</p>
    <p><strong>Shipping Address:</strong> ${order.shipping_address}</p>
  </div>

  <div class="section">
    <h3>Order Details</h3>
    <p><strong>Order ID:</strong> #${order.id}</p>
    <p><strong>Status:</strong> <span class="badge">${order.status.toUpperCase()}</span></p>
    <p><strong>Payment Method:</strong> ${order.payment_method === 'cod' ? 'Cash on Delivery' : 'Online Payment'}</p>
    <p><strong>Payment Status:</strong> ${order.payment_status.toUpperCase()}</p>
    ${order.notes ? `<p><strong>Notes:</strong> ${order.notes}</p>` : ''}
  </div>

  <div class="section">
    <h3>Items Ordered</h3>
    <table>
      <thead>
        <tr>
          <th>Product</th>
          <th>Qty</th>
          <th>Unit Price</th>
          <th>Subtotal</th>
        </tr>
      </thead>
      <tbody>
        ${items.map(item => `
          <tr>
            <td>${item.product_name}</td>
            <td>${item.quantity} ${item.unit}</td>
            <td>₱${parseFloat(item.unit_price).toFixed(2)}</td>
            <td>₱${(parseFloat(item.unit_price) * item.quantity).toFixed(2)}</td>
          </tr>
        `).join('')}
        <tr class="total-row">
          <td colspan="3" style="text-align:right"><strong>Total Amount:</strong></td>
          <td><strong>₱${parseFloat(order.total_amount).toFixed(2)}</strong></td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="footer">
    <p>Thank you for choosing Tanim - Connecting Farmers to Buyers</p>
    <p>This receipt was generated on ${new Date().toLocaleString('en-PH')}</p>
  </div>
</body>
</html>`;

    res.setHeader("Content-Type", "text/html");
    res.setHeader("Content-Disposition", `attachment; filename="receipt-order-${id}.html"`);
    res.send(html);
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: "Internal Server Error" });
  }
});

export default router;
