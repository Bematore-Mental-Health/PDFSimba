<?php
include '../DashboardBackend/session.php';

// Redirect to login page if session is not set
if (!isUserLoggedIn()) {
    header('Location: ../login.php');
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>PDFSimba | Dashboard</title>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.12.313/pdf.min.js"></script>
<script>
  // Set worker path for PDF.js
  pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.12.313/pdf.worker.min.js';
</script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"/>
  <link rel="shortcut icon" href="../Media/book3.png" type="image/x-icon">
  <link rel="stylesheet" href="../CSS/dashboard.css">
</head>
<body>

<?php 
include 'header-sidebar.php';
?>
    <div class="container">
      <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">

        <!-- PDF TOOL CARDS -->
          <!-- Word to PDF -->
  <div class="col">
    <a href="#" class="pdf-tool-box" data-bs-toggle="modal" data-bs-target="#wordToPdfModal">
      <i class="bi bi-file-earmark-word text-primary"></i>
      <div class="tool-name">Word to PDF</div>
    </a>
  </div>

  <!-- Excel to PDF -->
  <div class="col">
    <a href="#" class="pdf-tool-box" data-bs-toggle="modal" data-bs-target="#excelToPdfModal">
      <i class="bi bi-file-earmark-excel text-success"></i>
      <div class="tool-name">Excel to PDF</div>
    </a>
  </div>

  <!-- PowerPoint to PDF -->
  <div class="col">
    <a href="#" class="pdf-tool-box" data-bs-toggle="modal" data-bs-target="#pptToPdfModal">
      <i class="bi bi-file-earmark-ppt text-danger"></i>
      <div class="tool-name">PowerPoint to PDF</div>
    </a>
  </div>

  <!-- JPG to PDF -->
  <div class="col">
    <a href="#" class="pdf-tool-box" data-bs-toggle="modal" data-bs-target="#jpgToPdfModal">
      <i class="bi bi-image text-purple"></i>
      <div class="tool-name">JPG to PDF</div>
    </a>
  </div>

  <!-- Sign Document -->
  <div class="col">
    <a href="#" class="pdf-tool-box" data-bs-toggle="modal" data-bs-target="#uploadSigningDocumentModal">
      <i class="bi bi-vector-pen text-pink"></i>
      <div class="tool-name">Sign Document</div>
    </a>
  </div>

  <!-- Edit PDF -->
  <div class="col">
    <a href="#" class="pdf-tool-box" data-bs-toggle="modal" data-bs-target="#editPdfModal">
      <i class="bi bi-pencil-square text-info"></i>
      <div class="tool-name">Edit PDF</div>
    </a>
  </div>

  <!-- eBooks to PDF -->
  <div class="col">
    <a href="#" class="pdf-tool-box" data-bs-toggle="modal" data-bs-target="#ebookToPdfModal">
      <i class="bi bi-book text-warning"></i>
      <div class="tool-name">eBooks to PDF</div>
    </a>
  </div>

  <!-- iWork to PDF -->
  <div class="col">
    <a href="#" class="pdf-tool-box" data-bs-toggle="modal" data-bs-target="#iworkToPdfModal">
      <i class="bi bi-apple text-dark"></i>
      <div class="tool-name">iWork to PDF</div>
    </a>
  </div>

  <!-- PDF to Word -->
  <div class="col">
    <a href="#" class="pdf-tool-box" data-bs-toggle="modal" data-bs-target="#pdfToWordModal">
      <i class="bi bi-file-earmark-word text-primary"></i>
      <div class="tool-name">PDF to Word</div>
    </a>
  </div>

  <!-- PDF to Excel -->
  <div class="col">
    <a href="#" class="pdf-tool-box" data-bs-toggle="modal" data-bs-target="#pdfToExcelModal">
      <i class="bi bi-file-earmark-excel text-success"></i>
      <div class="tool-name">PDF to Excel</div>
    </a>
  </div>

  <!-- PDF to PowerPoint -->
  <div class="col">
    <a href="#" class="pdf-tool-box" data-bs-toggle="modal" data-bs-target="#pdfToPptModal">
      <i class="bi bi-file-earmark-ppt text-danger"></i>
      <div class="tool-name">PDF to PowerPoint</div>
    </a>
  </div>

  <!-- PDF to JPG -->
  <div class="col">
    <a href="#" class="pdf-tool-box" data-bs-toggle="modal" data-bs-target="#pdfToJpgModal">
      <i class="bi bi-image text-purple"></i>
      <div class="tool-name">PDF to JPG</div>
    </a>
  </div>

  <!-- PDF to PNG -->
  <div class="col">
    <a href="#" class="pdf-tool-box" data-bs-toggle="modal" data-bs-target="#pdfToPngModal">
      <i class="bi bi-image text-indigo"></i>
      <div class="tool-name">PDF to PNG</div>
    </a>
  </div>

  <!-- PDF to PDF/A -->
  <div class="col">
    <a href="#" class="pdf-tool-box" data-bs-toggle="modal" data-bs-target="#pdfToPdfaModal">
      <i class="bi bi-file-earmark-text text-secondary"></i>
      <div class="tool-name">PDF to PDF/A</div>
    </a>
  </div>

  <!-- Merge PDF -->
  <div class="col">
    <a href="#" class="pdf-tool-box" data-bs-toggle="modal" data-bs-target="#mergePDFModal">
      <i class="bi bi-files text-orange"></i>
      <div class="tool-name">Merge PDF</div>
    </a>
  </div>

  <!-- Split PDF -->
  <div class="col">
    <a href="#" class="pdf-tool-box" data-bs-toggle="modal" data-bs-target="#splitPDFModal">
      <i class="bi bi-scissors text-orange"></i>
      <div class="tool-name">Split PDF</div>
    </a>
  </div>

  <!-- Protect PDF -->
  <div class="col">
    <a href="#" class="pdf-tool-box" data-bs-toggle="modal" data-bs-target="#protectPDFModal">
      <i class="bi bi-shield-lock-fill text-success"></i>
      <div class="tool-name">Protect PDF</div>
    </a>
  </div>

  <!-- Unlock PDF -->
  <div class="col">
    <a href="#" class="pdf-tool-box" data-bs-toggle="modal" data-bs-target="#unlockPDFModal">
      <i class="bi bi-unlock-fill text-danger"></i>
      <div class="tool-name">Unlock PDF</div>
    </a>
  </div>
</div>
      </div>
    </div>
  </div>


<!-- Modals -->
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
          <!-- Hidden input for user ID -->
          <input type="hidden" id="currentUserId" name="user_id" value="<?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : ''; ?>">
          
          <input type="file" name="word_file" accept=".doc,.docx" class="form-control mb-3" required>
          <button type="submit" class="btn btn-success w-100">Convert</button>
        </div>
        
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
          <!-- Hidden input for user ID -->
          <input type="hidden" id="currentUserId" name="user_id" value="<?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : ''; ?>">
          
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
          <input type="hidden" id="currentUserId" name="user_id" value="<?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : ''; ?>">

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
          <input type="hidden" id="currentUserId" name="user_id" value="<?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : ''; ?>">

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
            <input type="hidden" id="currentUserId" name="user_id" value="<?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : ''; ?>">

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
            <input type="hidden" id="currentUserId" name="user_id" value="<?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : ''; ?>">

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
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Convert</button>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- PDF to JPG Modal -->
<div class="modal fade" id="pdfToJpgModal" tabindex="-1" aria-labelledby="pdfToJpgModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form id="pdfToJpgForm" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title">Convert PDF Pages to JPG</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <!-- Hidden input for user ID -->
          <input type="hidden" id="currentUserIdPdfToJpg" name="user_id" value="<?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : ''; ?>">
          
          <div class="mb-3">
            <input type="file" class="form-control" name="pdf_file" accept=".pdf" required />
          </div>
          <div id="pdfToJpgLoading" style="display: none;">
            <div class="spinner-border text-info" role="status"></div>
            Converting... Please wait
          </div>
          <div id="pdfToJpgResult" style="display: none;">
            <a id="downloadZipLink" class="btn btn-success mb-2" download>Download All as ZIP</a>
            <div id="jpgPreviewContainer" class="border rounded p-3 mb-3" style="height: 300px; overflow-y: auto;">
              <strong>Preview:</strong>
              <div id="previewImages" class="d-flex flex-wrap gap-2"></div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Convert</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- PDF to PNG -->
 <div class="modal fade" id="pdfToPngModal" tabindex="-1" aria-labelledby="pdfToPngModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form id="pdfToPngForm" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title">Convert PDF Pages to PNG</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <!-- Hidden input for user ID -->
          <input type="hidden" id="currentUserIdPdfToPng" name="user_id" value="<?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : ''; ?>">
          
          <div class="mb-3">
            <input type="file" class="form-control" name="pdf_file" accept=".pdf" required />
          </div>
          <div id="pdfToPngLoading" style="display: none;">
            <div class="spinner-border text-info" role="status"></div>
            Converting... Please wait
          </div>
          <div id="pdfToPngResult" style="display: none;">
            <a id="downloadPngZipLink" class="btn btn-success mb-2" download>Download All as ZIP</a>
            <div id="pngPreviewContainer" class="border rounded p-3 mb-3" style="height: 300px; overflow-y: auto;">
              <strong>Preview:</strong>
              <div id="previewPngImages" class="d-flex flex-wrap gap-2"></div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Convert</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- PDF to PDF/A Modal -->
<div class="modal fade" id="pdfToPdfaModal" tabindex="-1" aria-labelledby="pdfToPdfaModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="pdfToPdfaModalLabel">Convert PDF to PDF/A</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="pdfToPdfaForm" enctype="multipart/form-data">
          <!-- Hidden input for user ID -->
          <input type="hidden" id="currentUserIdPdfToPdfa" name="user_id" value="<?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : ''; ?>">
          
          <div class="mb-3">
            <label for="pdfFileToPdfa" class="form-label">Upload PDF:</label>
            <input type="file" id="pdfFileToPdfa" name="pdf_file" class="form-control" accept="application/pdf" required>
          </div>
          <div class="mb-3">
            <label for="pdfaVersion" class="form-label">PDF/A Version:</label>
            <select id="pdfaVersion" name="pdfa_version" class="form-select">
              <option value="1A">PDF/A-1a (Strict)</option>
              <option value="1B" selected>PDF/A-1b (Basic)</option>
              <option value="2A">PDF/A-2a</option>
              <option value="2B">PDF/A-2b</option>
              <option value="2U">PDF/A-2u</option>
              <option value="3A">PDF/A-3a</option>
              <option value="3B">PDF/A-3b</option>
              <option value="3U">PDF/A-3u</option>
            </select>
          </div>
          <div id="pdfaLoading" class="text-center mb-3" style="display: none;">
            <div class="spinner-border" role="status"></div>
            <p>Converting to PDF/A...</p>
          </div>
          <button type="submit" class="btn btn-primary">Convert to PDF/A</button>
        </form>
        <div id="pdfaResult" class="mt-3"></div>
      </div>
    </div>
  </div>
</div>


<!-- Merge PDFs Modal -->
<div class="modal fade" id="mergePDFModal" tabindex="-1" aria-labelledby="mergePDFModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="mergePDFModalLabel">Merge PDFs</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="currentUserIdMergePDF" name="user_id" value="<?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : ''; ?>">
        <form id="mergePDFForm" enctype="multipart/form-data">
          <div class="mb-3">
            <label for="pdfFiles" class="form-label">Select PDFs to Merge:</label>
            <input type="file" id="pdfFiles" name="pdf_files" multiple class="form-control" accept="application/pdf" required>
          </div>
          <div id="mergePDFLoading" class="text-center" style="display: none;">
            <div class="spinner-border" role="status"></div>
            <p>Processing...</p>
          </div>
          <div id="pdfPreviewContainer" class="border p-2" style="display: none;">
            <button id="closePreview" class="btn btn-sm btn-danger mb-2">Close Preview</button>
            <div class="ratio ratio-16x9">
              <iframe id="pdfPreview" style="width: 100%; height: 100%; border: none;"></iframe>
            </div>
          </div>
          <button type="submit" class="btn btn-primary mt-3">Merge and Preview</button>
        </form>
      </div>
      <div class="modal-footer">
        <a id="downloadMergedPDF" href="#" class="btn btn-success" style="display: none;">
          <i class="bi bi-download"></i> Download Merged PDF
        </a>
      </div>
    </div>
  </div>
</div>


<!-- Split PDF Modal -->
<div class="modal fade" id="splitPDFModal" tabindex="-1" aria-labelledby="splitPDFModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="splitPDFModalLabel">Split PDF</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="splitPDFForm" enctype="multipart/form-data">
          <!-- Add this hidden input for user ID -->
          <input type="hidden" id="currentUserIdSplitPDF" name="user_id" value="<?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : ''; ?>">
          
          <div class="mb-3">
            <label for="pdfFile" class="form-label">Upload PDF:</label>
            <input type="file" id="pdfFile" name="pdf_file" class="form-control" accept="application/pdf" required>
          </div>
          <button type="submit" class="btn btn-primary">Choose Where to Split</button>
        </form>
      </div>
    </div>
  </div>
</div>


<!-- Protect PDF Modal -->
<div class="modal fade" id="protectPDFModal" tabindex="-1" aria-labelledby="protectPDFModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="protectPDFModalLabel">Protect PDF</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="protectPDFForm" enctype="multipart/form-data">
          <!-- Add hidden user ID field -->
          <input type="hidden" id="currentUserIdProtect" name="user_id" value="<?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : ''; ?>">
          
          <div class="mb-3">
            <label for="pdfFileProtect" class="form-label">Upload PDF:</label>
            <input type="file" id="pdfFileProtect" name="pdf_file" class="form-control" accept="application/pdf" required>
          </div>
          <div class="mb-3">
            <label for="pdfPassword" class="form-label">Password:</label>
            <input type="password" id="pdfPassword" name="password" class="form-control" required>
          </div>
          <div id="protectLoading" class="text-center mb-3" style="display: none;">
            <div class="spinner-border" role="status"></div>
            <p>Protecting PDF...</p>
          </div>
          <button type="submit" class="btn btn-primary">Protect PDF</button>
        </form>
        <div id="downloadLinksProtect" class="mt-3"></div>
      </div>
    </div>
  </div>
</div>


<!-- Unlock PDF Modal -->
<div class="modal fade" id="unlockPDFModal" tabindex="-1" aria-labelledby="unlockPDFModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="unlockPDFModalLabel">Unlock PDF</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
                  <input type="hidden" id="currentUserIdProtect" name="user_id" value="<?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : ''; ?>">

        <form id="unlockPDFForm" enctype="multipart/form-data">
          <div class="mb-3">
            <label for="pdfFileUnlock" class="form-label">Upload Protected PDF:</label>
            <input type="file" id="pdfFileUnlock" name="pdf_file" class="form-control" accept="application/pdf" required>
          </div>
          <div class="mb-3">
            <label for="pdfPasswordUnlock" class="form-label">Password:</label>
            <input type="password" id="pdfPasswordUnlock" name="password" class="form-control" required>
          </div>
          <div id="unlockLoading" class="text-center mb-3" style="display: none;">
            <div class="spinner-border" role="status"></div>
            <p>Unlocking PDF...</p>
          </div>
          <button type="submit" class="btn btn-primary">Unlock PDF</button>
        </form>
        <div id="downloadLinksUnlock" class="mt-3"></div>
      </div>
    </div>
  </div>
</div>

<!-- eBooks to PDF Modal -->
<div class="modal fade" id="ebookToPdfModal" tabindex="-1" aria-labelledby="ebookToPdfModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="ebookToPdfModalLabel">Convert eBook to PDF</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="currentUserId" name="user_id" value="<?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : ''; ?>">

        <div class="upload-area p-4 border rounded mb-3 text-center">
          <input type="file" id="ebookFileInput" accept=".epub,.mobi,.azw,.fb2" class="d-none">
          <label for="ebookFileInput" class="btn btn-primary mb-3">
            <i class="bi bi-upload"></i> Select eBook File
          </label>
          <p class="small text-muted">Supports .epub, .mobi, .azw, and .fb2 files</p>
          
          <!-- Convert button is now always visible -->
          <button id="convertEbookBtn" class="btn btn-warning mt-3">
            <i class="bi bi-file-earmark-pdf"></i> Convert to PDF
          </button>
        </div>
        
        <div id="ebookPreviewContainer" class="d-none">
          <h6>PDF Preview</h6>
          <div class="pdf-preview-container" style="max-height: 60vh; overflow: auto; border: 1px solid #ddd; background: #f8f9fa;">
            <div id="ebookPdfViewer" class="text-center p-2"></div>
          </div>
          <div class="d-flex justify-content-between mt-3">
            <button id="downloadEbookPdfBtn" class="btn btn-success">
              <i class="bi bi-download"></i> Download PDF
            </button>
            <button id="convertAnotherEbookBtn" class="btn btn-outline-secondary">
              <i class="bi bi-arrow-repeat"></i> Convert Another
            </button>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Sign Document Modal -->
<div class="modal fade" id="uploadSigningDocumentModal" tabindex="-1" aria-labelledby="uploadSigningDocumentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form id="signingDocumentForm" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title">Upload Document for Signing</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="currentUserId" name="user_id" value="<?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : ''; ?>">  
          <div class="mb-3">
            <input type="file" class="form-control" name="document_file" accept=".pdf, .doc, .docx" required />
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Open for Signing</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit PDF Modal -->
<div class="modal fade" id="editPdfModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Upload PDF for Editing</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="uploadForm" enctype="multipart/form-data">
          <input type="hidden" id="currentUserId" name="user_id" value="<?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : ''; ?>">
          <input type="file" name="pdfFile" id="pdfFileInput" class="form-control mb-3" accept="application/pdf" required>
          <button type="submit" class="btn btn-primary">Upload & Edit PDF</button>
        </form>
      </div>
    </div>
  </div>
</div>


<!-- Modal for iWork to PDF conversion -->
<div class="modal fade" id="iworkToPdfModal" tabindex="-1" aria-labelledby="iworkToPdfModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="iworkToPdfModalLabel">Convert iWork to PDF</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
                  <input type="hidden" id="currentUserId" name="user_id" value="<?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : ''; ?>">

        <div class="upload-area p-4 border rounded mb-3 text-center">
          <input type="file" id="iworkFileInput" accept=".pages,.key,.numbers" class="d-none">
          <label for="iworkFileInput" class="btn btn-primary mb-3">
            <i class="bi bi-upload"></i> Select iWork File
          </label>
          <p class="small text-muted">Supports .pages, .key, and .numbers files</p>
          <div id="fileInfo" class="mt-2 d-none">
            <p>Selected file: <span id="fileName"></span></p>
            <button id="convertBtn" class="btn btn-warning mt-3">
              <i class="bi bi-file-earmark-pdf"></i> Convert to PDF
            </button>
          </div>
        </div>
<div id="pdfDisplayArea" class="d-none mt-3">
  <h6>PDF Preview</h6>
  <div class="pdf-preview-container" style="max-height: 60vh; overflow: auto; border: 1px solid #ddd; background: #f8f9fa;">
    <div id="pdfViewer" class="text-center p-2"></div>
  </div>
  <div class="d-flex justify-content-between mt-3">
    <button id="downloadBtn" class="btn btn-success">
      <i class="bi bi-download"></i> Download PDF
    </button>
    <button id="convertAnotherBtn" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-repeat"></i> Convert Another
    </button>
  </div>
</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<!-- PDF Tools JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="./JS/pdf-tools.js"></script>
<script src="./JS/toggle.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>


</html>
