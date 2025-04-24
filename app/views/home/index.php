<?php require APPROOT . '/views/includes/headers/header.php'; ?>



<section class="hero">
    <div class="container hero-content">
        <h1>Making a Difference, Together</h1>
        <p>Connect with causes that matter and make a real impact in people's lives</p>
        <div class="hero-buttons">
            <a href="<?php echo URLROOT; ?>/users/login" class="btn btn-secondary">Make a Donation</a>
            <a href="<?php echo URLROOT; ?>/pages/about" class="btn btn-secondary">Learn More</a>
        </div>
    </div>
</section>




<section class="features">
    <div class="container">
        <h2 class="section-title">Why Choose Hopefull</h2>
        <div class="features-grid">
            <div class="feature-card">
                <i class="fas fa-hand-holding-heart"></i>
                <h3>Direct Impact</h3>
                <p>Your donations directly reach those in need with full transparency</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-shield-alt"></i>
                <h3>Secure & Transparent</h3>
                <p>Safe payment processing and clear tracking of all donations</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-users"></i>
                <h3>Community Driven</h3>
                <p>Join a community of donors making real change happen</p>
            </div>
        </div>
    </div>
</section>


<!-- Updated CTA Section with side image and gradient background -->
<section class="cta">
    <div class="container cta-content">
        <h2>Ready to Make a Difference?</h2>
        <p>Join thousands of donors who are changing lives every day</p>
        <div class="cta-buttons">
          <a href="<?php echo URLROOT; ?>/users/register" class="btn btn-primary">Get Started</a>
        </div>  
    </div>
</section>

<section class="statistics">
    <div class="container">
        <h2 class="section-title">Our Impact</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-hand-holding-heart"></i>
                <div class="stat-number" data-target="100000">0</div>
                <h3>Donations Made</h3>
                <p>Supporting various causes</p>
            </div>
            
            <div class="stat-card">
                <i class="fas fa-users"></i>
                <div class="stat-number" data-target="50000">0</div>
                <h3>Lives Impacted</h3>
                <p>Across the country</p>
            </div>
            
            <div class="stat-card">
                <i class="fas fa-hand-holding-usd"></i>
                <div class="stat-number" data-target="5000000">0</div>
                <h3>Funds Raised</h3>
                <p>In Sri Lankan Rupees</p>
            </div>
            
            <div class="stat-card">
                <i class="fas fa-clock"></i>
                <div class="stat-number" data-target="1000">0</div>
                <h3>Volunteer Hours</h3>
                <p>Dedicated to helping others</p>
            </div>
        </div>
    </div>
</section>

<?php require APPROOT . '/views/includes/footer.php'; ?>

<!-- Slideshow JavaScript -->
<script>
let slideIndex = 1;
showSlides(slideIndex);

function plusSlides(n) {
    showSlides(slideIndex += n);
}

function currentSlide(n) {
    showSlides(slideIndex = n);
}

function showSlides(n) {
    let i;
    let slides = document.getElementsByClassName("slide");
    let dots = document.getElementsByClassName("dot");
    
    if (n > slides.length) {slideIndex = 1}
    if (n < 1) {slideIndex = slides.length}
    
    for (i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";
    }
    
    for (i = 0; i < dots.length; i++) {
        dots[i].className = dots[i].className.replace(" active", "");
    }
    
    slides[slideIndex-1].style.display = "block";
    dots[slideIndex-1].className += " active";
}

// Auto slideshow
function autoSlideShow() {
    plusSlides(1);
    setTimeout(autoSlideShow, 5000); // Change image every 5 seconds
}

// Start the auto slideshow
setTimeout(autoSlideShow, 5000);

// Counter animation for statistics
document.addEventListener('DOMContentLoaded', function() {
    const statNumbers = document.querySelectorAll('.stat-number');
    
    const observerOptions = {
        threshold: 0.5
    };
    
    const observer = new IntersectionObserver(function(entries, observer) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const target = parseInt(entry.target.getAttribute('data-target'));
                const element = entry.target;
                let count = 0;
                const duration = 2000; // ms
                const interval = Math.max(50, duration / target);
                const increment = Math.ceil(target / (duration / interval));
                
                const timer = setInterval(() => {
                    count += increment;
                    if (count >= target) {
                        element.textContent = target.toLocaleString();
                        clearInterval(timer);
                    } else {
                        element.textContent = count.toLocaleString();
                    }
                }, interval);
                
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    statNumbers.forEach(number => {
        observer.observe(number);
    });
});
</script>