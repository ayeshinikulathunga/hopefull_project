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


<section class="inquiries">
    <div class="container">
        <h2 class="section-title">Contact Us</h2>
        <p class="inquiries-intro">Have questions or want to learn more? Fill out the form below and we'll get back to you as soon as possible.</p>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($_GET['success']); ?>
            </div>
        <?php endif; ?>
        
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="form-container">
                    <form action="<?php echo URLROOT; ?>/visitorInquiries/submit" method="POST" id="inquiryForm">
                        <div class="form-group mb-3">
                            <label for="name">Your Name <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="email">Email Address <span class="text-danger">*</span></label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="phone">Phone Number</label>
                            <input type="text" id="phone" name="phone" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label for="subject">Subject <span class="text-danger">*</span></label>
                            <input type="text" id="subject" name="subject" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="message">Your Message <span class="text-danger">*</span></label>
                            <textarea id="message" name="message" rows="5" class="form-control" required></textarea>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-submit">Submit Inquiry</button>
                        </div>
                    </form>
                    <br/>
                    <p class="inquiries-intro">You will get an email within 1-3 business days. Make sure to check!</p>
                </div>
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
