#!/bin/bash

# Build frontend assets with Vite
echo "Building frontend assets..."
npm run build

# Create required directories if they don't exist
mkdir -p public/assets/css
mkdir -p public/assets/js

# If Vite build failed, use temporary CSS
if [ ! -f "public/assets/css/app.css" ]; then
    echo "Using fallback CSS..."
    cp public/css/app.css public/assets/css/app.css
fi

echo "Build completed."
