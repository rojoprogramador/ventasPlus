/**
 * CSRF Token Handler - Handles 419 Page Expired errors and session issues
 * This script ensures proper CSRF token handling and session management
 */

(function() {
    // Function to get the CSRF token from meta tag
    function getCSRFToken() {
        const token = document.head.querySelector('meta[name="csrf-token"]');
        return token ? token.content : null;
    }

    // Function to check if CSRF token exists and is valid
    function verifyCSRFToken() {
        const token = getCSRFToken();
        if (!token) {
            console.error('CSRF token not found in meta tag. Page might not function correctly.');
            return false;
        }
        return true;
    }

    // Function to refresh the page if token is missing
    function refreshIfTokenMissing() {
        if (!verifyCSRFToken()) {
            console.log('CSRF token missing or invalid. Attempting to refresh the page...');
            window.location.reload();
            return true;
        }
        return false;
    }

    // Initialize the CSRF handler
    function init() {
        // Verify token on page load
        const tokenValid = verifyCSRFToken();
        
        if (!tokenValid) {
            console.warn('CSRF token validation failed on page load. This may cause issues with form submissions.');
        }
          // Add refresh button to page if in debug mode
        if (window.DEBUG_MODE || document.querySelector('meta[name="app-debug"]')?.content === 'true') {
            const refreshButton = document.createElement('button');
            refreshButton.textContent = 'Refresh CSRF Token';
            refreshButton.style.position = 'fixed';
            refreshButton.style.bottom = '10px';
            refreshButton.style.right = '10px';
            refreshButton.style.zIndex = '9999';
            refreshButton.style.padding = '8px 12px';
            refreshButton.style.backgroundColor = '#4CAF50';
            refreshButton.style.color = 'white';
            refreshButton.style.border = 'none';
            refreshButton.style.borderRadius = '4px';
            refreshButton.style.cursor = 'pointer';
            
            refreshButton.addEventListener('click', function() {
                window.location.reload();
            });
            
            // Only append to body if it exists
            if (document.body) {
                document.body.appendChild(refreshButton);
            }
        }

        // Monitor for CSRF token errors
        const originalFetch = window.fetch;
        window.fetch = function(url, options) {
            return originalFetch(url, options).then(response => {
                if (response.status === 419) {
                    console.error('CSRF token mismatch detected in fetch request. Page will be refreshed.');
                    window.location.reload();
                }
                return response;
            });
        };
          // Add listeners for forms to ensure they have CSRF tokens
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (form.method && form.method.toLowerCase() === 'post') {
                // Check if the form has a CSRF token field
                let hasToken = false;
                for (let i = 0; i < form.elements.length; i++) {
                    if (form.elements[i].name === '_token') {
                        hasToken = true;
                        break;
                    }
                }
                
                if (!hasToken) {
                    console.warn('Form is missing CSRF token field. Adding it automatically.');
                    const tokenInput = document.createElement('input');
                    tokenInput.type = 'hidden';
                    tokenInput.name = '_token';
                    tokenInput.value = getCSRFToken();
                    form.appendChild(tokenInput);
                }
            }
        }, true);
    }
    
    // Execute when DOM is fully loaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        // If document is already loaded, wait a small timeout to ensure Vue has initialized
        setTimeout(init, 100);
    }
    
    // Export functions for possible external use
    window.CSRFHandler = {
        getToken: getCSRFToken,
        verifyToken: verifyCSRFToken,
        refreshIfTokenMissing: refreshIfTokenMissing
    };
})();
