document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mobileMenu = document.querySelector('.mobile-menu');
    const mobileMenuClose = document.querySelector('.mobile-menu-close');
    
    if (mobileMenuToggle && mobileMenu && mobileMenuClose) {
        mobileMenuToggle.addEventListener('click', function() {
            mobileMenu.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
        
        mobileMenuClose.addEventListener('click', function() {
            mobileMenu.classList.remove('active');
            document.body.style.overflow = '';
        });
    }
    
    // User menu dropdown
    const userMenuToggle = document.querySelector('.user-menu-toggle');
    const userMenuDropdown = document.querySelector('.user-menu-dropdown');
    
    if (userMenuToggle && userMenuDropdown) {
        userMenuToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            userMenuDropdown.classList.toggle('active');
        });
        
        document.addEventListener('click', function(e) {
            if (!userMenuDropdown.contains(e.target) && !userMenuToggle.contains(e.target)) {
                userMenuDropdown.classList.remove('active');
            }
        });
    }
    
    // Form validation
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            let valid = true;
            const requiredInputs = form.querySelectorAll('[required]');
            
            requiredInputs.forEach(input => {
                if (!input.value.trim()) {
                    valid = false;
                    input.classList.add('error');
                    
                    // Create error message if it doesn't exist
                    let errorMsg = input.nextElementSibling;
                    if (!errorMsg || !errorMsg.classList.contains('error-message')) {
                        errorMsg = document.createElement('div');
                        errorMsg.classList.add('error-message');
                        errorMsg.textContent = 'This field is required';
                        input.parentNode.insertBefore(errorMsg, input.nextSibling);
                    }
                } else {
                    input.classList.remove('error');
                    
                    // Remove error message if it exists
                    const errorMsg = input.nextElementSibling;
                    if (errorMsg && errorMsg.classList.contains('error-message')) {
                        errorMsg.remove();
                    }
                    
                    // Email validation
                    if (input.type === 'email' && !validateEmail(input.value)) {
                        valid = false;
                        input.classList.add('error');
                        
                        let errorMsg = input.nextElementSibling;
                        if (!errorMsg || !errorMsg.classList.contains('error-message')) {
                            errorMsg = document.createElement('div');
                            errorMsg.classList.add('error-message');
                            errorMsg.textContent = 'Please enter a valid email address';
                            input.parentNode.insertBefore(errorMsg, input.nextSibling);
                        }
                    }
                    
                    // Password validation
                    if (input.type === 'password' && input.value.length < 8) {
                        valid = false;
                        input.classList.add('error');
                        
                        let errorMsg = input.nextElementSibling;
                        if (!errorMsg || !errorMsg.classList.contains('error-message')) {
                            errorMsg = document.createElement('div');
                            errorMsg.classList.add('error-message');
                            errorMsg.textContent = 'Password must be at least 8 characters long';
                            input.parentNode.insertBefore(errorMsg, input.nextSibling);
                        }
                    }
                }
            });
            
            if (!valid) {
                e.preventDefault();
            }
        });
        
        // Clear error on input
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                this.classList.remove('error');
                
                const errorMsg = this.nextElementSibling;
                if (errorMsg && errorMsg.classList.contains('error-message')) {
                    errorMsg.remove();
                }
            });
        });
    });
    
    // Email validation function
    function validateEmail(email) {
        const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(String(email).toLowerCase());
    }
    
    // Password confirmation validation
    const passwordInputs = document.querySelectorAll('input[type="password"]');
    const passwordConfirmInputs = document.querySelectorAll('input[name="confirm_password"]');
    
    passwordConfirmInputs.forEach(input => {
        input.addEventListener('input', function() {
            const passwordInput = this.form.querySelector('input[name="password"]');
            
            if (passwordInput && this.value !== passwordInput.value) {
                this.classList.add('error');
                
                let errorMsg = this.nextElementSibling;
                if (!errorMsg || !errorMsg.classList.contains('error-message')) {
                    errorMsg = document.createElement('div');
                    errorMsg.classList.add('error-message');
                    errorMsg.textContent = 'Passwords do not match';
                    this.parentNode.insertBefore(errorMsg, this.nextSibling);
                }
            } else {
                this.classList.remove('error');
                
                const errorMsg = this.nextElementSibling;
                if (errorMsg && errorMsg.classList.contains('error-message')) {
                    errorMsg.remove();
                }
            }
        });
    });
    
    // Live payouts ticker animation
    const payoutItems = document.querySelector('.payout-items');
    
    if (payoutItems && payoutItems.children.length > 0) {
        setInterval(() => {
            const firstItem = payoutItems.children[0];
            const itemWidth = firstItem.offsetWidth + parseInt(window.getComputedStyle(firstItem).marginRight);
            
            payoutItems.style.transition = 'transform 0.5s ease-in-out';
            payoutItems.style.transform = `translateX(-${itemWidth}px)`;
            
            setTimeout(() => {
                payoutItems.style.transition = 'none';
                payoutItems.style.transform = 'translateX(0)';
                payoutItems.appendChild(firstItem);
            }, 500);
        }, 3000);
    }
});