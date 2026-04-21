<?php
include 'includes/header.php';

$success = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Basic mock logic for contact form submission
    $success = "Thank you for reaching out. Our support team will get back to you shortly.";
}
?>

<!-- Page Header -->
<div class="py-5" style="background: url('/sanjanaraj/assets/images/images/img13.jpeg') center/cover; position: relative;">
    <div style="position: absolute; top:0; left:0; width:100%; height:100%; background:rgba(10,61,145,0.75);"></div>
    <div class="container position-relative z-1 py-4 text-center">
        <h1 class="display-4 fw-bold text-white mb-2">Contact Us</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
                <li class="breadcrumb-item"><a href="index.php" class="text-white-50 text-decoration-none">Home</a></li>
                <li class="breadcrumb-item active text-white" aria-current="page">Contact</li>
            </ol>
        </nav>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-5" data-aos="fade-right">
                <div class="pe-lg-4">
                    <h3 class="fw-bold mb-4" style="color: var(--fin-blue);">Get in Touch</h3>
                    <p class="text-muted mb-5">Have questions about our business opportunity, products, or compensation plan? Drop us a message or visit our corporate office.</p>
                    
                    <div class="d-flex mb-4 align-items-start">
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center text-primary border me-3 flex-shrink-0" id="icon-contact">
                            <i class="fas fa-building"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">Corporate Office (Coimbatore)</h5>
                            <p class="text-muted mb-0">204, Sanjay Raaj Towers, 100 Feet Road, Tatabad, Coimbatore - 641012</p>
                        </div>
                    </div>
                    
                    <div class="d-flex mb-4 align-items-start">
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center text-primary border me-3 flex-shrink-0" id="icon-contact">
                            <i class="fas fa-building"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">Chennai Office</h5>
                            <p class="text-muted mb-0">146, Anna Salai, Little Mount, Saidapet, Chennai - 600015</p>
                        </div>
                    </div>

                    <div class="d-flex mb-4 align-items-start">
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center text-primary border me-3 flex-shrink-0" id="icon-contact">
                            <i class="fas fa-building"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">Madurai Office</h5>
                            <p class="text-muted mb-0">No. 2/1, Pranav Plaza, Appadurai Nagar, Koodal Nagar, Madurai - 625018</p>
                        </div>
                    </div>

                    <div class="d-flex mb-4 align-items-start">
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center text-primary border me-3 flex-shrink-0" id="icon-contact">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">Sivaganga Branch</h5>
                            <p class="text-muted mb-0">No. 5067, Kurinji Nagar, Kalaiyarkovil, Somanathamangalam,<br>PO: Kalayarkoil, Dist: Sivaganga,<br>Tamil Nadu - 630551</p>
                        </div>
                    </div>

                    <div class="mt-4 p-3 bg-light rounded border">
                        <h6 class="fw-bold mb-2">Operating Regions:</h6>
                        <span class="badge bg-secondary mb-1">Tamil Nadu</span>
                        <span class="badge bg-secondary mb-1">Andhra Pradesh</span>
                        <span class="badge bg-secondary mb-1">Telangana</span>
                        <span class="badge bg-secondary mb-1">Kerala</span>
                        <span class="badge bg-secondary mb-1">Maharashtra</span>
                        <span class="badge bg-secondary mb-1">Puducherry</span>
                    </div>

                    <div class="mt-4 p-3 bg-white rounded border border-warning shadow-sm">
                        <h6 class="fw-bold mb-2 text-warning"><i class="fas fa-user-shield me-2"></i>Grievance Redressal Officer</h6>
                        <p class="mb-1 text-dark fw-bold">Mr. Karthikeyan</p>
                        <p class="mb-0 small text-muted"><i class="fas fa-phone-alt me-2"></i> +91-422-4213050</p>
                        <p class="mb-0 small text-muted"><i class="fas fa-envelope me-2"></i> karthikeyan.n@tranzindia.in</p>
                    </div>
                </div>

                <!-- Google Maps Embed placeholder -->
                <div class="mt-4 rounded-4 overflow-hidden border shadow-sm">
                    <iframe src="https://maps.google.com/maps?q=Kalayarkoil,+Sivaganga,+Tamil+Nadu&t=&z=14&ie=UTF8&iwloc=&output=embed" width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>

            <div class="col-lg-7" data-aos="fade-left">
                <div class="card card-custom p-5 bg-white border-0">
                    <h3 class="fw-bold mb-4" style="color: var(--fin-orange);">Send a Message</h3>
                    
                    <?php if($success): ?>
                        <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i> <?php echo $success; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="row g-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-muted">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control py-2 bg-light border-0" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-muted">Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control py-2 bg-light border-0" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold small text-muted">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control py-2 bg-light border-0" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold small text-muted">Subject <span class="text-danger">*</span></label>
                                <select class="form-select py-2 bg-light border-0" required>
                                    <option value="">Select a reason</option>
                                    <option value="General Inquiry">General Inquiry</option>
                                    <option value="Join Future Network">Join Future Network</option>
                                    <option value="Product Delivery">Product Delivery</option>
                                    <option value="Payout Issues">Payout Issues</option>
                                </select>
                            </div>
                            <div class="col-12 mb-4">
                                <label class="form-label fw-bold small text-muted">Your Message <span class="text-danger">*</span></label>
                                <textarea class="form-control py-2 bg-light border-0" rows="5" required></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary-custom px-5 py-3 w-100 fs-5"><i class="fas fa-paper-plane me-2"></i> Send Message</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    #icon-contact {
        width: 50px; 
        height: 50px; 
        font-size: 1.25rem;
    }
</style>

<?php include 'includes/footer.php'; ?>
