<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard' ?> - WebSchedulr</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- Direct link to CSS - absolute path -->
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
    <div class="dashboard">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-heading">
                <div class="d-flex align-items-center">
                    <img src="/assets/images/logo-placeholder.png" alt="WebSchedulr" height="30" onerror="this.src='data:image/png;base64,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'">
                    <span class="ms-2">WebSchedulr</span>
                </div>
            </div>
            <ul class="sidebar-nav">
                <li class="nav-item">
                    <a href="<?= site_url('dashboard') ?>" class="nav-link <?= ($active_menu ?? '') === 'dashboard' ? 'active' : '' ?>">
                        <i class="bi bi-house"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= site_url('appointments') ?>" class="nav-link <?= ($active_menu ?? '') === 'appointments' ? 'active' : '' ?>">
                        <i class="bi bi-calendar-check"></i> Appointments
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= site_url('schedules') ?>" class="nav-link <?= ($active_menu ?? '') === 'schedules' ? 'active' : '' ?>">
                        <i class="bi bi-calendar-week"></i> Schedules
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= site_url('clients') ?>" class="nav-link <?= ($active_menu ?? '') === 'clients' ? 'active' : '' ?>">
                        <i class="bi bi-people"></i> Clients
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= site_url('payments') ?>" class="nav-link <?= ($active_menu ?? '') === 'payments' ? 'active' : '' ?>">
                        <i class="bi bi-credit-card"></i> Payments
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= site_url('settings') ?>" class="nav-link <?= ($active_menu ?? '') === 'settings' ? 'active' : '' ?>">
                        <i class="bi bi-gear"></i> Settings
                    </a>
                </li>
            </ul>
        </div>

        <!-- Content -->
        <div class="dashboard-content">
            <!-- Top navigation -->
            <div class="navbar">
                <button id="sidebar-toggle" class="d-md-none btn btn-outline-secondary">
                    <i class="bi bi-list"></i>
                </button>
                
                <div class="ms-auto d-flex align-items-center">
                    <!-- Notifications dropdown -->
                    <div class="dropdown me-3">
                        <a href="#" class="dropdown-toggle" role="button">
                            <i class="bi bi-bell"></i>
                            <span class="badge bg-danger">2</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <div class="dropdown-header">Notifications</div>
                            <a href="#" class="dropdown-item">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="bi bi-calendar-check"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <p class="mb-0">New appointment request</p>
                                        <small class="text-muted">5 minutes ago</small>
                                    </div>
                                </div>
                            </a>
                            <a href="#" class="dropdown-item">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="bi bi-credit-card"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <p class="mb-0">Payment received</p>
                                        <small class="text-muted">1 hour ago</small>
                                    </div>
                                </div>
                            </a>
                            <div class="dropdown-footer">
                                <a href="#">View all notifications</a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- User dropdown -->
                    <div class="dropdown">
                        <a href="#" class="dropdown-toggle d-flex align-items-center" role="button">
                            <div class="avatar me-2">
                                <img src="/assets/images/avatar-placeholder.png" alt="User" class="rounded-circle" width="32" height="32" onerror="this.src='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAACXBIWXMAAAsTAAALEwEAmpwYAAADSklEQVR4nO2WTUhcVxTH//e+mfHS+JXEMROjJBZN8CN+oEVXpS1+YFwYSB1duGihCzfiRjBtF7pwJVK6cltdFJEQsAgJuDAQAlKIUIgLQWhh0IRIJR9mkkni6Lz37umiM8kkM/Pmvdfahi78w+Wec+7539/ce84dwhZ2F0bqQMiZvgdC3g9F6Iz0cDnmTPTMtM7+t56COS/mizDDLINZaRHRsB57VgBECeKYZuZk6N8xJDL1APQBdJLw5Z4WWvlPAUSkU+LfPSCaUqu+7GVR7l9pGxDl08oQyJ5ir+MxXNe9GI/HT2iaNtbV1fU+Ef0iIieZW0i1TQh7AUT