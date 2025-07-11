import './bootstrap';
import Alpine from 'alpinejs';
import AOS from 'aos';
import 'aos/dist/aos.css';
import Splide from '@splidejs/splide';
// Import cart functionality
import './cart';


// Import admin scripts
import AdminUtils from './admin/admin';

// Make AdminUtils available globally
window.AdminUtils = AdminUtils;

// Initialize Alpine.js
window.Alpine = Alpine;
Alpine.start();

// Initialize AOS (Animate on Scroll)
AOS.init({
    duration: 800,
    easing: 'ease-in-out',
    once: true,
    mirror: false
});

// Initialize DataTables defaults if jQuery is available
document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.jQuery !== 'undefined' && typeof window.jQuery.fn.dataTable !== 'undefined') {
        window.jQuery.extend(true, window.jQuery.fn.dataTable.defaults, {
            language: {
                paginate: {
                    previous: '‹',
                    next: '›'
                }
            },
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'collection',
                    text: 'Export',
                    buttons: ['copy', 'csv', 'excel', 'pdf']
                }
            ]
        });
    }
});

// Product Quick View Modal
document.addEventListener('alpine:init', () => {
    Alpine.data('quickView', () => ({
        open: false,
        product: null,
        
        init() {
            // Close modal on escape key
            window.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.open) {
                    this.open = false;
                }
            });
        },
        
        showProduct(product) {
            this.product = product;
            this.open = true;
        }
    }));
});

// Initialize all carousels
document.addEventListener('DOMContentLoaded', () => {
    
    // Related Products Carousel
    const relatedSlider = document.querySelector('#related-products');
    if (relatedSlider) {
        new Splide(relatedSlider, {
            perPage: 4,
            gap: '2rem',
            breakpoints: {
                1024: { perPage: 3 },
                768: { perPage: 2 },
                640: { perPage: 1 },
            },
            pagination: false,
            arrows: true,
        }).mount();
    }
    
    // Add to cart functionality for all add-to-cart buttons
    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
    
    addToCartButtons.forEach(button => {
        
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const productId = this.dataset.productId;
            let quantity = 1;
            
            // If we're on the product page, get the quantity from the input
            const quantityInput = document.getElementById('quantity');
            if (quantityInput) {
                quantity = parseInt(quantityInput.value) || 1;
            }
            
            // Get CSRF token from meta tag
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Show loading state
            this.classList.add('opacity-70', 'cursor-wait');
            const originalText = this.innerHTML;
            this.innerHTML = '<svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
            
            // Make AJAX request with fetch
            
            fetch(`/cart/add/${productId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: `quantity=${quantity}`
            })
            .then(response => {
                return response.json();
            })
            .then(data => {
                
                // Reset button state
                this.classList.remove('opacity-70', 'cursor-wait');
                this.innerHTML = originalText;
                
                // Update cart count in navigation
                const cartCount = document.querySelector('#cart-count');
                if (cartCount) {
                    cartCount.textContent = data.cart_count;
                    if (data.cart_count > 0) {
                        cartCount.classList.remove('hidden');
                    }
                }
                
                // Show success message
                const successMessage = document.createElement('div');
                successMessage.className = 'fixed top-20 right-5 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-lg z-50 flex items-center';
                successMessage.innerHTML = `
                    <svg class="h-5 w-5 mr-2 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span>${data.message}</span>
                `;
                document.body.appendChild(successMessage);
                
                // Remove success message after 3 seconds
                setTimeout(() => {
                    successMessage.remove();
                }, 3000);
                
            })
            .catch(error => {
                console.error('Error adding to cart:', error);
                
                // Reset button state
                this.classList.remove('opacity-70', 'cursor-wait');
                this.innerHTML = originalText;
                
                // Show error message
                const errorMessage = document.createElement('div');
                errorMessage.className = 'fixed top-20 right-5 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-lg z-50 flex items-center';
                errorMessage.innerHTML = `
                    <svg class="h-5 w-5 mr-2 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <span>Error adding to cart. Please try again.</span>
                `;
                document.body.appendChild(errorMessage);
                
                // Remove error message after 3 seconds
                setTimeout(() => {
                    errorMessage.remove();
                }, 3000);
            });
        });
    });
    
    // Add event delegation for dynamically added buttons
    document.body.addEventListener('click', function(e) {
        // Find closest add-to-cart button if clicking inside it
        const button = e.target.closest('.add-to-cart-btn');
        
        // If click was not on or inside an add-to-cart button, exit
        if (!button) return;
        
        // If this button already has a direct event listener, exit
        // This prevents double-triggering
        if (button.hasAttribute('data-has-listener')) return;
        
        // Set attribute to prevent future delegation
        button.setAttribute('data-has-listener', 'true');
        
        
        e.preventDefault();
        
        // Same logic as above
        const productId = button.dataset.productId;
        let quantity = 1;
        
        // If we're on the product page, get the quantity from the input
        const quantityInput = document.getElementById('quantity');
        if (quantityInput) {
            quantity = parseInt(quantityInput.value) || 1;
        }
        
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Show loading state
        button.classList.add('opacity-70', 'cursor-wait');
        const originalText = button.innerHTML;
        button.innerHTML = '<svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
        
        fetch(`/cart/add/${productId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: `quantity=${quantity}`
        })
        .then(response => response.json())
        .then(data => {
            // Reset button state
            button.classList.remove('opacity-70', 'cursor-wait');
            button.innerHTML = originalText;
            
            // Update cart count
            const cartCount = document.querySelector('#cart-count');
            if (cartCount) {
                cartCount.textContent = data.cart_count;
                if (data.cart_count > 0) {
                    cartCount.classList.remove('hidden');
                }
            }
            
            // Show success message
            const successMessage = document.createElement('div');
            successMessage.className = 'fixed top-20 right-5 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-lg z-50 flex items-center';
            successMessage.innerHTML = `
                <svg class="h-5 w-5 mr-2 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span>${data.message}</span>
            `;
            document.body.appendChild(successMessage);
            
            // Remove success message after 3 seconds
            setTimeout(() => {
                successMessage.remove();
            }, 3000);
        })
        .catch(error => {
            console.error('Error adding to cart:', error);
            
            // Reset button state
            button.classList.remove('opacity-70', 'cursor-wait');
            button.innerHTML = originalText;
            
            // Show error message
            const errorMessage = document.createElement('div');
            errorMessage.className = 'fixed top-20 right-5 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-lg z-50 flex items-center';
            errorMessage.innerHTML = `
                <svg class="h-5 w-5 mr-2 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                <span>Error adding to cart. Please try again.</span>
            `;
            document.body.appendChild(errorMessage);
            
            // Remove error message after 3 seconds
            setTimeout(() => {
                errorMessage.remove();
            }, 3000);
        });
    });
});

// Mobile Menu Toggle
window.toggleMobileMenu = function() {
    document.querySelector('#mobile-menu').classList.toggle('hidden');
};

// Add image preview for file uploads in settings page
document.addEventListener('DOMContentLoaded', function() {
    // Handle file input changes in the main settings form
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            
            // Find the closest parent container to locate preview elements
            const container = input.closest('.form-group') || input.closest('.setting-item') || input.closest('div');
            
            // Create or find the preview container
            let previewContainer = container.querySelector('.file-preview');
            if (!previewContainer) {
                previewContainer = document.createElement('div');
                previewContainer.className = 'file-preview mt-2 rounded-lg overflow-hidden border border-gray-200';
                
                // Insert after the label/current file display but before file input
                const fileInputContainer = input.closest('label');
                if (fileInputContainer && fileInputContainer.parentNode) {
                    fileInputContainer.parentNode.insertBefore(previewContainer, fileInputContainer);
                } else {
                    input.parentNode.insertBefore(previewContainer, input);
                }
            }
            
            // Check if it's an image file
            const isImage = /\.(jpg|jpeg|png|gif|webp|svg|ico)$/i.test(file.name);
            
            if (isImage) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewContainer.innerHTML = `
                        <div class="relative group">
                            <img src="${e.target.result}" class="w-full h-auto max-h-48 object-contain bg-gray-100" alt="Preview">
                            <div class="p-2 bg-gray-100">
                                <p class="text-sm text-gray-700 truncate">${file.name}</p>
                                <p class="text-xs text-gray-500">${(file.size / 1024).toFixed(2)} KB</p>
                            </div>
                        </div>
                    `;
                };
                reader.readAsDataURL(file);
            } else {
                // For non-image files, show file info
                previewContainer.innerHTML = `
                    <div class="p-4 bg-gray-100">
                        <div class="flex items-center">
                            <svg class="w-8 h-8 text-gray-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-gray-800">${file.name}</p>
                                <p class="text-xs text-gray-500">${(file.size / 1024).toFixed(2)} KB</p>
                            </div>
                        </div>
                    </div>
                `;
            }
        });
    });
    
    // Handle file input changes in the edit modal
    const editSettingModal = document.getElementById('editSettingModal');
    if (editSettingModal) {
        editSettingModal.addEventListener('change', function(e) {
            if (e.target.type === 'file') {
                const file = e.target.files[0];
                if (!file) return;
                
                // Find the modal body to locate preview elements
                const modalBody = e.target.closest('.modal-body') || e.target.closest('form');
                
                // Create or find the preview container
                let previewContainer = modalBody.querySelector('.file-preview');
                if (!previewContainer) {
                    previewContainer = document.createElement('div');
                    previewContainer.className = 'file-preview mt-4 rounded-lg overflow-hidden border border-gray-200';
                    
                    // Insert after the input container
                    const inputContainer = e.target.closest('.space-y-4') || e.target.closest('div');
                    inputContainer.appendChild(previewContainer);
                }
                
                // Check if it's an image file
                const isImage = /\.(jpg|jpeg|png|gif|webp|svg|ico)$/i.test(file.name);
                
                if (isImage) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewContainer.innerHTML = `
                            <div class="relative group">
                                <img src="${e.target.result}" class="w-full h-auto max-h-48 object-contain bg-gray-100" alt="Preview">
                                <div class="p-2 bg-gray-100">
                                    <p class="text-sm text-gray-700 truncate">${file.name}</p>
                                    <p class="text-xs text-gray-500">${(file.size / 1024).toFixed(2)} KB</p>
                                </div>
                            </div>
                        `;
                    };
                    reader.readAsDataURL(file);
                } else {
                    // For non-image files, show file info
                    previewContainer.innerHTML = `
                        <div class="p-4 bg-gray-100">
                            <div class="flex items-center">
                                <svg class="w-8 h-8 text-gray-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">${file.name}</p>
                                    <p class="text-xs text-gray-500">${(file.size / 1024).toFixed(2)} KB</p>
                                </div>
                            </div>
                        </div>
                    `;
                }
            }
        });
    }
});

// Newsletter Form Submission
const newsletterForm = document.querySelector('#newsletter-form');
if (newsletterForm) {
    newsletterForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const email = newsletterForm.querySelector('input[type="email"]').value;
        
        try {
            const response = await fetch('/api/newsletter/subscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: `email=${encodeURIComponent(email)}`
            });
            
            if (response.ok) {
                alert('Thank you for subscribing!');
                newsletterForm.reset();
            } else {
                throw new Error('Subscription failed');
            }
        } catch (error) {
            alert('Sorry, there was an error. Please try again later.');
        }
    });
}

// Product Image Zoom Effect
const productImages = document.querySelectorAll('.product-zoom-image');
productImages.forEach(image => {
    image.addEventListener('mousemove', (e) => {
        const { left, top, width, height } = image.getBoundingClientRect();
        const x = (e.clientX - left) / width;
        const y = (e.clientY - top) / height;
        
        image.style.transformOrigin = `${x * 100}% ${y * 100}%`;
    });
    
    image.addEventListener('mouseenter', () => {
        image.style.transform = 'scale(1.5)';
    });
    
    image.addEventListener('mouseleave', () => {
        image.style.transform = 'scale(1)';
    });
});

// Lazy loading images with Intersection Observer
const setupLazyLoading = () => {
    if ('IntersectionObserver' in window) {
        const lazyImages = document.querySelectorAll('img[loading="lazy"]');
        
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    
                    // Load the image if it has a data-src attribute
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                    }
                    
                    // Set background placeholder to transparent once loaded
                    img.addEventListener('load', () => {
                        const parentDiv = img.closest('.bg-gray-100');
                        if (parentDiv) {
                            parentDiv.classList.remove('bg-gray-100');
                        }
                    });
                    
                    // Stop observing this image
                    observer.unobserve(img);
                }
            });
        }, {
            rootMargin: '200px 0px', // Load images 200px before they enter viewport
            threshold: 0.01 // Trigger when at least 1% of the image is visible
        });
        
        // Start observing each lazy image
        lazyImages.forEach(img => {
            imageObserver.observe(img);
        });
    } else {
        // Fallback for browsers that don't support Intersection Observer
        const lazyImages = document.querySelectorAll('img[data-src]');
        lazyImages.forEach(img => {
            img.src = img.dataset.src;
            img.removeAttribute('data-src');
        });
    }
};

// Call lazy loading setup when DOM is loaded
document.addEventListener('DOMContentLoaded', setupLazyLoading);

// Add page transition effects
document.addEventListener('DOMContentLoaded', () => {
    // Apply transition class to main content
    const main = document.querySelector('main');
    if (main) {
        main.classList.add('opacity-0');
        setTimeout(() => {
            main.classList.remove('opacity-0');
            main.classList.add('transition-opacity', 'duration-500', 'opacity-100');
        }, 50);
    }
});

// Cache DOM elements when possible instead of repeated queries
const cacheDomElements = () => {
    window.domCache = {
        cartCount: document.getElementById('cart-count'),
        miniCart: document.getElementById('mini-cart'),
        toastContainer: document.getElementById('toast-container'),
        searchInput: document.getElementById('search-input'),
        searchResults: document.getElementById('search-results')
    };
};

document.addEventListener('DOMContentLoaded', cacheDomElements);
