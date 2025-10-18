// Simple JavaScript for basic interactions
document.addEventListener('DOMContentLoaded', function() {
    // Add any basic JavaScript functionality here
    console.log('Veterinary Clinic Management System loaded');
    
    // Simple form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('border-red-500');
                } else {
                    field.classList.remove('border-red-500');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    });
    
    // Auto-hide success/error messages after 5 seconds
    const alerts = document.querySelectorAll('.bg-green-100, .bg-red-100');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.display = 'none';
        }, 5000);
    });
});
