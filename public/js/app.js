// Toggle the side navigation
document.addEventListener('DOMContentLoaded', function() {
    // Toggle sidebar
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('accordionSidebar');
    const contentWrapper = document.getElementById('content-wrapper');
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            document.body.classList.toggle('sidebar-toggled');
            sidebar.classList.toggle('toggled');
            
            if (sidebar.classList.contains('toggled')) {
                document.querySelector('#sidebarToggle i').classList.remove('bi-chevron-left');
                document.querySelector('#sidebarToggle i').classList.add('bi-chevron-right');
            } else {
                document.querySelector('#sidebarToggle i').classList.remove('bi-chevron-right');
                document.querySelector('#sidebarToggle i').classList.add('bi-chevron-left');
            }
        });
    }
    
    // Mobile sidebar toggle
    const sidebarToggleTop = document.getElementById('sidebarToggleTop');
    
    if (sidebarToggleTop) {
        sidebarToggleTop.addEventListener('click', function(e) {
            e.preventDefault();
            document.body.classList.toggle('sidebar-toggled');
            sidebar.classList.toggle('toggled');
        });
    }
    
    // Close any open menu dropdowns when window is resized below 768px
    window.addEventListener('resize', function() {
        if (window.innerWidth < 768) {
            document.body.classList.add('sidebar-toggled');
            sidebar.classList.add('toggled');
        }
    });
    
    // Prevent the content wrapper from scrolling when the fixed side navigation hovered over
    document.addEventListener('scroll', function() {
        const scrollDistance = window.pageYOffset;
        
        // Scroll to top button appears when scrolling down
        const scrollToTop = document.querySelector('.scroll-to-top');
        
        if (scrollToTop && scrollDistance > 100) {
            scrollToTop.style.display = 'block';
        } else if (scrollToTop) {
            scrollToTop.style.display = 'none';
        }
    });
    
    // Smooth scrolling using jQuery easing
    const scrollToTopButton = document.querySelector('a.scroll-to-top');
    
    if (scrollToTopButton) {
        scrollToTopButton.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
    
    // Initialize Bootstrap tooltips and popovers
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    });
    
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
    const popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
      return new bootstrap.Popover(popoverTriggerEl)
    });
    
    // Initialize dropdowns
    const dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'))
    const dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
      return new bootstrap.Dropdown(dropdownToggleEl)
    });
});