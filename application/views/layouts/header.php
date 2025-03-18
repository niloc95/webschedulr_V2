<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard' ?> - WebSchedulr</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <!-- DataTables -->
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">
    
    <!-- Custom styles -->
    <link href="/css/app.css" rel="stylesheet">

    <!-- Page-specific styles -->
    <?php if (in_array($active, ['calendar', 'appointment'])): ?>
        <link href="/css/calendar.css" rel="stylesheet">
        <link href="/css/appointment.css" rel="stylesheet">
    <?php endif; ?>
</head>
<body id="page-top">
    <!-- Rest of your header content -->
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="/">
                <div class="logo-container">
                    <img src="/assets/images/logo_black.png" alt="WebSchedulr" class="app-logo sidebar-logo">
                </div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item <?= ($active ?? '') === 'dashboard' ? 'active' : '' ?>">
                <a class="nav-link" href="/dashboard">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Scheduling
            </div>

            <!-- Nav Item - Calendar -->
            <li class="nav-item <?= ($active ?? '') === 'calendar' ? 'active' : '' ?>">
                <a class="nav-link" href="/calendar">
                    <i class="bi bi-calendar3"></i>
                    <span>Calendar</span>
                </a>
            </li>

            <!-- Nav Item - Appointments -->
            <li class="nav-item <?= $active == 'appointments' ? 'active' : '' ?>">
                <a class="nav-link" href="/appointments">
                <i class="bi bi-calendar-check"></i>
                <span>Appointments</span>
                </a>
            </li>

            <!-- Nav Item - Clients -->
            <li class="nav-item <?= ($active ?? '') === 'clients' ? 'active' : '' ?>">
                <a class="nav-link" href="/clients">
                    <i class="bi bi-people"></i>
                    <span>Clients</span>
                </a>
            </li>

            <!-- Nav Item - Services -->
            <li class="nav-item <?= ($active ?? '') === 'services' ? 'active' : '' ?>">
                <a class="nav-link" href="/services">
                    <i class="bi bi-tag"></i>
                    <span>Services</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Settings
            </div>

            <!-- Nav Item - Account -->
            <li class="nav-item <?= ($active ?? '') === 'account' ? 'active' : '' ?>">
                <a class="nav-link" href="/account">
                    <i class="bi bi-person-circle"></i>
                    <span>My Account</span>
                </a>
            </li>

            <!-- Nav Item - Settings -->
            <li class="nav-item <?= ($active ?? '') === 'settings' ? 'active' : '' ?>">
                <a class="nav-link" href="/settings">
                    <i class="bi bi-gear"></i>
                    <span>Settings</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle">
                    <i class="bi bi-chevron-left"></i>
                </button>
            </div>
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="bi bi-list"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    <?= $_SESSION['user']['name'] ?? 'User' ?>
                                </span>
                                <i class="bi bi-person-circle"></i>
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="/account">
                                    <i class="bi bi-person fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <a class="dropdown-item" href="/settings">
                                    <i class="bi bi-gear fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Settings
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="/logout">
                                    <i class="bi bi-box-arrow-right fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>
                        <li class="nav-item">
                            <button id="themeToggle" class="btn btn-link nav-link">
                                <i class="bi bi-moon" id="themeIcon"></i>
                            </button>
                        </li>
                    </ul>
                </nav>
                <!-- End of Topbar -->

                <script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Checking if calendar.css is loaded...');
    const styles = document.styleSheets;
    let found = false;
    
    for (let i = 0; i < styles.length; i++) {
        try {
            const href = styles[i].href;
            if (href && href.includes('calendar.css')) {
                console.log('✅ calendar.css is loaded!');
                found = true;
                break;
            }
        } catch (e) {
            // Some styles may be cross-origin and cause errors
        }
    }
    
    if (!found) {
        console.error('❌ calendar.css was not loaded!');
    }
});
</script>