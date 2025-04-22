document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const navWrapper = document.getElementById('navWrapper');

    mobileMenuBtn.addEventListener('click', function() {
        navWrapper.classList.toggle('active');
        document.body.classList.toggle('no-scroll');
    });
});

// Animate statistics numbers
function animateStats() {
    const stats = document.querySelectorAll('.stat-number');
    
    stats.forEach(stat => {
        const target = parseInt(stat.getAttribute('data-target'));
        const duration = 2000; // 2 seconds
        const steps = 100;
        const increment = target / steps;
        let current = 0;
        
        const updateCount = () => {
            const value = Math.floor(current);
            stat.textContent = value.toLocaleString();
            current += increment;
            
            if (current <= target) {
                setTimeout(updateCount, duration / steps);
            } else {
                stat.textContent = target.toLocaleString();
            }
        };
        
        updateCount();
    });
}

// Intersection Observer for triggering animation when in view
const observerOptions = {
    threshold: 0.25
};

const statsObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            animateStats();
            statsObserver.unobserve(entry.target);
        }
    });
}, observerOptions);

// Observe statistics section
const statsSection = document.querySelector('.statistics');
if (statsSection) {
    statsObserver.observe(statsSection);
}

document.addEventListener('DOMContentLoaded', function() {
    // Profile Dropdown
    const profileBtn = document.querySelector('.profile-btn');
    const profileDropdown = document.querySelector('.profile-dropdown');

    if(profileBtn && profileDropdown) {
        profileBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            profileDropdown.classList.toggle('active');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if(!profileDropdown.contains(e.target)) {
                profileDropdown.classList.remove('active');
            }
        });
    }
});

