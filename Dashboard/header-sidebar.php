<!-- Sidebar Toggle -->
<button id="sidebarToggle" class="btn btn-dark d-md-none position-fixed m-3">
  <i class="bi bi-list fs-4"></i>
</button>

<!-- Sidebar -->
<div id="sidebar" class="sidebar">
  <h4 class="logo"><i class="bi bi-file-earmark-text-fill me-2"></i>PDFSimba</h4>
  <a href="index.php" class="sidebar-link active"><i class="bi bi-house-door me-2"></i> Home</a>
<a href="signpdf.php"><i class="bi bi-pen me-2"></i> Sign Document</a>
<a href="editpdf.php"><i class="bi bi-pencil-square me-2"></i> Edit PDF</a>
<a href="pdftoword.php"><i class="bi bi-file-earmark-word me-2"></i> PDF to Word</a>
<a href="pdftoexcel.php"><i class="bi bi-file-earmark-excel me-2"></i> PDF to Excel</a>
  <a href="history.php"><i class="bi bi-clock-history me-2"></i> Full History</a>
  <a href="profile.php"><i class="bi bi-person me-2"></i> Profile</a>
<div class="p-3">
  <a href="../DashboardBackend/logout.php" class="btn btn-light text-dark w-100 d-flex align-items-center justify-content-center custom-logout-btn">
    <i class="bi bi-box-arrow-right me-2 text-dark"></i> Log Out
  </a>
</div>

</div>


  <div class="main-content">
    <div class="top-banner d-flex justify-content-between align-items-center">
     <div>
  <h4>Hi, <?= htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?></h4>
  <p>Convert, manage, and secure your documents with ease. Trusted by professionals and students worldwide.</p>
</div>

      <div>
        <i class="bi bi-book-half fs-1"></i>
      </div>
    </div>

