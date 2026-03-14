import { Router } from "express";
import { db, usersTable } from "@workspace/db";
import { eq } from "drizzle-orm";
import { hashPassword, verifyPassword, signToken, requireAuth, type AuthRequest } from "../lib/auth.js";

const router = Router();

router.post("/register", async (req, res) => {
  try {
    const { name, email, password, role, phone, address, farm_name, farm_location } = req.body;

    if (!name || !email || !password || !role) {
      res.status(400).json({ error: "Bad Request", message: "name, email, password, and role are required" });
      return;
    }

    if (!["buyer", "farmer"].includes(role)) {
      res.status(400).json({ error: "Bad Request", message: "Role must be buyer or farmer" });
      return;
    }

    const existing = await db.select().from(usersTable).where(eq(usersTable.email, email)).limit(1);
    if (existing.length > 0) {
      res.status(400).json({ error: "Bad Request", message: "Email already in use" });
      return;
    }

    const [user] = await db.insert(usersTable).values({
      name,
      email,
      password_hash: hashPassword(password),
      role,
      phone: phone || null,
      address: address || null,
      farm_name: farm_name || null,
      farm_location: farm_location || null,
    }).returning();

    const token = signToken({ id: user.id, email: user.email, role: user.role });

    res.status(201).json({
      user: {
        id: user.id,
        name: user.name,
        email: user.email,
        role: user.role,
        phone: user.phone,
        address: user.address,
        farm_name: user.farm_name,
        farm_location: user.farm_location,
        is_active: user.is_active,
        created_at: user.created_at,
      },
      token,
      message: "Registration successful",
    });
  } catch (err) {
    console.error("Register error:", err);
    res.status(500).json({ error: "Internal Server Error" });
  }
});

router.post("/login", async (req, res) => {
  try {
    const { email, password } = req.body;

    if (!email || !password) {
      res.status(400).json({ error: "Bad Request", message: "Email and password are required" });
      return;
    }

    const [user] = await db.select().from(usersTable).where(eq(usersTable.email, email)).limit(1);

    if (!user || !verifyPassword(password, user.password_hash)) {
      res.status(401).json({ error: "Unauthorized", message: "Invalid email or password" });
      return;
    }

    if (!user.is_active) {
      res.status(401).json({ error: "Unauthorized", message: "Account is deactivated" });
      return;
    }

    const token = signToken({ id: user.id, email: user.email, role: user.role });

    res.json({
      user: {
        id: user.id,
        name: user.name,
        email: user.email,
        role: user.role,
        phone: user.phone,
        address: user.address,
        farm_name: user.farm_name,
        farm_location: user.farm_location,
        is_active: user.is_active,
        created_at: user.created_at,
      },
      token,
      message: "Login successful",
    });
  } catch (err) {
    console.error("Login error:", err);
    res.status(500).json({ error: "Internal Server Error" });
  }
});

router.post("/logout", (_req, res) => {
  res.json({ message: "Logged out successfully" });
});

router.get("/me", requireAuth, async (req: AuthRequest, res) => {
  try {
    const [user] = await db.select().from(usersTable).where(eq(usersTable.id, req.user!.id)).limit(1);
    if (!user) {
      res.status(404).json({ error: "Not Found" });
      return;
    }
    res.json({
      id: user.id,
      name: user.name,
      email: user.email,
      role: user.role,
      phone: user.phone,
      address: user.address,
      farm_name: user.farm_name,
      farm_location: user.farm_location,
      is_active: user.is_active,
      created_at: user.created_at,
    });
  } catch (err) {
    console.error("GetMe error:", err);
    res.status(500).json({ error: "Internal Server Error" });
  }
});

export default router;
