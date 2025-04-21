// Main JavaScript file for Farmer's Market website

// Document ready function
$(document).ready(function() {
    // Initialize animations
    initAnimations();
    
    // Initialize cart functionality
    initCart();
    
    // Initialize user authentication
    initAuth();
    
    // Mobile menu toggle
    $('#mobile-menu-button').click(function() {
        $('#mobile-menu').slideToggle();
    });
});

// Initialize animations
function initAnimations() {
    // Animate staggered elements
    $('.staggered-animation > *').each(function(index) {
        $(this).delay(index * 100).animate({
            opacity: 1,
            transform: 'translateY(0)'
        }, 500);
    });
    
    // Add scroll animations
    $(window).scroll(function() {
        $('.animate-on-scroll').each(function() {
            if (isElementInViewport(this) && !$(this).hasClass('animated')) {
                $(this).addClass('animated');
                $(this).css({
                    opacity: 1,
                    transform: 'translateY(0)'
                });
            }
        });
    });
    
    // Trigger scroll once to animate elements in viewport on page load
    $(window).trigger('scroll');
}

// Check if element is in viewport
function isElementInViewport(el) {
    const rect = el.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
}

// Initialize cart functionality
function initCart() {
    // Get cart from localStorage
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    // Update cart count
    updateCartCount();
    
    // Add to cart button click event
    $('.add-to-cart').click(function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const price = $(this).data('price');
        
        // Check if product is already in cart
        const existingItem = cart.find(item => item.id === id);
        
        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            cart.push({
                id: id,
                name: name,
                price: price,
                quantity: 1
            });
        }
        
        // Save to localStorage
        localStorage.setItem('cart', JSON.stringify(cart));
        
        // Update cart count
        updateCartCount();
        
        // Show notification
        showNotification(`${name} added to cart!`);
    });
    
    // Update cart count function
    function updateCartCount() {
        const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
        $('#cart-count').text(totalItems);
    }
    
    // If on cart page, initialize cart page functionality
    if (window.location.pathname.includes('cart.html')) {
        initCartPage();
    }
}

// Initialize cart page functionality
function initCartPage() {
    // Get cart from localStorage
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    // Update cart UI
    updateCartUI();
    
    // Update cart button click event
    $('#update-cart').click(function() {
        // Get all quantity inputs
        $('.item-quantity').each(function() {
            const id = $(this).data('id');
            const quantity = parseInt($(this).val());
            
            // Find the item in the cart
            const item = cart.find(item => item.id === id);
            
            if (item) {
                if (quantity > 0) {
                    item.quantity = quantity;
                } else {
                    // Remove item if quantity is 0
                    const index = cart.findIndex(item => item.id === id);
                    if (index !== -1) {
                        cart.splice(index, 1);
                    }
                }
            }
        });
        
        // Save updated cart to localStorage
        localStorage.setItem('cart', JSON.stringify(cart));
        
        // Update cart UI
        updateCartUI();
        
        // Show notification
        showNotification('Cart updated successfully!');
    });
    
    // Update cart UI function
    function updateCartUI() {
        // Show/hide empty cart message
        if (cart.length === 0) {
            $('#empty-cart').removeClass('hidden');
            $('#cart-items').addClass('hidden');
            $('#order-summary').addClass('hidden');
        } else {
            $('#empty-cart').addClass('hidden');
            $('#cart-items').removeClass('hidden');
            $('#order-summary').removeClass('hidden');
            
            // Clear cart items list
            $('#cart-items-list').empty();
            
            // Add cart items to the list
            let subtotal = 0;
            
            cart.forEach(item => {
                const itemTotal = item.price * item.quantity;
                subtotal += itemTotal;
                
                // Create cart item row
                const cartItemHtml = `
                    <div class="p-4 border-b">
                        <div class="grid grid-cols-12 gap-4 items-center">
                            <div class="col-span-6 md:col-span-6 flex items-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-md mr-4 flex-shrink-0 overflow-hidden">
                                    <img src="https://placehold.co/100/f0fdf4/166534?text=${item.name.charAt(0)}" alt="${item.name}" class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <h3 class="font-medium text-gray-800">${item.name}</h3>
                                    <button class="remove-item text-red-500 text-sm hover:text-red-700" data-id="${item.id}">
                                        <i class="fas fa-trash-alt mr-1"></i> Remove
                                    </button>
                                </div>
                            </div>
                            <div class="col-span-2 md:col-span-2 text-center">
                                $${item.price}
                            </div>
                            <div class="col-span-2 md:col-span-2 text-center">
                                <input type="number" class="item-quantity w-16 px-2 py-1 border border-gray-300 rounded text-center" value="${item.quantity}" min="0" data-id="${item.id}">
                            </div>
                            <div class="col-span-2 md:col-span-2 text-right font-medium">
                                $${itemTotal.toFixed(2)}
                            </div>
                        </div>
                    </div>
                `;
                
                $('#cart-items-list').append(cartItemHtml);
            });
            
            // Calculate order summary
            const tax = subtotal * 0.08; // 8% tax
            const shipping = subtotal > 0 ? 5 : 0; // $5 shipping fee
            const total = subtotal + tax + shipping;
            
            // Update order summary
            $('#subtotal').text('$' + subtotal.toFixed(2));
            $('#tax').text('$' + tax.toFixed(2));
            $('#shipping').text('$' + shipping.toFixed(2));
            $('#total').text('$' + total.toFixed(2));
            
            // Add event listener for remove buttons
            $('.remove-item').click(function() {
                const id = $(this).data('id');
                
                // Remove item from cart
                const index = cart.findIndex(item => item.id === id);
                if (index !== -1) {
                    cart.splice(index, 1);
                }
                
                // Save updated cart to localStorage
                localStorage.setItem('cart', JSON.stringify(cart));
                
                // Update cart UI
                updateCartUI();
                
                // Show notification
                showNotification('Item removed from cart!');
            });
        }
    }
}

// Initialize user authentication
function initAuth() {
    // Check if user is logged in
    const isLoggedIn = localStorage.getItem('isLoggedIn') === 'true';
    
    if (isLoggedIn) {
        $('#logged-out-menu').hide();
        $('#logged-in-menu').show();
    } else {
        $('#logged-out-menu').show();
        $('#logged-in-menu').hide();
    }
    
    // Logout button click event
    $('#logout-btn').click(function(e) {
        e.preventDefault();
        localStorage.setItem('isLoggedIn', 'false');
        $('#logged-out-menu').show();
        $('#logged-in-menu').hide();
        showNotification('You have been logged out.');
        
        // In a real application, this would also call the logout API
        // $.post('backend/user.php', { action: 'logout' });
    });
    
    // If on login page, initialize login form
    if (window.location.pathname.includes('login.html')) {
        $('#login-form').submit(function(e) {
            e.preventDefault();
            
            // Get form values
            const email = $('#email').val();
            const password = $('#password').val();
            
            // In a real application, this would send a request to the server
            // For demo purposes, we'll simulate a successful login
            
            // Simulate API call with a timeout
            setTimeout(function() {
                // Set login status in localStorage
                localStorage.setItem('isLoggedIn', 'true');
                localStorage.setItem('userEmail', email);
                
                // Show notification
                showNotification('Login successful! Redirecting...');
                
                // Redirect to home page after a short delay
                setTimeout(function() {
                    window.location.href = 'index.html';
                }, 1500);
            }, 1000);
        });
    }
    
    // If on register page, initialize register form
    if (window.location.pathname.includes('register.html')) {
        $('#register-form').submit(function(e) {
            e.preventDefault();
            
            // Get form values
            const name = $('#name').val();
            const email = $('#email').val();
            const password = $('#password').val();
            const confirmPassword = $('#confirm-password').val();
            
            // Validate passwords match
            if (password !== confirmPassword) {
                showNotification('Passwords do not match!', 'error');
                return;
            }
            
            // In a real application, this would send a request to the server
            // For demo purposes, we'll simulate a successful registration
            
            // Simulate API call with a timeout
            setTimeout(function() {
                // Set login status in localStorage
                localStorage.setItem('isLoggedIn', 'true');
                localStorage.setItem('userEmail', email);
                localStorage.setItem('userName', name);
                
                // Show notification
                showNotification('Registration successful! Redirecting...');
                
                // Redirect to home page after a short delay
                setTimeout(function() {
                    window.location.href = 'index.html';
                }, 1500);
            }, 1000);
        });
    }
}

// Show notification function
function showNotification(message, type = 'success') {
    // Create notification element if it doesn't exist
    if ($('#notification').length === 0) {
        $('body').append('<div id="notification" class="fixed top-20 right-4 py-2 px-4 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300"></div>');
    }
    
    // Set notification color based on type
    if (type === 'success') {
        $('#notification').removeClass('bg-red-600').addClass('bg-primary-600 text-white');
    } else if (type === 'error') {
        $('#notification').removeClass('bg-primary-600').addClass('bg-red-600 text-white');
    }
    
    // Show notification with message
    $('#notification').text(message).removeClass('translate-x-full');
    
    // Hide after 3 seconds
    setTimeout(function() {
        $('#notification').addClass('translate-x-full');
    }, 3000);
}