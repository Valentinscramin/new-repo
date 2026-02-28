# TechCompare - Quick Start Guide

## 🚀 Getting Started

Follow these simple steps to get TechCompare running locally.

### Step 1: Start XAMPP Services

1. Open **XAMPP Control Panel**
2. Click **Start** on:
   - ✅ Apache (for web server)
   - ✅ MySQL (for database)

### Step 2: Setup Database

Open a terminal in the `backend` folder and run:

```bash
# Create the database
php bin/console doctrine:database:create

# Run migrations to create tables
php bin/console doctrine:migrations:migrate
```

When prompted, type `yes` to execute the migration.

### Step 3: Configure OpenAI API

1. Get your API key from [OpenAI Platform](https://platform.openai.com/api-keys)
2. Open `backend/.env` file
3. Replace `your_openai_api_key_here` with your actual key:
   ```
   OPENAI_API_KEY=sk-proj-xxxxxxxxxxxxx
   ```

### Step 4: Start Backend Worker

Open a new terminal in the `backend` folder:

```bash
php bin/console messenger:consume async -vv
```

⚠️ **Keep this terminal running** - it processes comparisons in the background.

### Step 5: Start Frontend

Open a terminal in the `frontend` folder:

```bash
npm run dev
```

### Step 6: Access the Application

Open your browser and go to:
- **Frontend**: http://localhost:5173
- **Backend API**: http://localhost/product_comparison/backend/public/index.php/api
- **phpMyAdmin**: http://localhost/phpmyadmin (optional, to view database)

## 🎯 First Use

1. Click **"Começar Agora"** or **"Registrar"**
2. Create an account with email and password
3. You'll be automatically logged in and redirected to the dashboard
4. Click **"Nova Comparação"** in the homepage
5. Paste 3 product URLs (e.g., from Amazon, Newegg, etc.)
6. Click **"Comparar Agora"**
7. Wait while the AI analyzes the products (this takes 30-60 seconds)
8. View the comparison results with winner highlighted!

## 📝 Example Product URLs

You can test with any e-commerce product URLs, for example:
- Gaming laptops from Amazon
- Monitors from Newegg  
- Keyboards from Best Buy
- Any tech product page with specs visible in the HTML

## ⚠️ Troubleshooting

### "Connection refused" error
- ✅ Make sure MySQL is running in XAMPP

### "Database does not exist"
- ✅ Run: `php bin/console doctrine:database:create`

### Comparison stuck in "processing"
- ✅ Check if the worker is running: `php bin/console messenger:consume async`
- ✅ Check backend logs: `backend/var/log/`

### Frontend can't connect to backend
- ✅ Make sure Apache is running in XAMPP
- ✅ Test API: http://localhost/product_comparison/backend/public/index.php/api

### OpenAI errors
- ✅ Verify your API key is correct in `.env`
- ✅ Check you have credits in your OpenAI account

## 🎨 Features to Try

- ✅ Create multiple comparisons
- ✅ View comparison history in Dashboard
- ✅ See real-time processing status
- ✅ Compare products from different stores
- ✅ View detailed specs side-by-side
- ✅ See AI-generated pros/cons for each product
- ✅ Auto-calculated winner based on cost-benefit score

## 🛠️ Development Notes

### Backend runs on:
- PHP Built-in server or XAMPP Apache
- Port: 80 (Apache default)
- URL: http://localhost/product_comparison/backend/public

### Frontend runs on:
- Vite dev server
- Port: 5173 (default)
- URL: http://localhost:5173

### Database:
- MySQL/MariaDB via XAMPP
- Database name: `techcompare`
- Default credentials: root / (no password)

## 📚 Next Steps

Check the main [README.md](README.md) for:
- Complete API documentation
- Architecture details
- Advanced configuration
- Production deployment tips

---

**Enjoy comparing! 🎮💻🌍**
