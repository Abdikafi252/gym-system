# Render Deployment Guide: Solving Database Connection Failed

Since Render handles MySQL differently than a local XAMPP setup, follow these steps to connect your system correctly.

## 1. Get a Managed MySQL Database
Render natively supports PostgreSQL, but for MySQL you need an external provider. I recommend **Aiven**, **TiDB Cloud**, or **PlanetScale** (for free/low-cost tiers).

- **Host**: (e.g., `mysql-1234567.aivencloud.com`)
- **Port**: `3306`
- **Database**: `gymnsb`
- **User**: (your-db-username)
- **Password**: (your-db-password)

## 2. Configure Environment Variables on Render
Go to your Render Dashboard → **M-A-GYM** → **Environment** and add the following keys:

| Key | Value |
| :--- | :--- |
| `DB_HOST` | your-external-host-address |
| `DB_USER` | your-database-username |
| `DB_PASS` | your-database-password |
| `DB_NAME` | `gymnsb` |

> [!IMPORTANT]
> Your `dbcon.php` is already designed to read these variables automatically. You do NOT need to change any code.

## 3. Verify Connection
I've added a tool for you to check if the connection is working. After you set the environment variables, visit:
`https://m-a-gym.onrender.com/api/check-db.php`

If it says "Connection Successful!", you are good to go!

---
*Iga xali inta dhiman - Guul!*
