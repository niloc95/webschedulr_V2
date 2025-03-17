document.addEventListener('DOMContentLoaded', function() {
    const saveClientBtn = document.getElementById('saveClientBtn');
    const clientSelect = document.getElementById('client_id');
    const newClientModal = document.getElementById('newClientModal');
    
    if (!saveClientBtn || !clientSelect) {
        console.error('Required elements not found on page');
        return;
    }
    
    // Handle form submission
    saveClientBtn.addEventListener('click', function(event) {
        // Prevent any default action
        event.preventDefault();
        
        // Get form inputs
        const clientNameInput = document.getElementById('client_name');
        const clientEmailInput = document.getElementById('client_email');
        const clientPhoneInput = document.getElementById('client_phone');
        const clientNotesInput = document.getElementById('client_notes');
        
        if (!clientNameInput) {
            console.error('Client name input not found');
            return;
        }
        
        const clientName = clientNameInput.value.trim();
        if (!clientName) {
            alert('Client name is required');
            clientNameInput.focus();
            return;
        }
        
        // Create data object for AJAX
        const clientData = {
            name: clientName,
            email: clientEmailInput ? clientEmailInput.value.trim() : '',
            phone: clientPhoneInput ? clientPhoneInput.value.trim() : '',
            notes: clientNotesInput ? clientNotesInput.value.trim() : ''
        };
        
        console.log('Sending client data:', clientData);
        
        // Send AJAX request
        fetch('/clients/ajax-create', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(clientData)
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
                // Add new client to dropdown
                const option = new Option(data.client.name, data.client.id, true, true);
                clientSelect.add(option);
                
                // Close modal
                if (newClientModal) {
                    const bsModal = bootstrap.Modal.getInstance(newClientModal);
                    if (bsModal) {
                        bsModal.hide();
                    }
                }
                
                // Reset form inputs
                if (clientNameInput) clientNameInput.value = '';
                if (clientEmailInput) clientEmailInput.value = '';
                if (clientPhoneInput) clientPhoneInput.value = '';
                if (clientNotesInput) clientNotesInput.value = '';
                
                // Success message
                alert('Client added successfully!');
            } else {
                alert('Error: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error during AJAX request:', error);
            alert('Error adding client. Please try again.');
        });
    });
});

// Add this to your existing appointment-form.js (after the client creation code)
document.addEventListener('DOMContentLoaded', function() {
    // Existing client code remains...
    
    // Service creation functionality
    const saveServiceBtn = document.getElementById('saveServiceBtn');
    const serviceSelect = document.getElementById('service_id');
    const newServiceModal = document.getElementById('newServiceModal');
    
    if (saveServiceBtn && serviceSelect) {
        saveServiceBtn.addEventListener('click', function(event) {
            event.preventDefault();
            
            const serviceName = document.getElementById('service_name').value.trim();
            const serviceDuration = document.getElementById('service_duration').value;
            const servicePrice = document.getElementById('service_price').value;
            const serviceColor = document.getElementById('service_color').value;
            
            if (!serviceName) {
                alert('Service name is required');
                return;
            }
            
            if (!serviceDuration || serviceDuration < 5) {
                alert('Duration must be at least 5 minutes');
                return;
            }
            
            const serviceData = {
                name: serviceName,
                duration: serviceDuration,
                price: servicePrice || 0,
                color: serviceColor || '#3498db'
            };
            
            console.log('Sending service data:', serviceData);
            
            // AJAX call to create service
            fetch('/services/ajax-create', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(serviceData)
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
                    // Add new service to dropdown
                    const option = new Option(
                        `${data.service.name} (${data.service.duration} min)`, 
                        data.service.id, 
                        true, 
                        true
                    );
                    serviceSelect.add(option);
                    
                    // Close modal using Bootstrap 5 method
                    const bsModal = bootstrap.Modal.getInstance(newServiceModal);
                    if (bsModal) {
                        bsModal.hide();
                    }
                    
                    // Reset form
                    document.getElementById('newServiceForm').reset();
                    document.getElementById('service_color').value = '#3498db';
                    
                    alert('Service added successfully!');
                } else {
                    alert('Error: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error adding service. Please try again.');
            });
        });
    }
});