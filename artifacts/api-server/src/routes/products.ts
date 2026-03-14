import { Router } from "express";
import { db, productsTable, usersTable, categoriesTable, reviewsTable, notificationsTable } from "@workspace/db";
import { eq, and, ilike, gte, lte, sql, desc, asc } from "drizzle-orm";
import { requireAuth, requireRole, type AuthRequest } from "../lib/auth.js";

const router = Router();

function buildProductResult(p: any, farmerName: string, farmName: string | null, categoryName: string | null, avgRating: number | null, reviewCount: number) {
  return {
    id: p.id,
    name: p.name,
    description: p.description,
    price: parseFloat(p.price),
    stock_quantity: p.stock_quantity,
    unit: p.unit,
    image_url: p.image_url,
    category_id: p.category_id,
    category_name: categoryName,
    farmer_id: p.farmer_id,
    farmer_name: farmerName,
    farm_name: farmName,
    average_rating: avgRating,
    review_count: reviewCount,
    low_stock_threshold: p.low_stock_threshold,
    is_active: p.is_active,
    created_at: p.created_at,
  };
}

router.get("/", async (req, res) => {
  try {
    const { search, category_id, farmer_id, min_price, max_price, in_stock, sort_by, page = "1", limit = "12" } = req.query as Record<string, string>;
    const pageNum = parseInt(page);
    const limitNum = parseInt(limit);
    const offset = (pageNum - 1) * limitNum;

    let allProducts = await db.select({
      product: productsTable,
      farmer: { name: usersTable.name, farm_name: usersTable.farm_name },
      category: { name: categoriesTable.name },
    })
      .from(productsTable)
      .leftJoin(usersTable, eq(productsTable.farmer_id, usersTable.id))
      .leftJoin(categoriesTable, eq(productsTable.category_id, categoriesTable.id))
      .where(eq(productsTable.is_active, true));

    let filtered = allProducts.filter(row => {
      const p = row.product;
      if (search && !p.name.toLowerCase().includes(search.toLowerCase())) return false;
      if (category_id && p.category_id !== parseInt(category_id)) return false;
      if (farmer_id && p.farmer_id !== parseInt(farmer_id)) return false;
      if (min_price && parseFloat(p.price) < parseFloat(min_price)) return false;
      if (max_price && parseFloat(p.price) > parseFloat(max_price)) return false;
      if (in_stock === "true" && p.stock_quantity <= 0) return false;
      return true;
    });

    const total = filtered.length;

    const resultsWithRatings = await Promise.all(filtered.slice(offset, offset + limitNum).map(async row => {
      const reviews = await db.select({ rating: reviewsTable.rating }).from(reviewsTable).where(eq(reviewsTable.product_id, row.product.id));
      const avgRating = reviews.length > 0 ? reviews.reduce((a, r) => a + r.rating, 0) / reviews.length : null;
      return buildProductResult(row.product, row.farmer?.name || "", row.farmer?.farm_name || null, row.category?.name || null, avgRating, reviews.length);
    }));

    res.json({ products: resultsWithRatings, total, page: pageNum, limit: limitNum });
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: "Internal Server Error" });
  }
});

router.post("/", requireAuth, requireRole("farmer", "admin"), async (req: AuthRequest, res) => {
  try {
    const { name, description, price, stock_quantity, unit, image_url, category_id, low_stock_threshold = 10 } = req.body;
    const farmer_id = req.user!.role === "admin" ? (req.body.farmer_id || req.user!.id) : req.user!.id;

    const [product] = await db.insert(productsTable).values({
      name, description, price: String(price), stock_quantity, unit: unit || "kg",
      image_url, category_id: category_id || null, farmer_id, low_stock_threshold,
    }).returning();

    const [farmer] = await db.select().from(usersTable).where(eq(usersTable.id, product.farmer_id)).limit(1);
    res.status(201).json(buildProductResult(product, farmer.name, farmer.farm_name, null, null, 0));
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: "Internal Server Error" });
  }
});

router.get("/farmer", requireAuth, requireRole("farmer", "admin"), async (req: AuthRequest, res) => {
  try {
    const { page = "1", limit = "20" } = req.query as Record<string, string>;
    const pageNum = parseInt(page);
    const limitNum = parseInt(limit);
    const offset = (pageNum - 1) * limitNum;

    const farmerId = req.user!.id;
    const allProducts = await db.select().from(productsTable).where(eq(productsTable.farmer_id, farmerId));
    const total = allProducts.length;

    const [farmer] = await db.select().from(usersTable).where(eq(usersTable.id, farmerId)).limit(1);

    const products = await Promise.all(allProducts.slice(offset, offset + limitNum).map(async p => {
      const reviews = await db.select({ rating: reviewsTable.rating }).from(reviewsTable).where(eq(reviewsTable.product_id, p.id));
      const avgRating = reviews.length > 0 ? reviews.reduce((a, r) => a + r.rating, 0) / reviews.length : null;
      return buildProductResult(p, farmer.name, farmer.farm_name, null, avgRating, reviews.length);
    }));

    res.json({ products, total, page: pageNum, limit: limitNum });
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: "Internal Server Error" });
  }
});

router.get("/:id", async (req, res) => {
  try {
    const id = parseInt(req.params.id);
    const [row] = await db.select({
      product: productsTable,
      farmer: { name: usersTable.name, farm_name: usersTable.farm_name },
      category: { name: categoriesTable.name },
    })
      .from(productsTable)
      .leftJoin(usersTable, eq(productsTable.farmer_id, usersTable.id))
      .leftJoin(categoriesTable, eq(productsTable.category_id, categoriesTable.id))
      .where(eq(productsTable.id, id))
      .limit(1);

    if (!row) {
      res.status(404).json({ error: "Not Found" });
      return;
    }

    const reviewRows = await db.select({
      review: reviewsTable,
      reviewer: { name: usersTable.name },
    })
      .from(reviewsTable)
      .leftJoin(usersTable, eq(reviewsTable.user_id, usersTable.id))
      .where(eq(reviewsTable.product_id, id));

    const reviews = reviewRows.map(r => ({
      id: r.review.id,
      product_id: r.review.product_id,
      user_id: r.review.user_id,
      reviewer_name: r.reviewer?.name || "Anonymous",
      rating: r.review.rating,
      comment: r.review.comment,
      created_at: r.review.created_at,
      updated_at: r.review.updated_at,
    }));

    const avgRating = reviews.length > 0 ? reviews.reduce((a, r) => a + r.rating, 0) / reviews.length : null;
    const base = buildProductResult(row.product, row.farmer?.name || "", row.farmer?.farm_name || null, row.category?.name || null, avgRating, reviews.length);

    res.json({ ...base, reviews });
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: "Internal Server Error" });
  }
});

router.put("/:id", requireAuth, async (req: AuthRequest, res) => {
  try {
    const id = parseInt(req.params.id);
    const [product] = await db.select().from(productsTable).where(eq(productsTable.id, id)).limit(1);
    if (!product) {
      res.status(404).json({ error: "Not Found" });
      return;
    }
    if (req.user!.role === "farmer" && product.farmer_id !== req.user!.id) {
      res.status(403).json({ error: "Forbidden" });
      return;
    }

    const updates: Record<string, any> = { updated_at: new Date() };
    const fields = ["name", "description", "price", "stock_quantity", "unit", "image_url", "category_id", "low_stock_threshold", "is_active"];
    for (const field of fields) {
      if (req.body[field] !== undefined) {
        updates[field] = field === "price" ? String(req.body[field]) : req.body[field];
      }
    }

    const [updated] = await db.update(productsTable).set(updates).where(eq(productsTable.id, id)).returning();

    // Check low stock after update
    if (updated.stock_quantity <= updated.low_stock_threshold) {
      await db.insert(notificationsTable).values({
        user_id: updated.farmer_id,
        type: "low_stock",
        title: "Low Stock Alert",
        message: `Product "${updated.name}" is running low. Only ${updated.stock_quantity} ${updated.unit} remaining.`,
        related_id: updated.id,
      });
    }

    const [farmer] = await db.select().from(usersTable).where(eq(usersTable.id, updated.farmer_id)).limit(1);
    const reviews = await db.select().from(reviewsTable).where(eq(reviewsTable.product_id, id));
    const avgRating = reviews.length > 0 ? reviews.reduce((a, r) => a + r.rating, 0) / reviews.length : null;

    res.json(buildProductResult(updated, farmer.name, farmer.farm_name, null, avgRating, reviews.length));
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: "Internal Server Error" });
  }
});

router.delete("/:id", requireAuth, async (req: AuthRequest, res) => {
  try {
    const id = parseInt(req.params.id);
    const [product] = await db.select().from(productsTable).where(eq(productsTable.id, id)).limit(1);
    if (!product) {
      res.status(404).json({ error: "Not Found" });
      return;
    }
    if (req.user!.role === "farmer" && product.farmer_id !== req.user!.id) {
      res.status(403).json({ error: "Forbidden" });
      return;
    }
    await db.update(productsTable).set({ is_active: false }).where(eq(productsTable.id, id));
    res.json({ message: "Product deleted" });
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: "Internal Server Error" });
  }
});

export default router;
