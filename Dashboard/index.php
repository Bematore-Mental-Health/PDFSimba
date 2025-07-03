<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>PDFSimba | Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"/>
  <link rel="shortcut icon" href="../Media/book3.png" type="image/x-icon">
  <link rel="stylesheet" href="../CSS/dashboard.css">
</head>
<body>

<!-- Sidebar Toggle -->
<button id="sidebarToggle" class="btn btn-dark d-md-none position-fixed m-3">
  <i class="bi bi-list fs-4"></i>
</button>

<!-- Sidebar -->
<div id="sidebar" class="sidebar">
  <h4 class="logo"><i class="bi bi-file-earmark-text-fill me-2"></i>PDFSimba</h4>
  <a href="index.php" class="sidebar-link active"><i class="bi bi-house-door me-2"></i> Home</a>
  <a href="wordtopdf.php"><i class="bi bi-file-earmark-word me-2"></i> Word to PDF</a>
  <a href="exceltopdf.php"><i class="bi bi-file-earmark-excel me-2"></i> Excel to PDF</a>
  <a href="jpgtopdf.php"><i class="bi bi-image me-2"></i> JPG to PDF</a>
  <a href="history.php"><i class="bi bi-clock-history me-2"></i> Full History</a>
  <a href="reports.php"><i class="bi bi-bar-chart me-2"></i> Reports</a>
  <div class="p-3">
    <button class="btn btn-light w-100"><i class="bi bi-box-arrow-right me-2"></i> Log Out</button>
  </div>
</div>




  <div class="main-content">
    <div class="top-banner d-flex justify-content-between align-items-center">
      <div>
        <h4>Hi, Irham Muhammad Shidiq</h4>
        <p>Convert, manage, and secure your documents with ease. Trusted by professionals and students worldwide. </p>
      </div>
      <div>
        <i class="bi bi-book-half fs-1"></i>
      </div>
    </div>

    <div class="container">
      <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">

        <!-- PDF TOOL CARDS -->
        <div class="col"><a href="#" class="pdf-tool-box" data-bs-toggle="modal" data-bs-target="#wordToPdfModal"><i class="bi bi-file-earmark-word text-primary"></i><div class="tool-name">Word to PDF</div></a></div>
        <div class="col"><a href="#" class="pdf-tool-box" data-bs-toggle="modal" data-bs-target="#excelToPdfModal"><i class="bi bi-file-earmark-excel text-success"></i><div class="tool-name">Excel to PDF</div></a></div>
        <div class="col"><a href="#" class="pdf-tool-box" data-bs-toggle="modal" data-bs-target="#pptToPdfModal"><i class="bi bi-file-earmark-ppt text-danger"></i><div class="tool-name">PowerPoint to PDF</div></a></div>
        <div class="col"><a href="#" class="pdf-tool-box" data-bs-toggle="modal" data-bs-target="#jpgToPdfModal"><i class="bi bi-image text-purple"></i><div class="tool-name">JPG to PDF</div></a></div>
        <div class="col"><a href="#" class="pdf-tool-box" data-bs-toggle="modal" data-bs-target="#uploadSigningDocumentModal"><i class="bi bi-vector-pen text-pink"></i><div class="tool-name">Sign Document</div></a></div>
        <div class="col"><a href="#" class="pdf-tool-box" data-bs-toggle="modal" data-bs-target="#editPdfModal"><i class="bi bi-pencil-square text-info"></i><div class="tool-name">Edit PDF</div></a></div>
        <div class="col"><a href="#" class="pdf-tool-box" data-bs-toggle="modal" data-bs-target="#ebookToPdfModal"><i class="bi bi-book text-warning"></i><div class="tool-name">eBooks to PDF</div></a></div>
        <div class="col"><a href="#" class="pdf-tool-box" data-bs-toggle="modal" data-bs-target="#iworkToPdfModal"><i class="bi bi-apple text-dark"></i><div class="tool-name">iWork to PDF</div></a></div>
        <div class="col"><a href="#" class="pdf-tool-box" data-bs-toggle="modal" data-bs-target="#pdfToWordModal"><i class="bi bi-file-earmark-word text-primary"></i><div class="tool-name">PDF to Word</div></a></div>
        <div class="col"><a href="#" class="pdf-tool-box" data-bs-toggle="modal" data-bs-target="#pdfToExcelModal"><i class="bi bi-file-earmark-excel text-success"></i><div class="tool-name">PDF to Excel</div></a></div>
        <div class="col"><a href="#" class="pdf-tool-box" data-bs-toggle="modal" data-bs-target="#pdfToPptModal"><i class="bi bi-file-earmark-ppt text-danger"></i><div class="tool-name">PDF to PowerPoint</div></a></div>
        <div class="col"><a href="#" class="pdf-tool-box" data-bs-toggle="modal" data-bs-target="#pdfToJpgModal"><i class="bi bi-image text-purple"></i><div class="tool-name">PDF to JPG</div></a></div>
        <div class="col"><a href="#" class="pdf-tool-box" data-bs-toggle="modal" data-bs-target="#pdfToPngModal"><i class="bi bi-image text-indigo"></i><div class="tool-name">PDF to PNG</div></a></div>
        <div class="col"><a href="#" class="pdf-tool-box" data-bs-toggle="modal" data-bs-target="#pdfToPdfaModal"><i class="bi bi-file-earmark-text text-secondary"></i><div class="tool-name">PDF to PDF/A</div></a></div>
        <div class="col"><a href="#" class="pdf-tool-box" data-bs-toggle="modal" data-bs-target="#mergePDFModal"><i class="bi bi-files text-orange"></i><div class="tool-name">Merge PDF</div></a></div>
        <div class="col"><a href="#" class="pdf-tool-box" data-bs-toggle="modal" data-bs-target="#splitPDFModal"><i class="bi bi-scissors text-orange"></i><div class="tool-name">Split PDF</div></a></div>
        <div class="col"><a href="#" class="pdf-tool-box" data-bs-toggle="modal" data-bs-target="#protectPDFModal"><i class="bi bi-shield-lock-fill text-success"></i><div class="tool-name">Protect PDF</div></a></div>
        <div class="col"><a href="#" class="pdf-tool-box" data-bs-toggle="modal" data-bs-target="#unlockPDFModal"><i class="bi bi-unlock-fill text-danger"></i><div class="tool-name">Unlock PDF</div></a></div>

      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

<script>
  const sidebar = document.getElementById('sidebar');
  const toggleBtn = document.getElementById('sidebarToggle');

  toggleBtn.addEventListener('click', () => {
    sidebar.classList.toggle('show');
    toggleBtn.classList.toggle('move-right');
  });
</script>

</html>
