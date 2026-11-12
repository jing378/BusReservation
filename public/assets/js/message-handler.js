/**
 * Message Handler for Bus Reservation System
 * Provides auto-disappearing messages for both success and error messages
 */

class MessageHandler {
    constructor() {
        this.init();
    }

    init() {
        // Handle PHP messages on page load
        this.handlePHPMessages();
        
        // Set up auto-hide for existing messages
        this.setupAutoHide();
    }

    handlePHPMessages() {
        // Handle success messages
        const successMessages = document.querySelectorAll('.qa[style*="color:#22c55e"]');
        successMessages.forEach(message => {
            this.autoHide(message, 3000); // Hide after 3 seconds
        });

        // Handle error messages
        const errorMessages = document.querySelectorAll('.qa[style*="color:#ef4444"]');
        errorMessages.forEach(message => {
            this.autoHide(message, 1000); // Hide after 1 second
        });

        // Handle individual field errors
        const fieldErrors = document.querySelectorAll('div[style*="color:#ef4444"][style*="font-size:12px"]');
        fieldErrors.forEach(error => {
            this.autoHide(error, 1000); // Hide after 1 second
        });
    }

    setupAutoHide() {
        // Set up auto-hide for any new messages that might be added dynamically
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                mutation.addedNodes.forEach((node) => {
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        if (node.classList && node.classList.contains('qa')) {
                            if (node.style.color === 'rgb(34, 197, 94)' || node.style.color === '#22c55e') {
                                this.autoHide(node, 3000);
                            } else if (node.style.color === 'rgb(239, 68, 68)' || node.style.color === '#ef4444') {
                                this.autoHide(node, 1000);
                            }
                        }
                    }
                });
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    autoHide(element, delay = 3000) {
        if (!element) return;

        // Add fade-out animation class
        element.style.transition = 'opacity 0.5s ease-out, transform 0.5s ease-out';
        
        setTimeout(() => {
            element.style.opacity = '0';
            element.style.transform = 'translateY(-10px)';
            
            setTimeout(() => {
                if (element.parentNode) {
                    element.parentNode.removeChild(element);
                }
            }, 500); // Wait for animation to complete
        }, delay);
    }

    showMessage(message, type = 'success', duration = 3000) {
        const messageContainer = document.createElement('div');
        messageContainer.className = 'qa';
        messageContainer.style.cssText = `
            justify-content: flex-start;
            color: ${type === 'success' ? '#22c55e' : '#ef4444'};
            margin: 1rem 0;
            padding: 12px 16px;
            border-radius: 8px;
            background: ${type === 'success' ? 'rgba(34, 197, 94, 0.1)' : 'rgba(239, 68, 68, 0.1)'};
            border: 1px solid ${type === 'success' ? 'rgba(34, 197, 94, 0.3)' : 'rgba(239, 68, 68, 0.3)'};
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 500;
            transition: opacity 0.5s ease-out, transform 0.5s ease-out;
        `;

        const icon = document.createElement('i');
        icon.className = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
        
        const text = document.createElement('span');
        text.textContent = message;

        messageContainer.appendChild(icon);
        messageContainer.appendChild(text);

        // Insert at the top of the main content
        const mainContent = document.querySelector('.main-layout') || document.querySelector('main') || document.body;
        if (mainContent) {
            mainContent.insertBefore(messageContainer, mainContent.firstChild);
        } else {
            document.body.appendChild(messageContainer);
        }

        // Auto-hide the message
        this.autoHide(messageContainer, duration);
    }

    showSuccess(message, duration = 3000) {
        this.showMessage(message, 'success', duration);
    }

    showError(message, duration = 1000) {
        this.showMessage(message, 'error', duration);
    }
}

// Initialize message handler when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.messageHandler = new MessageHandler();
});

// Global functions for easy access
function showSuccessMessage(message, duration = 3000) {
    if (window.messageHandler) {
        window.messageHandler.showSuccess(message, duration);
    }
}

function showErrorMessage(message, duration = 1000) {
    if (window.messageHandler) {
        window.messageHandler.showError(message, duration);
    }
}
