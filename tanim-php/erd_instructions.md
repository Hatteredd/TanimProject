# Tanim Database ERD Creation Guide

## Quick ERD Solutions

### Option 1: Import SQL File (Recommended)
1. Open MySQL Workbench
2. File → Open Script → `export_schema.sql`
3. Execute the script to create tables
4. Database → Reverse Engineer → Select All Tables
5. Create EER Diagram

### Option 2: Use DBeaver (More Stable)
1. Download DBeaver: https://dbeaver.io/
2. Create MySQL connection:
   - Host: 127.0.0.1
   - Port: 3306
   - Database: tanim_db
   - User: root
   - Password: (blank)
3. Right-click database → ERD → Generate Diagram

### Option 3: Online ERD Tools
- https://dbdiagram.io (import SQL)
- https://www.dbdesigner.net/
- https://draw.io (database templates)

## Database Relationships:
```
Users 1→N Products (seller)
Users 1→N Orders (buyer)
Users 1→N Reviews (reviewer)
Products 1→N Cart_Items (shopping cart)
Products 1→N Reviews (product being reviewed)
Orders 1→N Order_Items (order contents)
```

## Table Structure Summary:
- **users**: Accounts, authentication, profiles
- **products**: Farm inventory, catalog
- **orders**: Customer purchases, delivery tracking
- **reviews**: Product ratings, feedback
- **cart_items**: Shopping cart management
- **employees**: Staff, suppliers, payroll
- **expenses**: Financial tracking, operations costs
