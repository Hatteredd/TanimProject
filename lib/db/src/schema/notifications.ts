import { pgTable, serial, integer, text, boolean, timestamp } from "drizzle-orm/pg-core";
import { usersTable } from "./users";

export const notificationsTable = pgTable("notifications", {
  id: serial("id").primaryKey(),
  user_id: integer("user_id").notNull().references(() => usersTable.id),
  type: text("type", { enum: ["low_stock", "order_status", "new_order", "payment", "system"] }).notNull(),
  title: text("title").notNull(),
  message: text("message").notNull(),
  is_read: boolean("is_read").notNull().default(false),
  related_id: integer("related_id"),
  created_at: timestamp("created_at").notNull().defaultNow(),
});

export type Notification = typeof notificationsTable.$inferSelect;
