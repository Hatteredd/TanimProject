# Tanim - Agricultural Supply Chain Management Platform

## Overview

Tanim is a full-stack web application for managing an agricultural supply chain, connecting farmers directly to buyers with admin oversight.

## Stack

- **Monorepo tool**: pnpm workspaces
- **Node.js version**: 24
- **Package manager**: pnpm
- **TypeScript version**: 5.9
- **Frontend**: React + Vite (Tailwind CSS, Shadcn/ui, Recharts)
- **API framework**: Express 5
- **Database**: PostgreSQL + Drizzle ORM
- **Validation**: Zod (`zod/v4`), `drizzle-zod`
- **Auth**: JWT (jsonwebtoken + bcryptjs)
- **API codegen**: Orval (from OpenAPI spec)
- **Build**: esbuild (CJS bundle)

## Structure

```text
artifacts-monorepo/
├── artifacts/
│   ├── api-server/         # Express 5 backend API
│   └── tanim/              # React + Vite frontend (served at /)
├── lib/
│   ├── api-spec/           # OpenAPI spec + Orval codegen config
│   ├── api-client-react/   # Generated React Query hooks
│   ├── api-zod/            # Generated Zod schemas from OpenAPI
│   └── db/                 # Drizzle ORM schema + DB connection
```

## Features

### Multi-Role User System
- **Buyers**: Browse products, cart, checkout, orders, reviews
- **Farmers**: Manage their own products, view low-stock alerts, farmer dashboard
- **Admins**: Full platform management, charts/analytics, order status updates

### Product Catalog
- Search by name, filter by category, price range, stock status
- Product detail with reviews and ratings (5-star)

### Shopping Cart & Checkout
- Persistent cart saved in database
- Checkout with COD or Online payment
- Automatic stock deduction on order confirmation

### Low Stock Notifications
- Auto-notifies farmer when product stock drops below threshold
- Threshold configurable per product (default: 10 units)

### Order Management
- Admins can update order status (pending → confirmed → processing → shipped → delivered → cancelled)
- Buyers notified of status changes
- HTML receipt download for completed orders

### Admin Dashboard (Charts)
- Stats cards: revenue, orders, products, users
- Yearly sales bar chart (monthly breakdown) using Recharts
- Product sales pie chart using Recharts

### Review System
- Only buyers who purchased a product can review it
- Update and delete reviews

## Test Accounts
- Admin: `admin@tanim.ph` / `admin123`
- Farmer 1: `farmer1@tanim.ph` / `farmer123` (Santos Fresh Farm, Benguet)
- Farmer 2: `farmer2@tanim.ph` / `farmer123` (Reyes Organic Farm, Nueva Ecija)
- Buyer: `buyer1@tanim.ph` / `buyer123`

## API Routes

All routes under `/api`:
- `/auth` - register, login, logout, /me
- `/users` - CRUD user management
- `/categories` - product categories
- `/products` - product catalog with search/filter
- `/cart` - persistent shopping cart
- `/orders` - order management + receipt download
- `/reviews` - product reviews
- `/notifications` - user notifications
- `/dashboard` - admin analytics (stats, sales-by-year, sales-by-product, low-stock)
- `/farmer/products` - farmer's own products
- `/farmer/stats` - farmer dashboard stats

## Database Schema

Tables: `users`, `categories`, `products`, `orders`, `order_items`, `cart_items`, `reviews`, `notifications`

All following 2NF normalization principles.
