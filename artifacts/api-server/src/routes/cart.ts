import { Router } from "express";
import { db, cartItemsTable, productsTable, usersTable } from "@workspace/db";
import { eq, and, sql } from "drizzle-orm";
import { requireAuth, type AuthRequest } from "../lib/auth.js";

const router = Router();

async function buildCart(userId: number) {
  const items = await db.select({
    cartItem: cartItemsTable,
    product: productsTable,
    farmer: { name: usersTable.name },
  })
    .from(cartItemsTable)
    .leftJoin(productsTable, eq(cartItemsTable.product_id, productsTable.id))
    .leftJoin(usersTable, eq(productsTable.farmer_id, usersTable.id))
    .where(eq(cartItemsTable.user_id, userId));

  const cartItems = items.map(row => ({
    id: row.cartItem.id,
    product_id: row.cartItem.product_id,
    product_name: row.product?.name || "",
    product_image: row.product?.image_url || null,
    price: parseFloat(row.product?.price || "0"),
    quantity: row.cartItem.quantity,
    stock_quantity: row.product?.stock_quantity || 0,
    unit: row.product?.unit || "kg",
    farmer_name: row.farmer?.name || "",
    subtotal: parseFloat(row.product?.price || "0") * row.cartItem.quantity,
  }));

  const total = cartItems.reduce((sum, item) => sum + item.subtotal, 0);
  const item_count = cartItems.reduce((sum, item) => sum + item.quantity, 0);

  return { items: cartItems, total, item_count };
}

router.get("/", requireAuth, async (req: AuthRequest, res) => {
  try {
    const cart = await buildCart(req.user!.id);
    res.json(cart);
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: "Internal Server Error" });
  }
});

router.delete("/", requireAuth, async (req: AuthRequest, res) => {
  try {
    await db.delete(cartItemsTable).where(eq(cartItemsTable.user_id, req.user!.id));
    res.json({ message: "Cart cleared" });
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: "Internal Server Error" });
  }
});

router.post("/items", requireAuth, async (req: AuthRequest, res) => {
  try {
    const { product_id, quantity } = req.body;
    if (!product_id || !quantity) {
      res.status(400).json({ error: "Bad Request", message: "product_id and quantity are required" });
      return;
    }

    const [product] = await db.select().from(productsTable).where(eq(productsTable.id, product_id)).limit(1);
    if (!product) {
      res.status(404).json({ error: "Not Found", message: "Product not found" });
      return;
    }

    const [existing] = await db.select().from(cartItemsTable)
      .where(and(eq(cartItemsTable.user_id, req.user!.id), eq(cartItemsTable.product_id, product_id)))
      .limit(1);

    if (existing) {
      await db.update(cartItemsTable)
        .set({ quantity: existing.quantity + quantity, updated_at: new Date() })
        .where(eq(cartItemsTable.id, existing.id));
    } else {
      await db.insert(cartItemsTable).values({
        user_id: req.user!.id,
        product_id,
        quantity,
      });
    }

    const cart = await buildCart(req.user!.id);
    res.json(cart);
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: "Internal Server Error" });
  }
});

router.put("/items/:productId", requireAuth, async (req: AuthRequest, res) => {
  try {
    const productId = parseInt(req.params.productId);
    const { quantity } = req.body;

    await db.update(cartItemsTable)
      .set({ quantity, updated_at: new Date() })
      .where(and(eq(cartItemsTable.user_id, req.user!.id), eq(cartItemsTable.product_id, productId)));

    const cart = await buildCart(req.user!.id);
    res.json(cart);
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: "Internal Server Error" });
  }
});

router.delete("/items/:productId", requireAuth, async (req: AuthRequest, res) => {
  try {
    const productId = parseInt(req.params.productId);
    await db.delete(cartItemsTable)
      .where(and(eq(cartItemsTable.user_id, req.user!.id), eq(cartItemsTable.product_id, productId)));

    const cart = await buildCart(req.user!.id);
    res.json(cart);
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: "Internal Server Error" });
  }
});

export default router;
