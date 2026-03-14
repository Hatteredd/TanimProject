import { Router } from "express";
import { db, usersTable } from "@workspace/db";
import { eq, and } from "drizzle-orm";
import { requireAuth, requireRole, type AuthRequest } from "../lib/auth.js";

const router = Router();

router.get("/", requireAuth, requireRole("admin"), async (req, res) => {
  try {
    const { role, page = "1", limit = "20" } = req.query as Record<string, string>;
    const pageNum = parseInt(page);
    const limitNum = parseInt(limit);
    const offset = (pageNum - 1) * limitNum;

    let query = db.select().from(usersTable);
    const conditions = [];
    if (role) conditions.push(eq(usersTable.role, role as any));

    const allUsers = conditions.length > 0
      ? await db.select().from(usersTable).where(conditions[0])
      : await db.select().from(usersTable);

    const total = allUsers.length;
    const users = allUsers.slice(offset, offset + limitNum).map(u => ({
      id: u.id,
      name: u.name,
      email: u.email,
      role: u.role,
      phone: u.phone,
      address: u.address,
      farm_name: u.farm_name,
      farm_location: u.farm_location,
      is_active: u.is_active,
      created_at: u.created_at,
    }));

    res.json({ users, total, page: pageNum, limit: limitNum });
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: "Internal Server Error" });
  }
});

router.get("/:id", requireAuth, async (req: AuthRequest, res) => {
  try {
    const id = parseInt(req.params.id);
    if (req.user!.role !== "admin" && req.user!.id !== id) {
      res.status(403).json({ error: "Forbidden" });
      return;
    }
    const [user] = await db.select().from(usersTable).where(eq(usersTable.id, id)).limit(1);
    if (!user) {
      res.status(404).json({ error: "Not Found" });
      return;
    }
    res.json({
      id: user.id, name: user.name, email: user.email, role: user.role,
      phone: user.phone, address: user.address, farm_name: user.farm_name,
      farm_location: user.farm_location, is_active: user.is_active, created_at: user.created_at,
    });
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: "Internal Server Error" });
  }
});

router.put("/:id", requireAuth, async (req: AuthRequest, res) => {
  try {
    const id = parseInt(req.params.id);
    if (req.user!.role !== "admin" && req.user!.id !== id) {
      res.status(403).json({ error: "Forbidden" });
      return;
    }

    const { name, phone, address, farm_name, farm_location, is_active, low_stock_threshold } = req.body;
    const updates: Record<string, any> = { updated_at: new Date() };
    if (name !== undefined) updates.name = name;
    if (phone !== undefined) updates.phone = phone;
    if (address !== undefined) updates.address = address;
    if (farm_name !== undefined) updates.farm_name = farm_name;
    if (farm_location !== undefined) updates.farm_location = farm_location;
    if (is_active !== undefined && req.user!.role === "admin") updates.is_active = is_active;
    if (low_stock_threshold !== undefined) updates.low_stock_threshold = low_stock_threshold;

    const [updated] = await db.update(usersTable).set(updates).where(eq(usersTable.id, id)).returning();
    res.json({
      id: updated.id, name: updated.name, email: updated.email, role: updated.role,
      phone: updated.phone, address: updated.address, farm_name: updated.farm_name,
      farm_location: updated.farm_location, is_active: updated.is_active, created_at: updated.created_at,
    });
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: "Internal Server Error" });
  }
});

router.delete("/:id", requireAuth, requireRole("admin"), async (req, res) => {
  try {
    const id = parseInt(req.params.id);
    await db.update(usersTable).set({ is_active: false }).where(eq(usersTable.id, id));
    res.json({ message: "User deactivated" });
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: "Internal Server Error" });
  }
});

export default router;
