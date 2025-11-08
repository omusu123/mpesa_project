# Deploy M-Pesa Payment App to Render

This guide will help you deploy your M-Pesa payment application to Render for free.

## Prerequisites

1. **Render Account**: Sign up at https://render.com (free tier available)
2. **GitHub Account**: Your code should be in a GitHub repository
3. **Database**: MySQL database (you can use your existing one at 10.28.98.145)

## Step 1: Prepare Your Repository

1. **Push your code to GitHub**
   ```bash
   git init
   git add .
   git commit -m "Initial commit - M-Pesa Payment App"
   git branch -M main
   git remote add origin https://github.com/your-username/your-repo.git
   git push -u origin main
   ```

2. **Create a `.gitignore` file** (if you don't have one):
   ```
   # Environment files
   .env
   config.local.php
   
   # Logs
   *.log
   M_PESAConfirmationResponse.txt
   
   # IDE
   .vscode/
   .idea/
   *.swp
   *.swo
   
   # OS
   .DS_Store
   Thumbs.db
   ```

## Step 2: Create Render Web Service

1. **Log in to Render Dashboard**
   - Go to https://dashboard.render.com
   - Click "New +" → "Web Service"

2. **Connect Repository**
   - Connect your GitHub account
   - Select your repository
   - Click "Connect"

3. **Configure Service**
   - **Name**: `mpesa-payment-app` (or any name you prefer)
   - **Environment**: `PHP`
   - **Region**: Choose closest to you (e.g., `Singapore` or `Frankfurt`)
   - **Branch**: `main` (or your default branch)
   - **Root Directory**: Leave empty (or specify if your PHP files are in a subdirectory)
   - **Build Command**: Leave empty (PHP doesn't need building)
   - **Start Command**: `php -S 0.0.0.0:$PORT`
   - **Plan**: `Free` (or upgrade if needed)

4. **Environment Variables**
   Click "Advanced" → "Add Environment Variable" and add:
   
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

5. **Click "Create Web Service"**

## Step 3: Update Database Configuration

1. **Create `db.php` that reads from environment variables**:
   ```php
   <?php
   // Read from environment variables (Render) or use defaults
   $host = $_ENV['DB_HOST'] ?? '10.28.98.145';
   $dbname = $_ENV['DB_NAME'] ?? 'mpesa_db';
   $username = $_ENV['DB_USER'] ?? 'admin';
   $password = $_ENV['DB_PASS'] ?? 'admin@123';
   
   // ... rest of the connection code
   ```

2. **Update `config.php` to read from environment variables**:
   ```php
   // M-Pesa API Credentials from environment or defaults
   define('MPESA_CONSUMER_KEY', $_ENV['MPESA_CONSUMER_KEY'] ?? 'your_key');
   define('MPESA_CONSUMER_SECRET', $_ENV['MPESA_CONSUMER_SECRET'] ?? 'your_secret');
   // ... etc
   ```

## Step 4: Get Your Render URL

1. **After deployment**, Render will provide a URL like:
   - `https://mpesa-payment-app.onrender.com`

2. **Update callback URL**:
   - The app will auto-detect the URL from Render environment
   - Or manually set in `config.php`:
     ```php
     define('MPESA_CALLBACK_URL', 'https://mpesa-payment-app.onrender.com/callback_url.php');
     ```

## Step 5: Test Your Deployment

1. **Visit your app**:
   - `https://mpesa-payment-app.onrender.com/index.php`

2. **Test callback URL**:
   - Visit: `https://mpesa-payment-app.onrender.com/callback_url.php`
   - Should see a JSON response (even if it's an error, that means it's accessible)

3. **Test STK Push**:
   - Use the payment form
   - Enter phone number and amount
   - Check your phone for M-Pesa prompt

## Step 6: Configure Database Access (If Needed)

If your database at `10.28.98.145` doesn't allow external connections:

1. **Allow Render IPs** (if possible):
   - Render uses dynamic IPs, so you may need to allow all IPs
   - Or use a Render database service instead

2. **Alternative: Use Render PostgreSQL** (free tier):
   - Create a PostgreSQL database in Render
   - Update your database schema
   - Update connection settings

## Environment Variables Reference

Set these in Render Dashboard → Your Service → Environment:

| Variable | Description | Example |
|----------|-------------|---------|
| `DB_HOST` | Database host | `10.28.98.145` |
| `DB_NAME` | Database name | `mpesa_db` |
| `DB_USER` | Database user | `admin` |
| `DB_PASS` | Database password | `admin@123` |
| `MPESA_CONSUMER_KEY` | M-Pesa consumer key | `your_key` |
| `MPESA_CONSUMER_SECRET` | M-Pesa consumer secret | `your_secret` |
| `MPESA_BUSINESS_SHORTCODE` | Business shortcode | `174379` |
| `MPESA_PASSKEY` | M-Pesa passkey | `your_passkey` |

## Troubleshooting

### Deployment Fails
- Check build logs in Render dashboard
- Ensure `startCommand` is correct: `php -S 0.0.0.0:$PORT`
- Verify all PHP files are in the repository

### Database Connection Fails
- Verify database allows connections from Render IPs
- Check environment variables are set correctly
- Test database connection from Render shell

### Callback URL Not Working
- Verify the URL is publicly accessible
- Check that `callback_url.php` exists and is accessible
- Review Render logs for errors

### App Goes to Sleep (Free Tier)
- Free tier apps sleep after 15 minutes of inactivity
- First request after sleep may be slow (cold start)
- Upgrade to paid plan to avoid sleeping

## Next Steps

1. ✅ Deploy to Render
2. ✅ Get your public URL
3. ✅ Update callback URL in config.php
4. ✅ Test STK push
5. ✅ Monitor logs and transactions

## Support

- Render Docs: https://render.com/docs
- Render Support: https://render.com/support
- M-Pesa Daraja Docs: https://developer.safaricom.co.ke

