/**
 * BloodLink - Main JavaScript
 * Handles interactive functionality and client-side logic
 */

// =============================================
// DOM Content Loaded
// =============================================

document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

// =============================================
// App Initialization
// =============================================

function initializeApp() {
    setupEventListeners();
    setupScrollAnimations();
    setupFormValidation();
    setupNotifications();
}

// =============================================
// Event Listeners
// =============================================

function setupEventListeners() {
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href !== '#' && document.querySelector(href)) {
                e.preventDefault();
                const target = document.querySelector(href);
                const offset = 80; // Navbar height
                const targetPosition = target.offsetTop - offset;
                
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });

    // Active nav link highlight
    highlightActiveNavLink();

    // Responsive navbar collapse on link click
    document.querySelectorAll('.navbar-nav a').forEach(link => {
        link.addEventListener('click', function() {
            const navbarToggle = document.querySelector('.navbar-toggler');
            if (window.getComputedStyle(navbarToggle).display !== 'none') {
                navbarToggle.click();
            }
        });
    });
}

function highlightActiveNavLink() {
    const currentPath = window.location.pathname.split('/').pop() || 'index.html';
    document.querySelectorAll('.navbar-nav a.nav-link').forEach(link => {
        const href = link.getAttribute('href');
        if (href === currentPath || (currentPath === '' && href === 'index.html')) {
            link.classList.add('active');
        } else {
            link.classList.remove('active');
        }
    });
}

// =============================================
// Scroll Animations
// =============================================

function setupScrollAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Observe all elements with animation classes
    document.querySelectorAll('.feature-card, .blood-type-card, .timeline-item').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });
}

// =============================================
// Form Validation
// =============================================

function setupFormValidation() {
    // Prevent form submission for demo
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            // Allow some forms to submit normally, prevent others for demo
            if (!this.id.includes('Form')) {
                return;
            }
            // Demo behavior handled by individual forms
        });
    });
}

// =============================================
// Password Strength Meter
// =============================================

function checkPasswordStrength(password) {
    let strength = 0;
    let feedback = [];

    if (password.length >= 8) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^a-zA-Z0-9]/.test(password)) strength++;

    if (strength < 2) {
        feedback = ['Weak password'];
    } else if (strength < 3) {
        feedback = ['Password could be stronger', 'Try adding symbols'];
    } else if (strength < 4) {
        feedback = ['Good password'];
    } else {
        feedback = ['Strong password'];
    }

    return { strength, feedback };
}

// =============================================
// API Call Simulation
// =============================================

class BloodLinkAPI {
    constructor() {
        this.baseURL = '/api';
        this.token = localStorage.getItem('authToken') || null;
    }

    // Authentication
    async login(email, password) {
        return this.simulateAPICall('POST', '/auth/login', { email, password });
    }

    async register(data) {
        return this.simulateAPICall('POST', '/auth/register', data);
    }

    // Donor Operations
    async searchDonors(filters) {
        return this.simulateAPICall('GET', '/donors/search', filters);
    }

    async getDonorProfile(donorId) {
        return this.simulateAPICall('GET', `/donors/${donorId}`, null);
    }

    async updateDonorProfile(donorId, data) {
        return this.simulateAPICall('PUT', `/donors/${donorId}`, data);
    }

    // Blood Requests
    async createBloodRequest(data) {
        return this.simulateAPICall('POST', '/requests', data);
    }

    async getBloodRequests(filters = {}) {
        return this.simulateAPICall('GET', '/requests', filters);
    }

    async respondToRequest(requestId, accepted) {
        return this.simulateAPICall('POST', `/requests/${requestId}/respond`, { accepted });
    }

    // Notifications
    async getNotifications() {
        return this.simulateAPICall('GET', '/notifications', null);
    }

    async markNotificationAsRead(notificationId) {
        return this.simulateAPICall('PUT', `/notifications/${notificationId}`, { read: true });
    }

    // Helper method for simulating API calls
    async simulateAPICall(method, endpoint, data) {
        return new Promise((resolve) => {
            setTimeout(() => {
                const response = {
                    success: true,
                    data: data || {},
                    message: `${method} ${endpoint} successful`
                };
                resolve(response);
            }, 500); // Simulate network delay
        });
    }
}

// Create global API instance
const bloodLinkAPI = new BloodLinkAPI();

// =============================================
// Notification System
// =============================================

function setupNotifications() {
    // Check for notification support
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }
}

function showNotification(title, options = {}) {
    if ('Notification' in window && Notification.permission === 'granted') {
        new Notification(title, {
            icon: '/images/logo.png',
            ...options
        });
    }
}

function showToast(message, type = 'info', duration = 3000) {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} position-fixed`;
    toast.style.cssText = `
        top: 20px;
        right: 20px;
        z-index: 1050;
        min-width: 300px;
        animation: slideDown 0.3s ease-out;
    `;
    toast.innerHTML = `
        <div class="d-flex justify-content-between align-items-center">
            <span>${message}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, duration);
}

// =============================================
// LocalStorage Utilities
// =============================================

const StorageManager = {
    setUser: function(user) {
        localStorage.setItem('user', JSON.stringify(user));
    },

    getUser: function() {
        const user = localStorage.getItem('user');
        return user ? JSON.parse(user) : null;
    },

    setAuthToken: function(token) {
        localStorage.setItem('authToken', token);
    },

    getAuthToken: function() {
        return localStorage.getItem('authToken');
    },

    setDonorProfile: function(profile) {
        localStorage.setItem('donorProfile', JSON.stringify(profile));
    },

    getDonorProfile: function() {
        const profile = localStorage.getItem('donorProfile');
        return profile ? JSON.parse(profile) : null;
    },

    clear: function() {
        localStorage.clear();
    }
};

// =============================================
// Utility Functions
// =============================================

function formatDate(date) {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(date).toLocaleDateString('en-US', options);
}

function formatTime(date) {
    const options = { hour: '2-digit', minute: '2-digit' };
    return new Date(date).toLocaleTimeString('en-US', options);
}

function calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 3959; // Earth's radius in miles
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
              Math.sin(dLon/2) * Math.sin(dLon/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return (R * c).toFixed(1);
}

function isValidEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function isValidPhone(phone) {
    const re = /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/;
    return re.test(phone);
}

// =============================================
// Modal Utilities
// =============================================

const ModalManager = {
    show: function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
        }
    },

    hide: function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            const bsModal = bootstrap.Modal.getInstance(modal);
            if (bsModal) bsModal.hide();
        }
    },

    toggle: function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            const bsModal = new bootstrap.Modal(modal);
            bsModal.toggle();
        }
    }
};

// =============================================
// Blood Type Compatibility
// =============================================

const BloodTypeUtils = {
    compatibility: {
        'O+': ['O+', 'A+', 'B+', 'AB+'],
        'O-': ['O+', 'O-', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-'],
        'A+': ['A+', 'AB+'],
        'A-': ['A+', 'A-', 'AB+', 'AB-'],
        'B+': ['B+', 'AB+'],
        'B-': ['B+', 'B-', 'AB+', 'AB-'],
        'AB+': ['AB+'],
        'AB-': ['AB+', 'AB-']
    },

    canDonate: function(donorType, recipientType) {
        return this.compatibility[donorType] && this.compatibility[donorType].includes(recipientType);
    },

    getCompatibleTypes: function(bloodType) {
        return this.compatibility[bloodType] || [];
    }
};

// =============================================
// Email Service (Simulated)
// =============================================

const EmailService = {
    sendEmailVerification: async function(email, verificationCode) {
        console.log(`Sending verification email to ${email} with code: ${verificationCode}`);
        return await bloodLinkAPI.simulateAPICall('POST', '/email/send-verification', {
            email,
            verificationCode
        });
    },

    sendPasswordReset: async function(email, resetToken) {
        console.log(`Sending password reset to ${email}`);
        return await bloodLinkAPI.simulateAPICall('POST', '/email/send-reset', {
            email,
            resetToken
        });
    },

    sendEmergencyAlert: async function(recipientEmail, requestDetails) {
        console.log(`Sending emergency alert to ${recipientEmail}`);
        return await bloodLinkAPI.simulateAPICall('POST', '/email/send-alert', {
            recipientEmail,
            requestDetails
        });
    }
};

// =============================================
// Logging Utility
// =============================================

const Logger = {
    log: function(message, data = null) {
        console.log(`[BloodLink] ${message}`, data || '');
    },

    error: function(message, error = null) {
        console.error(`[BloodLink Error] ${message}`, error || '');
    },

    warn: function(message, data = null) {
        console.warn(`[BloodLink Warning] ${message}`, data || '');
    }
};

// =============================================
// Analytics (Simulated)
// =============================================

const Analytics = {
    trackEvent: function(eventName, eventData = {}) {
        Logger.log(`Event tracked: ${eventName}`, eventData);
        // Send to analytics service
    },

    trackPageView: function(pageName) {
        Logger.log(`Page view: ${pageName}`);
        // Send to analytics service
    },

    trackError: function(errorMessage, errorData = {}) {
        Logger.error(`Error tracked: ${errorMessage}`, errorData);
        // Send to error tracking service
    }
};

// =============================================
// Page Load Analytics
// =============================================

window.addEventListener('load', function() {
    const pageName = document.title;
    Analytics.trackPageView(pageName);
    Logger.log('Page loaded successfully');
});

// =============================================
// Error Handling
// =============================================

window.addEventListener('error', function(event) {
    Analytics.trackError(event.message, {
        filename: event.filename,
        lineno: event.lineno
    });
});

// =============================================
// Performance Monitoring
// =============================================

if (window.performance && window.performance.timing) {
    window.addEventListener('load', function() {
        const perfData = window.performance.timing;
        const pageLoadTime = perfData.loadEventEnd - perfData.navigationStart;
        Logger.log(`Page load time: ${pageLoadTime}ms`);
    });
}

// =============================================
// Debug Mode
// =============================================

const DebugMode = {
    enabled: false,

    enable: function() {
        this.enabled = true;
        window.bloodLinkAPI = bloodLinkAPI;
        window.StorageManager = StorageManager;
        window.BloodTypeUtils = BloodTypeUtils;
        console.log('BloodLink Debug Mode Enabled');
    },

    disable: function() {
        this.enabled = false;
        console.log('BloodLink Debug Mode Disabled');
    }
};

// Enable debug mode if ?debug=true in URL
if (new URLSearchParams(window.location.search).has('debug')) {
    DebugMode.enable();
}

// =============================================
// Export for use in other scripts
// =============================================

window.BloodLink = {
    API: bloodLinkAPI,
    Storage: StorageManager,
    Modal: ModalManager,
    Toast: showToast,
    BloodTypes: BloodTypeUtils,
    Logger: Logger,
    Analytics: Analytics,
    Email: EmailService,
    Debug: DebugMode
};
