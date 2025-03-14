#!/bin/bash

# Ensure the required directories exist
mkdir -p public/css
mkdir -p public/assets/images
mkdir -p public/assets/js

# Create a simple CSS file if it doesn't exist
if [ ! -f "public/css/app.css" ]; then
    cat > public/css/app.css << 'EOF'
/* Basic styling for WebSchedulr */
body {
  font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
  background-color: #f8fafc;
  color: #334155;
  line-height: 1.5;
}

/* Dashboard */
.dashboard {
  display: flex;
}

.sidebar {
  min-width: 250px;
  max-width: 250px;
  min-height: calc(100vh - 56px);
  background: white;
  border-right: 1px solid rgba(0, 0, 0, 0.125);
}

.sidebar-heading {
  padding: 1rem;
  font-weight: 600;
  border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}

.sidebar-nav {
  padding: 0;
  margin: 0;
  list-style: none;
}

.sidebar-nav .nav-item .nav-link {
  display: block;
  padding: 0.75rem 1rem;
  color: #64748b;
  text-decoration: none;
  border-left: 3px solid transparent;
}

.sidebar-nav .nav-item .nav-link:hover {
  color: #3b82f6;
  background-color: rgba(59, 130, 246, 0.05);
}

.sidebar-nav .nav-item .nav-link.active {
  color: #3b82f6;
  border-left-color: #3b82f6;
  background-color: rgba(59, 130, 246, 0.05);
}

.sidebar-nav .nav-item .nav-link i {
  margin-right: 0.5rem;
}

.dashboard-content {
  flex: 1;
  padding: 1.5rem;
  max-width: 100%;
  overflow-x: hidden;
}

/* Utilities */
.d-flex {
  display: flex;
}

.align-items-center {
  align-items: center;
}

.ms-2 {
  margin-left: 0.5rem;
}

.me-2 {
  margin-right: 0.5rem;
}

.navbar {
  display: flex;
  justify-content: space-between;
  padding: 0.5rem 1rem;
  margin-bottom: 1rem;
  background-color: white;
  border-radius: 0.375rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.ms-auto {
  margin-left: auto;
}

.page-title {
  margin-bottom: 1.5rem;
  font-size: 1.5rem;
  font-weight: 600;
}

/* Dashboard stats */
.dashboard-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-bottom: 1.5rem;
}

.stat-card {
  padding: 1.25rem;
  border-radius: 0.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  background-color: white;
}

.stat-title {
  color: #64748b;
  font-size: 0.875rem;
  margin-bottom: 0.5rem;
}

.stat-value {
  font-size: 1.5rem;
  font-weight: 600;
  margin-bottom: 0;
}

/* Card component */
.card {
  position: relative;
  display: flex;
  flex-direction: column;
  min-width: 0;
  word-wrap: break-word;
  background-color: white;
  background-clip: border-box;
  border: 1px solid rgba(0, 0, 0, 0.125);
  border-radius: 0.5rem;
  overflow: hidden;
  margin-bottom: 1.5rem;
}

.card-header {
  padding: 1rem 1.25rem;
  margin-bottom: 0;
  background-color: rgba(0, 0, 0, 0.03);
  border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}

.card-title {
  margin-bottom: 0.5rem;
  font-weight: 600;
}

.card-body {
  flex: 1 1 auto;
  padding: 1.25rem;
}

/* Table component */
.table {
  width: 100%;
  margin-bottom: 1rem;
  color: #334155;
  border-collapse: collapse;
}

.table th,
.table td {
  padding: 0.75rem;
  vertical-align: top;
  border-top: 1px solid #e2e8f0;
}

.table thead th {
  vertical-align: bottom;
  border-bottom: 2px solid #e2e8f0;
}

/* Button component */
.btn {
  display: inline-block;
  font-weight: 500;
  text-align: center;
  white-space: nowrap;
  vertical-align: middle;
  user-select: none;
  border: 1px solid transparent;
  padding: 0.5rem 1rem;
  font-size: 0.875rem;
  line-height: 1.5;
  border-radius: 0.375rem;
  transition: all 0.15s ease-in-out;
  text-decoration: none;
  cursor: pointer;
}

.btn-primary {
  color: white;
  background-color: #3b82f6;
  border-color: #3b82f6;
}

.btn-primary:hover {
  background-color: #2563eb;
  border-color: #1d4ed8;
}

.btn-outline-primary {
  color: #3b82f6;
  border-color: #3b82f6;
  background-color: transparent;
}

.btn-outline-primary:hover {
  color: white;
  background-color: #3b82f6;
}

.btn-outline-secondary {
  color: #64748b;
  border-color: #64748b;
  background-color: transparent;
}

.btn-outline-secondary:hover {
  color: white;
  background-color: #64748b;
}

.btn-sm {
  padding: 0.25rem 0.5rem;
  font-size: 0.75rem;
}

.badge {
  display: inline-block;
  padding: 0.25em 0.4em;
  font-size: 0.75rem;
  font-weight: 600;
  line-height: 1;
  text-align: center;
  white-space: nowrap;
  vertical-align: baseline;
  border-radius: 0.25rem;
}

.bg-success {
  background-color: #22c55e;
  color: white;
}

.bg-warning {
  background-color: #f59e0b;
  color: white;
}

.bg-danger {
  background-color: #ef4444;
  color: white;
}

/* Responsive utilities */
.d-md-none {
  display: block;
}

@media (min-width: 768px) {
  .d-md-none {
    display: none;
  }
  
  .d-none.d-md-block {
    display: block;
  }
}

.col-md-8 {
  width: 100%;
}

.col-md-4 {
  width: 100%;
  margin-top: 1.5rem;
}

@media (min-width: 768px) {
  .row {
    display: flex;
    flex-wrap: wrap;
    margin-right: -0.75rem;
    margin-left: -0.75rem;
  }
  
  .col-md-8 {
    width: 66.666667%;
    padding-right: 0.75rem;
    padding-left: 0.75rem;
  }
  
  .col-md-4 {
    width: 33.333333%;
    padding-right: 0.75rem;
    padding-left: 0.75rem;
    margin-top: 0;
  }
}

.mt-4 {
  margin-top: 1.5rem;
}

.mb-4 {
  margin-bottom: 1.5rem;
}

.mb-0 {
  margin-bottom: 0;
}

.justify-content-between {
  justify-content: space-between;
}

.rounded-circle {
  border-radius: 50%;
}
EOF
fi

# Check if placeholder images exist, if not create them
if [ ! -f "public/assets/images/logo-placeholder.png" ]; then
    # Create a simple text file to indicate where the image should be
    echo "Logo placeholder - replace with actual logo" > public/assets/images/logo-placeholder.png
fi

if [ ! -f "public/assets/images/avatar-placeholder.png" ]; then
    # Create a simple text file to indicate where the image should be
    echo "Avatar placeholder - replace with actual avatar" > public/assets/images/avatar-placeholder.png
fi

# Copy CSS to assets directory for JavaScript imports
cp public/css/app.css public/assets/css/app.css

echo "Assets updated successfully!"