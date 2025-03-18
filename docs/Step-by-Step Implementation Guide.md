# WebSchedulr Modernization: Step-by-Step Implementation Guide


## Step 1: Create Development Branch

```bash
# Clone the repository if you haven't already
git clone https://github.com/niloc95/webschedulr.git
cd webschedulr

# Create and switch to a new branch for modernization
git checkout -b modernization
```

## Step 2: Install Core Dependencies

### 2.1: Set up Composer

```bash
# Install Composer if not already installed
# On Windows: https://getcomposer.org/Composer-Setup.exe
# On Mac/Linux:
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

# Initialize Composer in your project
composer init --name niloc95/webschedulr --description "Online Appointment Scheduler" --type project --license GPL-3.0

# Add required PHP dependencies
composer require vlucas/phpdotenv monolog/monolog
composer require --dev phpunit/phpunit friendsofphp/php-cs-fixer
```

### 2.2: Set up NPM and frontend tools

```bash
# Install Node.js if not already installed
# https://nodejs.org/

# Initialize npm in your project
npm init -y

# Install Vite and other frontend dependencies
npm install --save-dev vite sass
npm install bootstrap @popperjs/core flatpickr chart.js axios
```

## Step 3: Create Project Structure

```bash
# Create essential directories
mkdir -p application/controllers
mkdir -p application/models
mkdir -p application/services
mkdir -p application/views/components
mkdir -p application/views/layouts
mkdir -p src/js
mkdir -p src/scss/components
mkdir -p src/scss/layouts
mkdir -p src/scss/themes
mkdir -p public
mkdir -p logs
mkdir -p assets
```

## Step 4: Create Configuration Files

### 4.1: Create .gitignore file

```bash
cat > .gitignore << 'EOF'
# Environment variables
.env

# Composer packages
/vendor/

# Node modules
/node_modules/

# Built assets
/assets/

# Logs
/logs/

# IDE specific files
.idea/
.vscode/
*.sublime-*

# OS specific files
.DS_Store
Thumbs.db
EOF
```

### 4.2: Create .env.example file

```bash
cat > .env.example << 'EOF'
# General Settings
BASE_URL=http://localhost
LANGUAGE=english
DEBUG_MODE=false

# Database Settings
DB_HOST=mysql
DB_NAME=@webSchedulr
DB_USERNAME=user
DB_PASSWORD=password

# Google Calendar Sync
GOOGLE_SYNC_FEATURE=false
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=

# Payment Gateway - PayFast
PAYMENT_GATEWAY_ACTIVE=false
PAYFAST_MERCHANT_ID=
PAYFAST_MERCHANT_KEY=
PAYFAST_PASSPHRASE=
PAYFAST_TEST_MODE=true
EOF

# Create actual .env file from example
cp .env.example .env
```

### 4.3: Create bootstrap.php

```bash
cat > bootstrap.php << 'EOF'
<?php
// Load Composer autoloader if available, otherwise use traditional includes
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
    
    // Load environment variables from .env file
    if (class_exists('Dotenv\Dotenv') && file_exists(__DIR__ . '/.env')) {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->load();
    }
}
EOF
```

## Step 5: Set up Frontend Build Tools

### 5.1: Create Vite configuration file

```bash
cat > vite.config.js << 'EOF'
import { defineConfig } from 'vite';
import { resolve } from 'path';

export default defineConfig({
  build: {
    outDir: 'assets',
    assetsDir: '',
    rollupOptions: {
      input: {
        main: resolve(__dirname, 'src/js/main.js'),
        admin: resolve(__dirname, 'src/js/admin.js'),
      },
      output: {
        entryFileNames: 'js/[name].js',
        chunkFileNames: 'js/[name]-[hash].js',
      },
    },
  },
  resolve: {
    alias: {
      '@': resolve(__dirname, 'src')
    }
  }
});
EOF
```

## Step 6: Create Payment Service

### PaymentService.php

```bash
cat > application/services/PaymentService.php << 'EOF'
<?php
namespace WebSchedulr\Services;

class PaymentService {
    private $merchantId;
    private $merchantKey;
    private $passphrase;
    private $testMode;
    
    public function __construct() {
        $this->merchantId = Config::PAYFAST_MERCHANT_ID ?? '';
        $this->merchantKey = Config::PAYFAST_MERCHANT_KEY ?? '';
        $this->passphrase = Config::PAYFAST_PASSPHRASE ?? '';
        $this->testMode = Config::PAYFAST_TEST_MODE ?? true;
    }
    
    public function generatePaymentForm($appointmentData) {
        if (!(Config::PAYMENT_GATEWAY_ACTIVE ?? false)) {
            return null;
        }
        
        $data = [
            'merchant_id' => $this->merchantId,
            'merchant_key' => $this->merchantKey,
            'amount' => number_format($appointmentData['price'], 2, '.', ''),
        ];
        
        return [
            'data' => $data,
            'url' => $this->testMode ? 'https://sandbox.payfast.co.za/eng/process' : 'https://www.payfast.co.za/eng/process'
        ];
    }
}
EOF
```

## Step 7: Update Composer Autoload Configuration

### application/autoload.php

```bash
cat > application/autoload.php << 'EOF'
<?php
spl_autoload_register(function ($class) {
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    
    $prefixes = [
        'Controller_' => 'application/controllers/',
        'Model_' => 'application/models/',
    ];
    
    foreach ($prefixes as $prefix => $dir) {
        if (strpos($class, $prefix) === 0) {
            $class = substr($class, strlen($prefix));
            $file = $dir . $class . '.php';
            
            if (file_exists($file)) {
                require $file;
                return true;
            }
        }
    }
    
    return false;
});
EOF
```

webschedulr/
├── application/               # Application source code
│   ├── controllers/           # Controllers
│   ├── models/                # Data models
│   ├── services/              # Business logic services
│   │   └── PaymentService.php # PayFast payment integration
│   ├── views/                 # View templates
│   │   ├── components/        # Reusable UI components
│   │   ├── dashboard/         # Dashboard views
│   │   │   └── index.php      # Dashboard main view
│   │   └── layouts/           # Layout templates
│   │       └── dashboard.php  # Main application layout
│   ├── helpers/               # Helper functions
│   │   └── general_helper.php # General utility functions
│   ├── router.php             # Simple routing system
│   └── autoload.php           # Custom autoloader for legacy code
├── public/                    # Publicly accessible files
│   ├── css/                   # CSS files
│   │   └── app.css            # Main stylesheet
│   ├── assets/                # Compiled/optimized assets
│   │   └── images/            # Image assets
│   ├── index.php              # Application entry point
│   └── .htaccess              # Apache URL rewriting rules
├── src/                       # Frontend source files
│   ├── js/                    # JavaScript source
│   │   ├── app.js             # Main JavaScript file
│   │   └── components/        # JS components
│   └── scss/                  # SCSS source files
│       ├── components/        # SCSS components
│       └── layouts/           # Layout-specific styles
├── vendor/                    # Composer dependencies
├── logs/                      # Application logs
├── bootstrap.php              # Application bootstrap
├── config.php                 # Configuration file
├── .env.example               # Environment variables example
├── .env                       # Environment variables (not in git)
├── .gitignore                 # Git ignore file
├── composer.json              # Composer configuration
├── package.json               # NPM configuration
└── vite.config.js             # Vite configuration