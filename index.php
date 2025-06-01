<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>PDFSimba | Convert Documents Online</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="icon" href="./Media/book3.png" type="image/x-icon">
    <link rel="stylesheet" href="./CSS/index.css" />
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top">
  <div class="container">
    <a class="navbar-brand" href="index.php"><i class="fa-solid fa-file-lines brand-icon"></i>PDFSimba</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav mx-auto">
        <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Tools</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#">PDF to Word</a></li>
            <li><a class="dropdown-item" href="#">Excel to PDF</a></li>
            <li><a class="dropdown-item" href="#">Image to Text</a></li>
          </ul>
        </li>
       <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
  <li class="nav-item"><a class="nav-link" href="#use-cases">Use Cases</a></li>
  <li class="nav-item"><a class="nav-link" href="#faqs">FAQs</a></li>
      </ul>
      <div class="d-flex align-items-center">
        <a href="#" class="text-dark me-3"><i class="fa-regular fa-user user-icon"></i></a>
        <a href="#" class="btn try-btn">TRY FOR FREE</a>
      </div>
    </div>
  </div>
</nav>

<!-- Hero Section -->
<section class="hero-section">
  <div class="container">
    <div class="row align-items-center">
      <!-- Text -->
      <div class="col-lg-6 hero-text">
        <p class="hero-subtitle img-underlined">Start to new journey</p>
        <h1>Best <span class="highlight">online</span> tools to convert your documents</h1>
        <p>Fast and secure document conversion: PDF to Word, Excel, PPT and more. Built for professionals and students.</p>
        <a href="#tools" class="btn btn-dark-rounded mt-3">Upload Document</a>
      </div>

      <!-- Image -->
      <div class="col-lg-6 text-center position-relative">
        <img src="./Media/b1.png" alt="Hero" class="hero-img img-fluid">
        <div class="icon-badge"><i class="fa-solid fa-lightbulb"></i></div>
      </div>
    </div>
  </div>

  <!-- Shapes -->
  <div class="shape-circle shape-orange-ring"></div>
  <div class="shape-circle shape-teal"></div>
  <div class="shape-circle shape-pink"></div>
</section>

<!-- Features Section -->
<section class="py-5 bg-white">
  <div class="container">
    <div class="row align-items-center">
      <!-- Left Text Content with more explanation -->
      <div class="col-md-6 mb-4 mb-md-0">
        <span class="badge mb-2 px-3 py-1 features-title">What We Do</span>
        <h2 class="fw-bold">Start Improving Your Document Workflow Today</h2>
        <p class="text-muted">
          Whether you're converting, editing, or securing your PDF documents, our all-in-one platform offers
          the tools you need to stay efficient. Save time and effort by using streamlined solutions tailored
          for your everyday business and personal document needs.
        </p>
        <p class="text-muted">
          Our services are built for professionals, students, and teams who want to simplify their digital
          document tasks 
        </p>
         <a href="#" class="btn upload-document">Upload Document</a>
      </div>

      <!-- Right Features Grid with PDF tools -->
      <div class="col-md-6">
        <div class="row g-4">
          <!-- Feature 1 -->
          <div class="col-6 d-flex align-items-start">
            <div class="icon-box bg-primary bg-opacity-25 text-primary me-3">
              <i class="bi bi-file-earmark-word fs-4"></i>
            </div>
            <div>
              <div class="fw-bold">Word to PDF</div>
              <small class="text-muted">Convert DOCX files easily</small>
            </div>
          </div>

          <!-- Feature 2 -->
          <div class="col-6 d-flex align-items-start">
            <div class="icon-box bg-success bg-opacity-25 text-success me-3">
              <i class="bi bi-file-earmark-excel fs-4"></i>
            </div>
            <div>
              <div class="fw-bold">Excel to PDF</div>
              <small class="text-muted">Perfect formatting every time</small>
            </div>
          </div>

          <!-- Feature 3 -->
          <div class="col-6 d-flex align-items-start">
            <div class="icon-box bg-danger bg-opacity-25 text-danger me-3">
              <i class="bi bi-file-earmark-ppt fs-4"></i>
            </div>
            <div>
              <div class="fw-bold">PowerPoint to PDF</div>
              <small class="text-muted">Slides preserved in high quality</small>
            </div>
          </div>

          <!-- Feature 4 -->
          <div class="col-6 d-flex align-items-start">
            <div class="icon-box bg-warning bg-opacity-25 text-warning me-3">
              <i class="bi bi-filetype-pdf fs-4"></i>
            </div>
            <div>
              <div class="fw-bold">Merge PDFs</div>
              <small class="text-muted">Combine multiple files fast</small>
            </div>
          </div>

          <!-- Feature 5 -->
          <div class="col-6 d-flex align-items-start">
            <div class="icon-box bg-info bg-opacity-25 text-info me-3">
              <i class="bi bi-shield-lock-fill fs-4"></i>
            </div>
            <div>
              <div class="fw-bold">Protect PDFs</div>
              <small class="text-muted">Encrypt with password</small>
            </div>
          </div>

          <!-- Feature 6 -->
          <div class="col-6 d-flex align-items-start">
            <div class="icon-box bg-secondary bg-opacity-25 text-secondary me-3">
              <i class="bi bi-unlock-fill fs-4"></i>
            </div>
            <div>
              <div class="fw-bold">Unlock PDFs</div>
              <small class="text-muted">Remove restrictions easily</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- PDF Tools Grid Section -->
<section class="pdf-tools-section py-5 bg-light">
  <div class="container">
    <h2 class="text-center fw-bold mb-5">All-in-One PDF Tools</h2>
    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-6 g-3">
      
      <!-- Tool -->
      <div class="col">
  <a href="#" class="pdf-tool-box text-center" data-bs-toggle="modal" data-bs-target="#wordToPdfModal">
    <i class="bi bi-file-earmark-word text-primary fs-3 mb-2"></i>
    <div class="tool-name">Word to PDF</div>
  </a>
</div>


      <div class="col">
  <a href="#" 
     class="pdf-tool-box text-center"
     data-bs-toggle="modal"
     data-bs-target="#excelToPdfModal">
    <i class="bi bi-file-earmark-excel text-success fs-3 mb-2"></i>
    <div class="tool-name">Excel to PDF</div>
  </a>
</div>


  <div class="col">
  <a href="#" class="pdf-tool-box text-center" data-bs-toggle="modal" data-bs-target="#pptToPdfModal">
    <i class="bi bi-file-earmark-ppt text-danger fs-3 mb-2"></i>
    <div class="tool-name">PowerPoint to PDF</div>
  </a>
</div>


<div class="col">
  <a href="#" class="pdf-tool-box text-center" data-bs-toggle="modal" data-bs-target="#jpgToPdfModal">
    <i class="bi bi-image text-purple fs-3 mb-2"></i>
    <div class="tool-name">JPG to PDF</div>
  </a>
</div>


 <div class="col">
  <a href="#" class="pdf-tool-box text-center" data-bs-toggle="modal" data-bs-target="#cadToPdfModal">
    <i class="bi bi-vector-pen text-pink fs-3 mb-2"></i>
    <div class="tool-name">AutoCAD to PDF</div>
  </a>
</div>

<div class="col">
  <a href="#" class="pdf-tool-box text-center" data-bs-toggle="modal" data-bs-target="#openofficeToPdfModal">
    <i class="bi bi-journal-code text-info fs-3 mb-2"></i>
    <div class="tool-name">OpenOffice to PDF</div>
  </a>
</div>


      <div class="col">
        <a href="#" class="pdf-tool-box text-center">
          <i class="bi bi-book text-warning fs-3 mb-2"></i>
          <div class="tool-name">eBooks to PDF</div>
        </a>
      </div>

      <div class="col">
        <a href="#" class="pdf-tool-box text-center">
          <i class="bi bi-apple text-dark fs-3 mb-2"></i>
          <div class="tool-name">iWork to PDF</div>
        </a>
      </div>

    <div class="col">
  <a href="#" class="pdf-tool-box text-center" data-bs-toggle="modal" data-bs-target="#pdfToWordModal">
    <i class="bi bi-file-earmark-word text-primary fs-3 mb-2"></i>
    <div class="tool-name">PDF to Word</div>
  </a>
</div>


   <div class="col">
  <a href="#" class="pdf-tool-box text-center" data-bs-toggle="modal" data-bs-target="#pdfToExcelModal">
    <i class="bi bi-file-earmark-excel text-success fs-3 mb-2"></i>
    <div class="tool-name">PDF to Excel</div>
  </a>
</div>

<div class="col">
  <a href="#" class="pdf-tool-box text-center" data-bs-toggle="modal" data-bs-target="#pdfToPptModal">
    <i class="bi bi-file-earmark-ppt text-danger fs-3 mb-2"></i>
    <div class="tool-name">PDF to PowerPoint</div>
  </a>
</div>

      <div class="col">
        <a href="#" class="pdf-tool-box text-center">
          <i class="bi bi-image text-purple fs-3 mb-2"></i>
          <div class="tool-name">PDF to JPG</div>
        </a>
      </div>

      <div class="col">
        <a href="#" class="pdf-tool-box text-center">
          <i class="bi bi-image text-indigo fs-3 mb-2"></i>
          <div class="tool-name">PDF to PNG</div>
        </a>
      </div>

      <div class="col">
        <a href="#" class="pdf-tool-box text-center">
          <i class="bi bi-file-earmark-text text-secondary fs-3 mb-2"></i>
          <div class="tool-name">PDF to PDF/A</div>
        </a>
      </div>

      <div class="col">
        <a href="#" class="pdf-tool-box text-center">
          <i class="bi bi-files text-orange fs-3 mb-2"></i>
          <div class="tool-name">Merge PDF</div>
        </a>
      </div>

      <div class="col">
        <a href="#" class="pdf-tool-box text-center">
          <i class="bi bi-scissors text-orange fs-3 mb-2"></i>
          <div class="tool-name">Split PDF</div>
        </a>
      </div>

      <div class="col">
        <a href="#" class="pdf-tool-box text-center">
          <i class="bi bi-shield-lock-fill text-success fs-3 mb-2"></i>
          <div class="tool-name">Protect PDF</div>
        </a>
      </div>

      <div class="col">
        <a href="#" class="pdf-tool-box text-center">
          <i class="bi bi-unlock-fill text-danger fs-3 mb-2"></i>
          <div class="tool-name">Unlock PDF</div>
        </a>
      </div>

    </div>
  </div>
</section>


<!-- Experience Section Start -->
<section class="py-5 bg-white">
  <div class="container experience-section">
    <div class="row align-items-center">
      <!-- Left Side -->
      <div class="col-md-6 text-center position-relative">
        <div class="image-stack">
          <img src="./Media/document2.png" alt="Document 1" class="image1">
          <img src="./Media/document1.jpg" alt="Document 2" class="image2">
          <div class="experience-badge">
            <h4 class="fw-bold">16</h4>
            <div>
              <div class="small">Years of</div>
              <div class="fw-bold">Experience</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Side -->
      <div class="col-md-6 mt-5 mt-md-0">
        <p class="text-uppercase  fw-bold">Get to know us</p>
        <h2 class="fw-bold mb-3">Master your documents from anywhere</h2>
        <p class="text-muted mb-4">
          Easily convert, edit, merge, split, and manage your documents all in one place. Whether you're on the go or at your desk, streamline your workflow with our powerful document tools.
        </p>

        <div class="row mb-4">
          <div class="col-6 mb-2">
            <div class="d-flex align-items-center">
              <div class="me-2 bg-danger text-white feature-icon"><i class="bi bi-check-circle-fill"></i></div>
              <span>Expert support</span>
            </div>
          </div>
          <div class="col-6 mb-2">
            <div class="d-flex align-items-center">
              <div class="me-2 bg-primary text-white feature-icon"><i class="bi bi-laptop-fill"></i></div>
              <span>Online tools</span>
            </div>
          </div>
          <div class="col-6 mb-2">
            <div class="d-flex align-items-center">
              <div class="me-2 bg-warning text-white feature-icon"><i class="bi bi-infinity"></i></div>
              <span>Lifetime access</span>
            </div>
          </div>
          <div class="col-6 mb-2">
            <div class="d-flex align-items-center">
              <div class="me-2 bg-success text-white feature-icon"><i class="bi bi-graph-up-arrow"></i></div>
              <span>Great results</span>
            </div>
          </div>
        </div>

        <a href="#" class="btn btn-dark px-4 py-2">Discover More</a>
      </div>
    </div>
  </div>
</section>
<!-- Section End -->

<!-- Use Case Section -->
<section class="use-cases py-5 bg-white">
  <div class="container">
    <!-- Tabs -->
<div class="d-flex justify-content-center flex-wrap gap-3 mb-5 use-case-tabs">
  <button class="btn btn-outline-success active">To Word</button>
  <button class="btn btn-outline-success">To PDF</button>
  <button class="btn btn-outline-success">To Excel</button>
  <button class="btn btn-outline-success">To JPG</button>
  <button class="btn btn-outline-success">Merge PDF</button>
  <button class="btn btn-outline-success">Unlock PDF</button>
</div>


    <!-- Cards -->
    <div class="row g-4">
      <!-- Card 1 -->
      <div class="col-md-4">
        <div class="card h-100 shadow-sm">
          <img src="./Media/students.jpg" class="card-img-top" alt="Student Use Case">
          <div class="card-body">
            <h5 class="card-title">Students</h5>
            <p class="card-text text-muted">Easily convert assignments and course materials into shareable PDFs.</p>
            <div class="d-flex justify-content-between align-items-center">
              <span class="text-success fw-bold">Free Access</span>
              <div class="text-warning small">★★★★★</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Card 2 -->
      <div class="col-md-4">
        <div class="card h-100 shadow-sm">
          <img src="./Media/legalprofessional.png" class="card-img-top" alt="Legal Professionals Use Case">
          <div class="card-body">
            <h5 class="card-title">Legal Professionals</h5>
            <p class="card-text text-muted">Securely manage contracts and client documents with encryption tools.</p>
            <div class="d-flex justify-content-between align-items-center">
              <span class="text-success fw-bold">Premium Tools</span>
              <div class="text-warning small">★★★★★</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Card 3 -->
      <div class="col-md-4">
        <div class="card h-100 shadow-sm">
          <img src="./Media/educators.jpg" class="card-img-top" alt="Educators Use Case">
          <div class="card-body">
            <h5 class="card-title">Educators</h5>
            <p class="card-text text-muted">Convert lecture notes and quizzes into polished PDF handouts.</p>
            <div class="d-flex justify-content-between align-items-center">
              <span class="text-success fw-bold">Time-Saving</span>
              <div class="text-warning small">★★★★☆</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Card 4 -->
      <div class="col-md-4">
        <div class="card h-100 shadow-sm">
          <img src="./Media/remoteteam.jpeg" class="card-img-top" alt="Remote Teams Use Case">
          <div class="card-body">
            <h5 class="card-title">Remote Teams</h5>
            <p class="card-text text-muted">Collaborate on PDFs in real-time with easy merge and edit features.</p>
            <div class="d-flex justify-content-between align-items-center">
              <span class="text-success fw-bold">Collaborative</span>
              <div class="text-warning small">★★★★★</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Card 5 -->
      <div class="col-md-4">
        <div class="card h-100 shadow-sm">
          <img src="./Media/marketing.jpg" class="card-img-top" alt="Marketing Teams Use Case">
          <div class="card-body">
            <h5 class="card-title">Marketing Teams</h5>
            <p class="card-text text-muted">Share branded presentations and pitch decks with clients in PDF format.</p>
            <div class="d-flex justify-content-between align-items-center">
              <span class="text-success fw-bold">Professional</span>
              <div class="text-warning small">★★★★☆</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Card 6 -->
      <div class="col-md-4">
        <div class="card h-100 shadow-sm">
          <img src="./Media/freelancers.jpg" class="card-img-top" alt="Freelancers Use Case">
          <div class="card-body">
            <h5 class="card-title">Freelancers</h5>
            <p class="card-text text-muted">Send portfolios and invoices quickly in secure PDF formats.</p>
            <div class="d-flex justify-content-between align-items-center">
              <span class="text-success fw-bold">Secure</span>
              <div class="text-warning small">★★★★★</div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- FAQs Section -->
<section class="faq-section py-5 bg-light">
  <div class="container">
    <h2 class="text-center mb-4 fw-bold">Frequently Asked Questions</h2>
    <p class="text-center text-muted mb-5">Everything you need to know about using our PDF tools.</p>

    <div class="accordion" id="faqAccordion">
      <!-- FAQ Item 1 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="faqHeading1">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse1" aria-expanded="true" aria-controls="faqCollapse1">
            What file types can I convert to PDF?
          </button>
        </h2>
        <div id="faqCollapse1" class="accordion-collapse collapse show" aria-labelledby="faqHeading1" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            You can convert Word, Excel, PowerPoint, JPG, AutoCAD, eBooks, and many other formats into PDF using our tool.
          </div>
        </div>
      </div>

      <!-- FAQ Item 2 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="faqHeading2">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse2" aria-expanded="false" aria-controls="faqCollapse2">
            Is there a limit to how many PDFs I can convert?
          </button>
        </h2>
        <div id="faqCollapse2" class="accordion-collapse collapse" aria-labelledby="faqHeading2" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            No, there is no daily limit. You can convert as many files as you like for free.
          </div>
        </div>
      </div>

      <!-- FAQ Item 3 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="faqHeading3">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse3" aria-expanded="false" aria-controls="faqCollapse3">
            Are my files secure?
          </button>
        </h2>
        <div id="faqCollapse3" class="accordion-collapse collapse" aria-labelledby="faqHeading3" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            Absolutely. We use SSL encryption and delete all files permanently from our servers after 1 hour.
          </div>
        </div>
      </div>

      <!-- FAQ Item 4 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="faqHeading4">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse4" aria-expanded="false" aria-controls="faqCollapse4">
            Can I merge multiple PDFs into one?
          </button>
        </h2>
        <div id="faqCollapse4" class="accordion-collapse collapse" aria-labelledby="faqHeading4" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            Yes, our Merge PDF tool allows you to combine multiple PDF documents into a single file in seconds.
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Footer Section -->
<footer class="footer bg-dark text-white pt-5 pb-4">
  <div class="container">
    <div class="row">
      <!-- Logo & Description -->
      <div class="col-md-4 mb-4">
        <h4 class="fw-bold">PDFSimba</h4>
        <p class="text-muted">Convert, manage, and secure your documents with ease. Trusted by professionals and students worldwide.</p>
      </div>

      <!-- Navigation Links -->
      <div class="col-md-2 mb-4">
        <h6 class="fw-semibold mb-3">Quick Links</h6>
        <ul class="list-unstyled">
          <li><a href="#" class="text-muted text-decoration-none">Home</a></li>
          <li><a href="#" class="text-muted text-decoration-none">Tools</a></li>
          <li><a href="#" class="text-muted text-decoration-none">Pricing</a></li>
          <li><a href="#" class="text-muted text-decoration-none">About</a></li>
        </ul>
      </div>

      <!-- Resources Links -->
      <div class="col-md-2 mb-4">
        <h6 class="fw-semibold mb-3">Resources</h6>
        <ul class="list-unstyled">
          <li><a href="#" class="text-muted text-decoration-none">FAQs</a></li>
          <li><a href="#" class="text-muted text-decoration-none">Privacy Policy</a></li>
          <li><a href="#" class="text-muted text-decoration-none">Terms of Use</a></li>
          <li><a href="#" class="text-muted text-decoration-none">Help Center</a></li>
        </ul>
      </div>

      <!-- Newsletter & Social -->
      <div class="col-md-4 mb-4">
        <h6 class="fw-semibold mb-3">Stay Updated</h6>
        <form class="d-flex mb-3">
          <input type="email" class="form-control me-2" placeholder="Your email" />
          <button class="btn btn-success">Subscribe</button>
        </form>
        <div>
          <a href="#" class="text-muted me-3 fs-5"><i class="bi bi-facebook"></i></a>
          <a href="#" class="text-muted me-3 fs-5"><i class="bi bi-twitter"></i></a>
          <a href="#" class="text-muted me-3 fs-5"><i class="bi bi-instagram"></i></a>
          <a href="#" class="text-muted fs-5"><i class="bi bi-linkedin"></i></a>
        </div>
      </div>
    </div>

    <!-- Bottom -->
    <hr class="border-light">
    <div class="text-center text-muted small">
  © <span id="currentYear"></span> PDFSimba. All rights reserved.
</div>

<script>
  document.getElementById("currentYear").textContent = new Date().getFullYear();
</script>

  </div>
</footer>


<!-- Tool Conversion Modals -->
<!-- Word to PDF Modal -->
<div class="modal fade" id="wordToPdfModal" tabindex="-1" aria-labelledby="wordToPdfLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="wordToPdfForm" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="wordToPdfLabel">Convert Word to PDF</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="file" name="word_file" accept=".doc,.docx" class="form-control mb-3" required>
          <button type="submit" class="btn btn-success w-100">Convert</button>
        </div>
    <!-- Inside modal-body, right after the submit button -->
<div id="loadingIndicator" class="text-center my-3" style="display: none;">
  <div class="spinner-border text-primary" role="status">
    <span class="visually-hidden">Converting...</span>
  </div>
  <p class="mt-2">Converting... Please wait</p>
</div>

<!-- Preview Container (initially hidden) -->
<div id="previewContainer" style="display: none;">
  <iframe id="pdfPreview" style="width:95%; height:400px; margin-left:4%;" frameborder="0"></iframe>
  <a id="downloadLink" href="#" class="btn btn-success w-80 mt-3" style=" margin-left:4%; margin-bottom:3%" download>Download PDF</a>
</div>
      </div>
    </form>
  </div>
</div>


<!-- Excel to PDF Modal -->
<div class="modal fade" id="excelToPdfModal" tabindex="-1" aria-labelledby="excelToPdfLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="excelToPdfForm" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="excelToPdfLabel">Convert Excel to PDF</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="file" name="excel_file" accept=".xls,.xlsx" class="form-control mb-3" required>
          <button type="submit" class="btn btn-success w-100">Convert</button>

          <div id="excelLoadingIndicator" class="text-center my-3" style="display: none;">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Converting...</span>
            </div>
            <p class="mt-2">Converting... Please wait</p>
          </div>

          <div id="excelPreviewContainer" style="display: none;">
            <iframe id="excelPdfPreview" style="width:95%; height:400px; margin-left:4%;" frameborder="0"></iframe>
            <a id="excelDownloadLink" href="#" class="btn btn-success w-80 mt-3" style="margin-left:4%; margin-bottom:3%" download>Download PDF</a>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- PowerPoint to PDF Modal -->
<div class="modal fade" id="pptToPdfModal" tabindex="-1" aria-labelledby="pptToPdfLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="pptToPdfForm" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="pptToPdfLabel">Convert PowerPoint to PDF</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="file" name="ppt_file" accept=".ppt,.pptx" class="form-control mb-3" required>
          <button type="submit" class="btn btn-danger w-100">Convert</button>

          <div id="pptLoadingIndicator" class="text-center my-3" style="display: none;">
            <div class="spinner-border text-danger" role="status">
              <span class="visually-hidden">Converting...</span>
            </div>
            <p class="mt-2">Converting... Please wait</p>
          </div>

          <div id="pptPreviewContainer" style="display: none;">
            <iframe id="pptPdfPreview" style="width:95%; height:400px; margin-left:4%;" frameborder="0"></iframe>
            <a id="pptDownloadLink" href="#" class="btn btn-danger w-80 mt-3" style="margin-left:4%; margin-bottom:3%" download>Download PDF</a>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- JPG to PDF Modal -->
<div class="modal fade" id="jpgToPdfModal" tabindex="-1" aria-labelledby="jpgToPdfModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <form id="jpgToPdfForm" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="jpgToPdfModalLabel">Convert JPG to PDF</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <input type="file" name="jpg_file" accept=".jpg,.jpeg,.png" class="form-control" required multiple>
          </div>

          <!-- Purple Convert Button -->
          <div class="d-grid mb-3">
            <button type="submit" class="btn" style="background-color: #6f42c1; color: white;">Convert</button>
          </div>

          <!-- Purple Loading Indicator -->
          <div id="jpgLoadingIndicator" class="text-center my-3" style="display: none;">
            <div class="spinner-border" style="color: #6f42c1;" role="status">
              <span class="visually-hidden">Converting...</span>
            </div>
            <p class="mt-2" style="color: #6f42c1;">Converting... Please wait</p>
          </div>

          <!-- PDF Preview and Download -->
          <div id="jpgPreviewContainer" style="display: none;">
            <iframe id="jpgPdfPreview" width="100%" height="500px" frameborder="0"></iframe>
            <a id="jpgDownloadLink" class="btn mt-3" style="background-color: #6f42c1; color: white;" download>Download PDF</a>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- AutoCAD to PDF Modal -->
<div class="modal fade" id="cadToPdfModal" tabindex="-1" aria-labelledby="cadToPdfModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <form id="cadToPdfForm">
        <div class="modal-header">
          <h5 class="modal-title" id="cadToPdfModalLabel">Convert AutoCAD to PDF</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <input type="file" name="cad_file" accept=".dwg,.dxf" class="form-control" required>
          </div>
          <div id="cadLoadingIndicator" class="text-center my-3" style="display: none;">
            <div class="spinner-border text-pink" role="status">
              <span class="visually-hidden">Converting...</span>
            </div>
            <p class="mt-2 text-pink">Converting... Please wait</p>
          </div>
          <div id="cadPreviewContainer" style="display: none;">
            <iframe id="cadPdfPreview" width="100%" height="500px" frameborder="0"></iframe>
            <a id="cadDownloadLink" class="btn btn-pink mt-3" style="background-color: #d63384; color: white;" download>Download PDF</a>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-pink" style="background-color: #d63384; color: white;">Convert</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- OpenOffice to PDF Modal -->
<div class="modal fade" id="openofficeToPdfModal" tabindex="-1" aria-labelledby="openofficeToPdfModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form id="openofficeToPdfForm" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title" id="openofficeToPdfModalLabel">Convert OpenOffice to PDF</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="file" name="openoffice_file" accept=".odt,.ods,.odp" class="form-control mb-3" required>

          <div id="openofficeLoadingIndicator" class="text-center my-3" style="display: none; color: #0dcaf0;">
            <div class="spinner-border text-info" role="status"></div>
            <p class="mt-2">Converting... Please wait</p>
          </div>

          <div id="openofficePreviewContainer" style="display: none;">
            <iframe id="openofficePdfPreview" width="100%" height="500px"></iframe>
            <a id="openofficeDownloadLink" class="btn btn-info mt-2" download>Download PDF</a>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-info">Convert</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- PDF to Word Modal -->
<div class="modal fade" id="pdfToWordModal" tabindex="-1" aria-labelledby="pdfToWordModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form id="pdfToWordForm" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title">Convert PDF to Word</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div class="mb-3">
            <input type="file" class="form-control" name="pdf_file" accept=".pdf" required />
          </div>
          <div id="pdfToWordLoading" style="display: none;">
            <div class="spinner-border text-info" role="status"></div>
            <p class="mt-2">Converting... Please wait</p>
          </div>
          <div id="pdfToWordResult" style="display: none;">
            <a id="downloadWordLink" class="btn btn-success mb-2" download>Download Word Document</a>
            <a id="editWordLink" class="btn btn-primary mb-2" target="_blank">Edit Word Document</a>
          </div>
        </div>
        <div id="pdfToWordPreview" class="border rounded p-3 mb-3" style="height: 300px; overflow-y: auto; display: none;">
  <strong>Preview:</strong>
  <div id="previewContent"></div>
</div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Convert</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- PDF to Excel Modal -->
<div class="modal fade" id="pdfToExcelModal" tabindex="-1" aria-labelledby="pdfToExcelModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form id="pdfToExcelForm" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title">Convert PDF to Excel</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div class="mb-3">
            <input type="file" class="form-control" name="pdf_file" accept=".pdf" required />
          </div>
          <div id="pdfToExcelLoading" style="display: none;">
            <div class="spinner-border text-info" role="status"></div>
            Converting... Please wait
          </div>
          <div id="pdfToExcelResult" style="display: none;">
            <a id="downloadExcelLink" class="btn btn-success mb-2" download>Download Excel File</a>
            <a id="editExcelLink" class="btn btn-primary mb-2" target="_blank">Edit Excel File</a>
          </div>
          <div id="pdfToExcelPreview" class="border rounded p-3 mb-3" style="height: 300px; overflow-y: auto; display: none;">
            <strong>Preview:</strong>
            <pre id="excelPreviewContent"></pre>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Convert</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- PDF to PowerPoint Modal -->
<div class="modal fade" id="pdfToPptModal" tabindex="-1" aria-labelledby="pdfToPptModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form id="pdfToPptForm" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title">Convert PDF to PowerPoint</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div class="mb-3">
            <input type="file" class="form-control" name="pdf_file" accept=".pdf" required />
          </div>
          <div id="pdfToPptLoading" style="display: none;">
            <div class="spinner-border text-info" role="status"></div>
            Converting... Please wait
          </div>
          <div id="pdfToPptResult" style="display: none;">
            <a id="downloadPptLink" class="btn btn-success mb-2" download>Download PowerPoint</a>
            <a id="editPptLink" class="btn btn-primary mb-2" target="_blank">Edit PowerPoint</a>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Convert</button>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- PDF Tools JS -->
<script src="./JS/pdf-tools.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
