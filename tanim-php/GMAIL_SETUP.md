# Gmail SMTP Setup for Email Verification

## Step 1: Enable 2-Factor Authentication
1. Go to your Google Account: https://myaccount.google.com/
2. Click on "Security"
3. Enable "2-Step Verification"

## Step 2: Generate App Password
1. Go to: https://myaccount.google.com/apppasswords
2. Select "Mail" for the app
3. Select "Other (Custom name)" and enter "Tanim Laravel"
4. Click "Generate"
5. Copy the 16-character password (this is what you'll use in .env)

## Step 3: Update .env file
Add these lines to your `.env` file:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-gmail-address@gmail.com
MAIL_PASSWORD=your-16-character-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-gmail-address@gmail.com
MAIL_FROM_NAME="Tanim - Agricultural Supply Chain"
```

## Step 4: Clear Cache
After updating .env, run these commands:

```bash
php artisan config:cache
php artisan config:clear
php artisan cache:clear
```

## Step 5: Test
1. Register a new user with your Gmail address
2. You should receive a verification email
3. Click the verification link in the email

## Troubleshooting
- If you get "Authentication failed", double-check your app password
- Make sure 2-factor authentication is enabled
- Use the 16-character app password, NOT your regular Gmail password
- Check spam/junk folder if email doesn't arrive
