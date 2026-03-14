import { Router } from "express";
import { db, reviewsTable, usersTable, ordersTable, orderItemsTable } from "@workspace/db";
import { eq, and } from "drizzle-orm";
import { requireAuth, type AuthRequest } from "../lib/auth.js";

const router = Router();

router.get("/", async (req, res) => {
  try {
    const { product_id, page = "1", limit = "10" } = req.query as Record<string, string>;
    if (!product_id) {
      res.status(400).json({ error: "Bad Request", message: "product_id is required" });
      return;
    }

    const pageNum = parseInt(page);
    const limitNum = parseInt(limit);
    const offset = (pageNum - 1) * limitNum;

    const allReviews = await db.select({
      review: reviewsTable,
      reviewer: { name: usersTable.name },
    })
      .from(reviewsTable)
      .leftJoin(usersTable, eq(reviewsTable.user_id, usersTable.id))
      .where(eq(reviewsTable.product_id, parseInt(product_id)));

    const total = allReviews.length;
    const avgRating = total > 0 ? allReviews.reduce((sum, r) => sum + r.review.rating, 0) / total : 0;

    const reviews = allReviews.slice(offset, offset + limitNum).map(r => ({
      id: r.review.id,
      product_id: r.review.product_id,
      user_id: r.review.user_id,
      reviewer_name: r.reviewer?.name || "Anonymous",
      rating: r.review.rating,
      comment: r.review.comment,
      created_at: r.review.created_at,
      updated_at: r.review.updated_at,
    }));

    res.json({ reviews, total, average_rating: avgRating, page: pageNum, limit: limitNum });
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: "Internal Server Error" });
  }
});

router.post("/", requireAuth, async (req: AuthRequest, res) => {
  try {
    const { product_id, rating, comment } = req.body;
    if (!product_id || !rating) {
      res.status(400).json({ error: "Bad Request", message: "product_id and rating are required" });
      return;
    }

    if (rating < 1 || rating > 5) {
      res.status(400).json({ error: "Bad Request", message: "Rating must be 1-5" });
      return;
    }

    // Check if buyer has purchased this product
    const orders = await db.select({ orderId: ordersTable.id })
      .from(ordersTable)
      .where(and(eq(ordersTable.user_id, req.user!.id), eq(ordersTable.status, "delivered")));

    const orderIds = orders.map(o => o.orderId);
    let hasPurchased = false;
    for (const orderId of orderIds) {
      const [item] = await db.select().from(orderItemsTable)
        .where(and(eq(orderItemsTable.order_id, orderId), eq(orderItemsTable.product_id, product_id)))
        .limit(1);
      if (item) { hasPurchased = true; break; }
    }

    // Allow admins/farmers to review for testing, but buyers need purchase
    if (req.user!.role === "buyer" && !hasPurchased) {
      res.status(403).json({ error: "Forbidden", message: "You must purchase this product before leaving a review" });
      return;
    }

    // Check for existing review
    const [existing] = await db.select().from(reviewsTable)
      .where(and(eq(reviewsTable.product_id, product_id), eq(reviewsTable.user_id, req.user!.id)))
      .limit(1);

    if (existing) {
      res.status(400).json({ error: "Bad Request", message: "You have already reviewed this product" });
      return;
    }

    const [review] = await db.insert(reviewsTable).values({
      product_id,
      user_id: req.user!.id,
      rating,
      comment: comment || null,
    }).returning();

    const [user] = await db.select().from(usersTable).where(eq(usersTable.id, req.user!.id)).limit(1);

    res.status(201).json({
      id: review.id,
      product_id: review.product_id,
      user_id: review.user_id,
      reviewer_name: user.name,
      rating: review.rating,
      comment: review.comment,
      created_at: review.created_at,
      updated_at: review.updated_at,
    });
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: "Internal Server Error" });
  }
});

router.put("/:id", requireAuth, async (req: AuthRequest, res) => {
  try {
    const id = parseInt(req.params.id);
    const { rating, comment } = req.body;

    const [review] = await db.select().from(reviewsTable).where(eq(reviewsTable.id, id)).limit(1);
    if (!review) {
      res.status(404).json({ error: "Not Found" });
      return;
    }

    if (req.user!.role !== "admin" && review.user_id !== req.user!.id) {
      res.status(403).json({ error: "Forbidden" });
      return;
    }

    const updates: Record<string, any> = { updated_at: new Date() };
    if (rating !== undefined) updates.rating = rating;
    if (comment !== undefined) updates.comment = comment;

    const [updated] = await db.update(reviewsTable).set(updates).where(eq(reviewsTable.id, id)).returning();
    const [user] = await db.select().from(usersTable).where(eq(usersTable.id, updated.user_id)).limit(1);

    res.json({
      id: updated.id,
      product_id: updated.product_id,
      user_id: updated.user_id,
      reviewer_name: user.name,
      rating: updated.rating,
      comment: updated.comment,
      created_at: updated.created_at,
      updated_at: updated.updated_at,
    });
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: "Internal Server Error" });
  }
});

router.delete("/:id", requireAuth, async (req: AuthRequest, res) => {
  try {
    const id = parseInt(req.params.id);
    const [review] = await db.select().from(reviewsTable).where(eq(reviewsTable.id, id)).limit(1);
    if (!review) {
      res.status(404).json({ error: "Not Found" });
      return;
    }
    if (req.user!.role !== "admin" && review.user_id !== req.user!.id) {
      res.status(403).json({ error: "Forbidden" });
      return;
    }
    await db.delete(reviewsTable).where(eq(reviewsTable.id, id));
    res.json({ message: "Review deleted" });
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: "Internal Server Error" });
  }
});

export default router;
