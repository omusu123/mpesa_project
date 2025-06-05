# Daraja 2.0 C2B Integration 🚀

A simple and functional implementation of Safaricom's Daraja 2.0 STK Push API using PHP and Bootstrap.

## 🌟 Features

- Collects mobile number and amount via a web form
- Sends STK Push requests to M-Pesa using the Daraja API
- Logs callback data for transaction confirmation
- Fully responsive UI

## 🛠 Setup Instructions

1. Clone or download this repository.
2. Open `stk_initiate.php` and update these credentials:
   - `$consumerKey`
   - `$consumerSecret`
   - `$BusinessShortCode`
   - `$Passkey`
   - `$CallBackURL`
3. Host your project on a server (use localhost, Render, Heroku, etc.).
4. Visit `index.php` in your browser.
5. Enter phone and amount, then check your device for an M-Pesa prompt.

## 📂 File Structure

- `index.php` – Frontend form UI
- `stk_initiate.php` – Handles form submission and sends STK Push
- `callback_url.php` – Receives and logs M-Pesa transaction confirmation
- `M_PESAConfirmationResponse.txt` – Stores raw callback logs

## 👨‍💻 Author

**Ernest Charles**

---

> ⚠️ This implementation uses the M-Pesa **sandbox environment**. To use it in production, switch URLs and update your credentials.
