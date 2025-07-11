/**
 * Cart functionality for Celestial Cosmetics
 */
document.addEventListener('DOMContentLoaded', function() {
    // Find Add to Cart buttons
    const addToCartButtons = document.querySelectorAll('.add-to-cart, [data-action="add-to-cart"]');
    const cartCountElement = document.getElementById('cart-count');
    const miniCartTrigger = document.querySelector('[data-action="toggle-mini-cart"]');
    
    // Add click event listeners to all Add to Cart buttons
    addToCartButtons.forEach(button => {
        attachAddToCartListener(button);
    });
    
    // Use event delegation for dynamically added buttons
    document.body.addEventListener('click', function(e) {
        const button = e.target.closest('.add-to-cart, [data-action="add-to-cart"]');
        if (!button || button.hasAttribute('data-has-listener')) return;
        
        // Prevent default action and handle with our listener
        e.preventDefault();
        attachAddToCartListener(button);
        button.setAttribute('data-has-listener', 'true');
        button.click(); // Trigger the click to process the action
    });
    
    // Function to attach event listener to add-to-cart buttons
    function attachAddToCartListener(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const form = this.closest('form');
            if (!form) return;
            
            const url = form.getAttribute('action');
            const formData = new FormData(form);
            const btnText = this.innerHTML;
            
            // Show loading state
            this.innerHTML = '<svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
            this.disabled = true;
            
            // Send the request
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update cart count
                    if (cartCountElement) {
                        if (data.cart_count > 0) {
                            cartCountElement.textContent = data.cart_count;
                            cartCountElement.classList.remove('hidden');
                        } else {
                            cartCountElement.classList.add('hidden');
                        }
                    }
                    
                    // Update button to show success
                    this.innerHTML = '<svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
                    
                    // Show mini cart if it exists
                    if (miniCartTrigger && typeof(miniCartTrigger.click) === 'function') {
                        miniCartTrigger.click();
                    }
                    
                    // Show toast notification
                    showToast(data.message || 'Product added to cart', 'success');
                    
                    // Reset button after delay
                    setTimeout(() => {
                        this.innerHTML = btnText;
                        this.disabled = false;
                    }, 2000);
                } else {
                    // Show error
                    this.innerHTML = btnText;
                    this.disabled = false;
                    showToast(data.message || 'Error adding product to cart', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.innerHTML = btnText;
                this.disabled = false;
                showToast('Error adding product to cart', 'error');
            });
        });
        
        // Mark button as having listener attached
        button.setAttribute('data-has-listener', 'true');
    }
});

/**
 * Show toast notification
 * @param {string} message - Message to display
 * @param {string} type - Type of toast (success, error, info)
 */
window.showToast = function(message, type = 'success') {
    // Create toast container if it doesn't exist
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'fixed top-4 right-4 z-50 flex flex-col gap-2';
        document.body.appendChild(toastContainer);
    }
    
    const toast = document.createElement('div');
    let bgColor = 'bg-green-500';
    
    if (type === 'error') {
        bgColor = 'bg-red-500';
    } else if (type === 'info') {
        bgColor = 'bg-blue-500';
    }
    
    toast.className = `${bgColor} text-white px-4 py-2 rounded shadow-lg flex items-center transition-all duration-300 transform translate-x-full`;
    toast.innerHTML = `
        <div class="mr-2">
            ${type === 'success' ? 
                '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>' : 
                (type === 'error' ? 
                    '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>' : 
                    '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
                )
            }
        </div>
        <p>${message}</p>
    `;
    
    toastContainer.appendChild(toast);
    
    // Animate in
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
    }, 10);
    
    // Remove after 3 seconds
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => {
            toast.remove();
        }, 300);
    }, 3000);
}; 