# Rishtewale
PHP/MySQL mobile-first matrimonial site.

## Setup
1. Create MySQL database and import `config/schema.sql`, then `seed.sql` for sample profiles.
2. Edit `config/config.php` DB credentials.
3. Upload the whole folder to PHP hosting.
4. Admin: `/admin/login.php` — initial password `7149`.
5. In Admin → Settings, set UPI ID, QR, premium prices, Telegram Bot Token and Telegram Chat ID.

## Telegram
The bot is notified when a premium UTR is submitted and when a female video-calling earning application is submitted. No bot credentials are included in the package; configure them from Admin Settings.

## Notes
Sample profiles are explicitly demo/sample data. Replace them with real approved profiles before launch. The UPI link uses the standard Android UPI intent; whether a specific app opens depends on the device/app installation.
