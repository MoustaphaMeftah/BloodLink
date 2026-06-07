document.addEventListener('DOMContentLoaded', function() {
    initSidebar();
    initScrollAnimations();
    initNavbarScroll();
});

function initSidebar() {
    const sidebar = document.getElementById('dashboardSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggleBtns = document.querySelectorAll('#sidebarToggle, #mobileSidebarToggle');

    if (!sidebar) return;

    toggleBtns.forEach(btn => {
        if (btn) {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                sidebar.classList.toggle('open');
                if (overlay) overlay.classList.toggle('show');
            });
        }
    });

    if (overlay) {
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('open');
            overlay.classList.remove('show');
        });
    }

    const sidebarLinks = sidebar.querySelectorAll('.sidebar-link');
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 991.98) {
                sidebar.classList.remove('open');
                if (overlay) overlay.classList.remove('show');
            }
        });
    });
}

function initScrollAnimations() {
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    document.querySelectorAll('.fade-in-up, .feature-card-modern, .step-card, .blood-type-item, .stat-item').forEach(el => {
        if (!el.classList.contains('fade-in-up')) {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        }
        observer.observe(el);
    });
}

function initNavbarScroll() {
    const navbar = document.querySelector('.navbar');
    if (!navbar) return;

    window.addEventListener('scroll', function() {
        if (window.scrollY > 10) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
}

function showToast(message, type, duration) {
    type = type || 'info';
    duration = duration || 3000;

    const icons = {
        success: 'fa-check-circle',
        danger: 'fa-exclamation-circle',
        warning: 'fa-exclamation-triangle',
        info: 'fa-info-circle'
    };

    const toast = document.createElement('div');
    toast.style.cssText = `
        position: fixed; top: 80px; right: 20px; z-index: 9999;
        background: white; border-radius: 12px; padding: 1rem 1.25rem;
        box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        border-left: 4px solid ${type === 'success' ? '#28a745' : type === 'danger' ? '#dc3545' : type === 'warning' ? '#ffc107' : '#17a2b8'};
        min-width: 300px; max-width: 420px;
        animation: slideIn 0.3s ease-out;
        display: flex; align-items: center; gap: 12px;
        font-size: 0.9rem; color: #1a1a2e; font-weight: 500;
    `;
    toast.innerHTML = `
        <i class="fas ${icons[type] || icons.info}" style="font-size:1.2rem;color:${type === 'success' ? '#28a745' : type === 'danger' ? '#dc3545' : type === 'warning' ? '#ffc107' : '#17a2b8'};"></i>
        <span style="flex:1;">${message}</span>
        <button onclick="this.parentElement.remove()" style="background:none;border:none;color:#adb5bd;cursor:pointer;padding:0;font-size:1rem;">&times;</button>
    `;

    document.body.appendChild(toast);

    setTimeout(function() {
        toast.style.animation = 'slideOut 0.3s ease-out forwards';
        setTimeout(function() { toast.remove(); }, 300);
    }, duration);
}

const styleSheet = document.createElement('style');
styleSheet.textContent = `
    @keyframes slideIn {
        from { opacity: 0; transform: translateX(100px); }
        to { opacity: 1; transform: translateX(0); }
    }
    @keyframes slideOut {
        from { opacity: 1; transform: translateX(0); }
        to { opacity: 0; transform: translateX(100px); }
    }
`;
document.head.appendChild(styleSheet);

window.BloodLink = {
    Toast: showToast,
};
