# M-Pesa Payment App - Render Deployment Guide

## Quick Start

This app is configured for deployment on Render. Follow these steps:

### 1. Push to GitHub

```bash
git init
git add .
git commit -m "Initial commit"
git branch -M main
git remote add origin https://github.com/your-username/your-repo.git
git push -u origin main
```

### 2. Deploy to Render

1. Go to https://render.com and sign up/login
2. Click "New +" → "Web Service"
3. Connect your GitHub repository
4. Configure:
   - **Name**: `mpesa-payment-app`
   - **Environment**: `PHP`
   - **Build Command**: (leave empty)
   - **Start Command**: `php -S 0.0.0.0:$PORT`
   - **Plan**: `Free`

### 3. Set Environment Variables

In Render Dashboard → Your Service → Environment, add:

```
DB_HOST=10.28.98.145
DB_NAME=mpesa_db
DB_USER=admin
DB_PASS=admin@123

MPESA_CONSUMER_KEY=your_consumer_key
MPESA_CONSUMER_SECRET=your_consumer_secret
MPESA_BUSINESS_SHORTCODE=174379
MPESA_PASSKEY=your_passkey
```

### 4. Get Your URL

After deployment, Render provides:
- `https://mpesa-payment-app.onrender.com`

The callback URL will auto-detect, or set it in `config.php`:
```php
define('MPESA_CALLBACK_URL', 'https://mpesa-payment-app.onrender.com/callback_url.php');
```

### 5. Test

1. Visit: `https://mpesa-payment-app.onrender.com/index.php`
2. Test callback: `https://mpesa-payment-app.onrender.com/callback_url.php`
3. Make a test payment

## Files for Render

- `render.yaml` - Render configuration
- `RENDER_DEPLOYMENT.md` - Detailed deployment guide
- `db.php` - Reads from environment variables
- `config.php` - Reads from environment variables
- `stk_initiate.php` - Auto-detects Render URL

## Features

- ✅ Auto-detects Render URL for callbacks
- ✅ Reads credentials from environment variables
- ✅ Database connection with auto-creation
- ✅ Payment tracking and callbacks
- ✅ Responsive UI

## Support

See `RENDER_DEPLOYMENT.md` for detailed instructions.

