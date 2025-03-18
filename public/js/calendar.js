// Add this script to handle quick status updates
document.addEventListener('DOMContentLoaded', function() {
    // Status update links
    const statusLinks = document.querySelectorAll('.status-update');
    statusLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const appointmentId = this.dataset.id;
            const newStatus = this.dataset.status;
            
            if (!appointmentId || !newStatus) {
                console.error('Missing appointment ID or status');
                return;
            }
            
            // Send AJAX request
            fetch('/calendar/update-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id: appointmentId,
                    status: newStatus
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Refresh the page to show updated status
                    window.location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to update status'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating appointment status.');
            });
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    // Status update handling from previous implementation
    
    // Initialize tooltips for appointment details
    const appointmentElements = document.querySelectorAll('.appointment');
    
    if (appointmentElements.length > 0) {
        appointmentElements.forEach(appointment => {
            // Use Bootstrap tooltips (if Bootstrap 5 is used)
            if (typeof bootstrap !== 'undefined') {
                const tooltip = new bootstrap.Tooltip(appointment, {
                    html: true,
                    title: appointment.getAttribute('data-tooltip-content') || 'No details available',
                    placement: 'auto',
                    boundary: 'window'
                });
            }
            
            // Add hover effect
            appointment.addEventListener('mouseenter', function() {
                this.classList.add('appointment-hover');
            });
            
            appointment.addEventListener('mouseleave', function() {
                this.classList.remove('appointment-hover');
            });
        });
    }
});

/**
 * Calendar functionality for WebSchedulr
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('Calendar.js loaded');
    
    // Handle status update links
    const statusLinks = document.querySelectorAll('.status-update');
    if (statusLinks.length > 0) {
        console.log('Found ' + statusLinks.length + ' status update links');
        
        statusLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                const appointmentId = this.dataset.id;
                const newStatus = this.dataset.status;
                
                if (!appointmentId || !newStatus) {
                    console.error('Missing appointment ID or status');
                    return;
                }
                
                console.log(`Updating appointment ${appointmentId} status to ${newStatus}`);
                
                // Send AJAX request
                fetch('/calendar/update-status', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        id: appointmentId,
                        status: newStatus
                    })
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error('Server returned ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Server response:', data);
                    if (data.success) {
                        // Update UI without refresh
                        const statusButton = document.getElementById('statusDropdown' + appointmentId);
                        if (statusButton) {
                            // Update button text
                            statusButton.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
                            
                            // Update button class
                            statusButton.className = statusButton.className.replace(/btn-\w+/, '');
                            let badgeClass = 'warning';
                            if (newStatus === 'confirmed') badgeClass = 'success';
                            if (newStatus === 'completed') badgeClass = 'primary';
                            if (newStatus === 'cancelled') badgeClass = 'danger';
                            statusButton.classList.add('btn-' + badgeClass);
                            
                            // Show success toast or message
                            alert('Status updated to ' + newStatus);
                        } else {
                            // Fallback to page refresh if button not found
                            window.location.reload();
                        }
                    } else {
                        alert('Error: ' + (data.message || 'Failed to update status'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error updating appointment status. Please try again.');
                });
            });
        });
    }
    
    // Additional calendar functionality can be added here
});