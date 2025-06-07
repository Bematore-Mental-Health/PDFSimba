document.addEventListener("DOMContentLoaded", function () {
  // Word to PDF
  const form = document.getElementById("wordToPdfForm");
  const previewContainer = document.getElementById("previewContainer");
  const loadingIndicator = document.getElementById("loadingIndicator");
  const pdfPreview = document.getElementById("pdfPreview");
  const downloadLink = document.getElementById("downloadLink");

  form.addEventListener("submit", function (e) {
    e.preventDefault();

    previewContainer.style.display = "none";
    loadingIndicator.style.display = "block";

    const formData = new FormData(form);

    fetch("http://127.0.0.1:5000/convert-word-to-pdf", {
      method: "POST",
      body: formData,
    })
    .then(response => {
      if (!response.ok) throw new Error("Failed to convert file.");
      return response.json();
    })
    .then(data => {
      const pdfUrl = "http://127.0.0.1:5000/" + data.pdf_path;

      pdfPreview.src = pdfUrl;
      downloadLink.href = pdfUrl;
      downloadLink.download = "converted_file.pdf";

      loadingIndicator.style.display = "none";
      previewContainer.style.display = "block";

      form.reset();
    })
    .catch(error => {
      loadingIndicator.style.display = "none";
      alert("Error: " + error.message);
    });
  });

  // Excel to PDF
  const excelForm = document.getElementById("excelToPdfForm");
  const excelPreviewContainer = document.getElementById("excelPreviewContainer");
  const excelLoadingIndicator = document.getElementById("excelLoadingIndicator");
  const excelPdfPreview = document.getElementById("excelPdfPreview");
  const excelDownloadLink = document.getElementById("excelDownloadLink");

  excelForm.addEventListener("submit", function (e) {
    e.preventDefault();

    excelPreviewContainer.style.display = "none";
    excelLoadingIndicator.style.display = "block";

    const formData = new FormData(excelForm);

    fetch("http://127.0.0.1:5000/convert-excel-to-pdf", {
      method: "POST",
      body: formData,
    })
    .then(response => {
      if (!response.ok) throw new Error("Failed to convert Excel file.");
      return response.json();
    })
    .then(data => {
      const pdfUrl = "http://127.0.0.1:5000/" + data.pdf_path;
      excelPdfPreview.src = pdfUrl;
      excelDownloadLink.href = pdfUrl;
      excelDownloadLink.download = "converted_excel.pdf";

      excelLoadingIndicator.style.display = "none";
      excelPreviewContainer.style.display = "block";

      excelForm.reset();
    })
    .catch(error => {
      excelLoadingIndicator.style.display = "none";
      alert("Error: " + error.message);
    });
  });

  // PowerPoint to PDF
  const pptForm = document.getElementById("pptToPdfForm");
  const pptPreviewContainer = document.getElementById("pptPreviewContainer");
  const pptLoadingIndicator = document.getElementById("pptLoadingIndicator");
  const pptPdfPreview = document.getElementById("pptPdfPreview");
  const pptDownloadLink = document.getElementById("pptDownloadLink");

  pptForm.addEventListener("submit", function (e) {
    e.preventDefault();

    pptPreviewContainer.style.display = "none";
    pptLoadingIndicator.style.display = "block";

    const formData = new FormData(pptForm);

    fetch("http://127.0.0.1:5000/convert-ppt-to-pdf", {
      method: "POST",
      body: formData,
    })
    .then(response => {
      if (!response.ok) throw new Error("Failed to convert PowerPoint file.");
      return response.json();
    })
    .then(data => {
      const pdfUrl = "http://127.0.0.1:5000/" + data.pdf_path;
      pptPdfPreview.src = pdfUrl;
      pptDownloadLink.href = pdfUrl;
      pptDownloadLink.download = "converted_ppt.pdf";

      pptLoadingIndicator.style.display = "none";
      pptPreviewContainer.style.display = "block";

      pptForm.reset();
    })
    .catch(error => {
      pptLoadingIndicator.style.display = "none";
      alert("Error: " + error.message);
    });
  });

  // JPG to PDF
  const jpgForm = document.getElementById("jpgToPdfForm");
  const jpgPreviewContainer = document.getElementById("jpgPreviewContainer");
  const jpgLoadingIndicator = document.getElementById("jpgLoadingIndicator");
  const jpgPdfPreview = document.getElementById("jpgPdfPreview");
  const jpgDownloadLink = document.getElementById("jpgDownloadLink");

  jpgForm.addEventListener("submit", function (e) {
    e.preventDefault();

    jpgPreviewContainer.style.display = "none";
    jpgLoadingIndicator.style.display = "block";

    const formData = new FormData(jpgForm);

    fetch("http://127.0.0.1:5000/convert-jpg-to-pdf", {
      method: "POST",
      body: formData,
    })
    .then(response => {
      if (!response.ok) throw new Error("Failed to convert JPG.");
      return response.json();
    })
    .then(data => {
      const pdfUrl = "http://127.0.0.1:5000/" + data.pdf_path;
      jpgPdfPreview.src = pdfUrl;
      jpgDownloadLink.href = pdfUrl;
      jpgDownloadLink.download = "converted_jpg.pdf";

      jpgLoadingIndicator.style.display = "none";
      jpgPreviewContainer.style.display = "block";

      jpgForm.reset();
    })
    .catch(error => {
      jpgLoadingIndicator.style.display = "none";
      alert("Error: " + error.message);
    });
  });
});

// AutoCAD to PDF
const cadForm = document.getElementById("cadToPdfForm");
const cadPreviewContainer = document.getElementById("cadPreviewContainer");
const cadLoadingIndicator = document.getElementById("cadLoadingIndicator");
const cadPdfPreview = document.getElementById("cadPdfPreview");
const cadDownloadLink = document.getElementById("cadDownloadLink");

cadForm.addEventListener("submit", function (e) {
  e.preventDefault();

  cadPreviewContainer.style.display = "none";
  cadLoadingIndicator.style.display = "block";

  const formData = new FormData(cadForm);

  fetch("http://127.0.0.1:5000/convert-cad-to-pdf", {
    method: "POST",
    body: formData,
  })
    .then(response => {
      if (!response.ok) throw new Error("Failed to convert AutoCAD file.");
      return response.json();
    })
    .then(data => {
      const pdfUrl = "http://127.0.0.1:5000/" + data.pdf_path;
      cadPdfPreview.src = pdfUrl;
      cadDownloadLink.href = pdfUrl;
      cadDownloadLink.download = "converted_cad.pdf";

      cadLoadingIndicator.style.display = "none";
      cadPreviewContainer.style.display = "block";

      cadForm.reset();
    })
    .catch(error => {
      cadLoadingIndicator.style.display = "none";
      alert("Error: " + error.message);
    });
});

// OpenOffice to PDF
const openofficeForm = document.getElementById("openofficeToPdfForm");
const openofficeLoadingIndicator = document.getElementById("openofficeLoadingIndicator");
const openofficePreviewContainer = document.getElementById("openofficePreviewContainer");
const openofficePdfPreview = document.getElementById("openofficePdfPreview");
const openofficeDownloadLink = document.getElementById("openofficeDownloadLink");

openofficeForm.addEventListener("submit", function (e) {
  e.preventDefault();
  openofficePreviewContainer.style.display = "none";
  openofficeLoadingIndicator.style.display = "block";

  const formData = new FormData(openofficeForm);

  fetch("http://127.0.0.1:5000/convert-openoffice-to-pdf", {
    method: "POST",
    body: formData
  })
    .then(response => response.json())
    .then(data => {
      if (data.error) throw new Error(data.error);

      const pdfUrl = "http://127.0.0.1:5000/" + data.pdf_path;
      openofficePdfPreview.src = pdfUrl;
      openofficeDownloadLink.href = pdfUrl;
      openofficeDownloadLink.download = "converted_openoffice.pdf";

      openofficeLoadingIndicator.style.display = "none";
      openofficePreviewContainer.style.display = "block";
      openofficeForm.reset();
    })
    .catch(error => {
      openofficeLoadingIndicator.style.display = "none";
      alert("Error: " + error.message);
    });
});

// PDF To Word
document.getElementById("pdfToWordForm").addEventListener("submit", function (e) {
  e.preventDefault();

  const form = e.target;
  const loading = document.getElementById("pdfToWordLoading");
  const result = document.getElementById("pdfToWordResult");
  const downloadLink = document.getElementById("downloadWordLink");
  const editLink = document.getElementById("editWordLink");
  const previewContainer = document.getElementById("pdfToWordPreview");
  const previewContent = document.getElementById("previewContent");

  // Reset UI states
  loading.style.display = "block";
  result.style.display = "none";
  previewContainer.style.display = "none";
  previewContent.innerHTML = "";

  const formData = new FormData(form);

  fetch("http://127.0.0.1:5000/convert-pdf-to-word", {
    method: "POST",
    body: formData,
  })
    .then((res) => res.json())
    .then((data) => {
      loading.style.display = "none";

      if (data.error) {
        alert("Error: " + data.error);
        return;
      }

      const htmlPath = "http://127.0.0.1:5000/" + data.word_path;
      const docxPath = "http://127.0.0.1:5000/" + data.word_docx_path;
      const editorUrl = "http://127.0.0.1:5000/editor/" + data.word_filename;

      // Set download and edit links
      downloadLink.href = docxPath;
      downloadLink.setAttribute("download", data.word_docx_path.split('/').pop());
      editLink.href = editorUrl;

      // Fetch preview content from HTML
      fetch(htmlPath)
        .then((res) => res.text())
        .then((html) => {
          previewContent.innerHTML = html;
          previewContainer.style.display = "block";
        })
        .catch((err) => {
          previewContent.innerHTML = "<p class='text-danger'>Failed to load preview.</p>";
          previewContainer.style.display = "block";
        });

      result.style.display = "block";
      form.reset();
    })
    .catch((err) => {
      loading.style.display = "none";
      alert("Conversion failed: " + err.message);
    });
});

// PDF  To Excel 
document.getElementById("pdfToExcelForm").addEventListener("submit", function (e) {
  e.preventDefault();

  const form = e.target;
  const loading = document.getElementById("pdfToExcelLoading");
  const result = document.getElementById("pdfToExcelResult");
  const downloadLink = document.getElementById("downloadExcelLink");
  const editLink = document.getElementById("editExcelLink");
  const previewContainer = document.getElementById("pdfToExcelPreview");
  const previewContent = document.getElementById("excelPreviewContent");

  loading.style.display = "block";
  result.style.display = "none";
  previewContainer.style.display = "none";
  previewContent.textContent = "";

  const formData = new FormData(form);

  fetch("http://127.0.0.1:5000/convert-pdf-to-excel", {
    method: "POST",
    body: formData,
  })
    .then(res => res.json())
    .then(data => {
      loading.style.display = "none";

      if (data.error) {
        alert("Error: " + data.error);
        return;
      }

      const excelPath = `http://127.0.0.1:5000/download/${data.excel_filename}`;
      const editorUrl = `http://127.0.0.1:5000/excel-editor/${data.excel_filename}`;

      downloadLink.href = excelPath;
      editLink.href = editorUrl;

      // Basic preview
      fetch(`http://127.0.0.1:5000/converted/${data.excel_filename}`)
        .then(res => res.arrayBuffer())
        .then(buffer => {
          const data = new Uint8Array(buffer);
          const workbook = XLSX.read(data, { type: "array" });
          const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
          const csv = XLSX.utils.sheet_to_csv(firstSheet);
          previewContent.textContent = csv;
          previewContainer.style.display = "block";
        });

      result.style.display = "block";
      form.reset();
    })
    .catch(err => {
      loading.style.display = "none";
      alert("Conversion failed: " + err.message);
    });
});


// PDF to Powerpoint 
document.getElementById("pdfToPptForm").addEventListener("submit", function (e) {
  e.preventDefault();

  const form = e.target;
  const loading = document.getElementById("pdfToPptLoading");
  const result = document.getElementById("pdfToPptResult");
  const downloadLink = document.getElementById("downloadPptLink");
  const editLink = document.getElementById("editPptLink");
  const previewContainer = document.getElementById("pptSlidesContainer");

  loading.style.display = "block";
  result.style.display = "none";
  editLink.hidden = true;
  if (previewContainer) previewContainer.innerHTML = "";

  const formData = new FormData(form);

  fetch("http://127.0.0.1:5000/convert-pdf-to-ppt", {
    method: "POST",
    body: formData,
  })
    .then(res => res.json())
    .then(data => {
      loading.style.display = "none";

      if (data.error) {
        alert("Error: " + data.error);
        return;
      }

      const pptPath = `http://127.0.0.1:5000/download/${data.ppt_filename}`;
      const editorUrl = `http://127.0.0.1:5000/ppt-editor/${data.ppt_filename}`;

      downloadLink.href = pptPath;
      editLink.href = editorUrl;
      editLink.hidden = false;

      result.style.display = "block";

      // Fetch and render slide previews
      fetch(`http://127.0.0.1:5000/get-slide-images/${data.ppt_filename}`)
        .then(res => res.json())
        .then(images => {
          if (previewContainer) {
            previewContainer.innerHTML = "";
            images.forEach(src => {
              const img = document.createElement("img");
              img.src = src;
              img.style.maxWidth = "200px";
              img.classList.add("img-thumbnail", "me-2", "mb-2");
              previewContainer.appendChild(img);
            });
          }
        });

      form.reset();
    })
    .catch(err => {
      loading.style.display = "none";
      alert("Conversion failed: " + err.message);
    });
});

// PDF to JPG
 document.getElementById("pdfToJpgForm").addEventListener("submit", function (e) {
    e.preventDefault();

    const form = e.target;
    const loading = document.getElementById("pdfToJpgLoading");
    const result = document.getElementById("pdfToJpgResult");
    const downloadLink = document.getElementById("downloadZipLink");
    const previewContainer = document.getElementById("jpgPreviewContainer");
    const previewImages = document.getElementById("previewImages");

    loading.style.display = "block";
    result.style.display = "none";
    previewImages.innerHTML = "";

    const formData = new FormData(form);

    fetch("http://127.0.0.1:5000/convert-pdf-to-jpg", {
      method: "POST",
      body: formData,
    })
      .then((res) => res.json())
      .then((data) => {
        loading.style.display = "none";

        if (data.error) {
          alert("Error: " + data.error);
          return;
        }

        const zipPath = `http://127.0.0.1:5000${data.zipUrl}`;
        downloadLink.href = zipPath;

        data.imageUrls.forEach((url) => {
          const img = document.createElement("img");
          img.src = `http://127.0.0.1:5000${url}`;
          img.className = "img-thumbnail";
          img.style.maxWidth = "150px";
          img.style.maxHeight = "150px";
          previewImages.appendChild(img);
        });

        result.style.display = "block";
        form.reset();
      })
      .catch((err) => {
        loading.style.display = "none";
        alert("Conversion failed: " + err.message);
      });
  });


//PDF To PNG
document.getElementById("pdfToPngForm").addEventListener("submit", function (e) {
  e.preventDefault();

  const form = e.target;
  const loading = document.getElementById("pdfToPngLoading");
  const result = document.getElementById("pdfToPngResult");
  const downloadLink = document.getElementById("downloadPngZipLink");
  const previewContainer = document.getElementById("pngPreviewContainer");
  const previewImages = document.getElementById("previewPngImages");

  loading.style.display = "block";
  result.style.display = "none";
  previewImages.innerHTML = "";

  const formData = new FormData(form);

  fetch("http://127.0.0.1:5000/convert-pdf-to-png", {
    method: "POST",
    body: formData,
  })
    .then((res) => res.json())
    .then((data) => {
      loading.style.display = "none";

      if (data.error) {
        alert("Error: " + data.error);
        return;
      }

      const zipPath = `http://127.0.0.1:5000${data.zipUrl}`;
      downloadLink.href = zipPath;

      data.imageUrls.forEach((url) => {
        const img = document.createElement("img");
        img.src = `http://127.0.0.1:5000${url}`;
        img.className = "img-thumbnail";
        img.style.maxWidth = "150px";
        img.style.maxHeight = "150px";
        previewImages.appendChild(img);
      });

      result.style.display = "block";
      form.reset();
    })
    .catch((err) => {
      loading.style.display = "none";
      alert("Conversion failed: " + err.message);
    });
});


// PDF To PDF/A
document.getElementById("pdfToPdfaForm").addEventListener("submit", function(e) {
  e.preventDefault();

  const form = this;
  const resultDiv = document.getElementById("pdfaResult");
  const loadingIndicator = document.getElementById("pdfaLoading");
  
  // Clear previous results and show loading
  resultDiv.innerHTML = "";
  loadingIndicator.style.display = "block";
  form.querySelector("button[type='submit']").disabled = true;

  const formData = new FormData(form);

  // Make sure this points to your Flask server (port 5000)
  fetch("http://localhost:5000/convert-to-pdfa", {
    method: "POST",
    body: formData,
    headers: {
      'Accept': 'application/json'
    }
  })
  .then(async response => {
    const contentType = response.headers.get('content-type');
    
    // First try to parse as JSON
    if (contentType && contentType.includes('application/json')) {
      const data = await response.json();
      if (!response.ok) {
        throw new Error(data.error || "Failed to convert PDF");
      }
      return data;
    }
    
    // If not JSON, get the text and throw it as error
    const text = await response.text();
    throw new Error(text || "Server returned an unexpected response");
  })
  .then(data => {
    // Create download link
    const link = document.createElement("a");
    link.href = data.download_url;
    link.className = "btn btn-success";
    link.textContent = "Download PDF/A File";
    link.download = data.filename || "converted.pdf";
    
    resultDiv.innerHTML = `
      <div class="alert alert-success">
        Successfully converted to PDF/A-${data.pdfa_version}
      </div>
    `;
    resultDiv.appendChild(link);
    
    // Auto-click the download link
    setTimeout(() => {
      link.click();
    }, 500);
  })
  .catch(error => {
    console.error("Error:", error);
    resultDiv.innerHTML = `
      <div class="alert alert-danger">
        ${error.message || 'An error occurred during conversion'}
      </div>`;
  })
  .finally(() => {
    loadingIndicator.style.display = "none";
    form.querySelector("button[type='submit']").disabled = false;
  });
});


// Merge PDF 
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("mergePDFForm");
    const loadingIndicator = document.getElementById("mergePDFLoading");
    const pdfPreviewContainer = document.getElementById("pdfPreviewContainer");
    const downloadLink = document.getElementById("downloadMergedPDF");
    const closePreviewBtn = document.getElementById("closePreview");

    // Initialize modal events
    const mergeModal = new bootstrap.Modal(document.getElementById('mergePDFModal'));
    
    // Close preview handler
    closePreviewBtn?.addEventListener("click", function() {
        pdfPreviewContainer.style.display = "none";
    });

    form.addEventListener("submit", function (e) {
        e.preventDefault();

        // Reset UI
        pdfPreviewContainer.style.display = "none";
        downloadLink.style.display = "none";
        loadingIndicator.style.display = "block";

        const formData = new FormData(form);

        fetch("http://127.0.0.1:5000/merge-pdfs", {
            method: "POST",
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw new Error(err.error || "Failed to merge PDFs"); });
            }
            return response.json();
        })
        .then(data => {
            if (data.error) throw new Error(data.error);

            // Create a fresh iframe
            const iframeContainer = pdfPreviewContainer.querySelector('.ratio');
            iframeContainer.innerHTML = ''; // Clear previous iframe
            
            const newIframe = document.createElement("iframe");
            newIframe.id = "pdfPreview";
            newIframe.style.width = "100%";
            newIframe.style.height = "100%";
            newIframe.style.border = "none";
            
            // Set the source with cache-busting parameter
            const pdfUrl = `http://127.0.0.1:5000${data.pdf_path}?t=${Date.now()}`;
            newIframe.src = pdfUrl;
            iframeContainer.appendChild(newIframe);

            // Update UI
            pdfPreviewContainer.style.display = "block";
            downloadLink.href = pdfUrl;
            downloadLink.download = data.filename || "merged.pdf";
            downloadLink.style.display = "inline-block";
            
            // Ensure modal is properly shown
            mergeModal.show();
        })
        .catch(error => {
            console.error("Error:", error);
            alert("Error: " + error.message);
        })
        .finally(() => {
            loadingIndicator.style.display = "none";
        });
    });
});


// Split PDFs
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("splitPDFForm");

    form.addEventListener("submit", function (e) {
        e.preventDefault();

        const formData = new FormData(form);
        fetch("http://127.0.0.1:5000/upload-pdf", {
            method: "POST",
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) throw new Error(data.error);

            // Encode the filename for URL
            const encodedFilename = encodeURIComponent(data.file_path);
            const editorUrl = `http://127.0.0.1:5000/split-editor?file=${encodedFilename}`;
            window.open(editorUrl, "_blank");
        })
        .catch(error => {
            console.error("Upload error:", error);
            alert("Error: " + error.message);
        });
    });
});

// Protect PDF
const backendBaseURL = "http://127.0.0.1:5000"; // Base URL of Flask backend

document.getElementById("protectPDFForm").addEventListener("submit", function (e) {
  e.preventDefault();

  const form = this;
  const downloadLinks = document.getElementById("downloadLinksProtect");
  const loadingIndicator = document.getElementById("protectLoading");

  // Clear previous results and show loading
  downloadLinks.innerHTML = "";
  loadingIndicator.style.display = "block";
  form.querySelector("button[type='submit']").disabled = true;

  const formData = new FormData(form);

  fetch(`${backendBaseURL}/protect-pdf`, {
    method: "POST",
    body: formData,
    headers: {
      'Accept': 'application/json',
    }
  })
    .then(response => {
      if (!response.ok) {
        return response.json().then(err => {
          throw new Error(err.error || "Failed to protect PDF");
        });
      }
      return response.json();
    })
    .then(data => {
      if (data.error) throw new Error(data.error);

      const link = document.createElement("a");
      link.href = data.download_url;
      link.className = "btn btn-success";
      link.textContent = "Download Protected PDF";
      link.download = data.filename || "protected.pdf";

      downloadLinks.innerHTML = "";
      downloadLinks.appendChild(link);
    })
    .catch(error => {
      console.error("Error:", error);
      downloadLinks.innerHTML = `<div class="alert alert-danger">Error: ${error.message}</div>`;
    })
    .finally(() => {
      loadingIndicator.style.display = "none";
      form.querySelector("button[type='submit']").disabled = false;
    });
});

// Unlock PDF 
document.getElementById("unlockPDFForm").addEventListener("submit", function(e) {
  e.preventDefault();

  const form = this;
  const downloadLinks = document.getElementById("downloadLinksUnlock");
  const loadingIndicator = document.getElementById("unlockLoading");
  
  // Clear previous results and show loading
  downloadLinks.innerHTML = "";
  loadingIndicator.style.display = "block";
  form.querySelector("button[type='submit']").disabled = true;

  const formData = new FormData(form);

  fetch("http://localhost:5000/unlock-pdf", {
    method: "POST",
    body: formData,
    headers: {
      'Accept': 'application/json'
    }
  })
  .then(async response => {
    const data = await response.json();
    if (!response.ok) {
      throw new Error(data.error || "Failed to unlock PDF");
    }
    return data;
  })
  .then(data => {
    // Create download link
    const link = document.createElement("a");
    link.href = data.download_url;
    link.className = "btn btn-success";
    link.textContent = "Download Unlocked PDF";
    link.download = data.filename || "unlocked.pdf";
    
    downloadLinks.innerHTML = "";
    downloadLinks.appendChild(link);
    
    // Auto-click the download link
    setTimeout(() => {
      link.click();
    }, 500);
  })
  .catch(error => {
    console.error("Error:", error);
    downloadLinks.innerHTML = `<div class="alert alert-danger">${error.message}</div>`;
  })
  .finally(() => {
    loadingIndicator.style.display = "none";
    form.querySelector("button[type='submit']").disabled = false;
  });
});

// iWork to PDF 
document.addEventListener('DOMContentLoaded', function() {
  const BACKEND_URL = 'http://localhost:5000';
  const fileInput = document.getElementById('iworkFileInput');
  const fileInfo = document.getElementById('fileInfo');
  const fileName = document.getElementById('fileName');
  const convertBtn = document.getElementById('convertBtn');
  const pdfDisplayArea = document.getElementById('pdfDisplayArea');
  const downloadBtn = document.getElementById('downloadBtn');
  const convertAnotherBtn = document.getElementById('convertAnotherBtn');

  let currentFile = null;
  let pdfBlob = null;

  // Reset UI function
  function resetConversionUI() {
    fileInput.value = '';
    currentFile = null;
    pdfBlob = null;
    fileInfo.classList.add('d-none');
    pdfDisplayArea.classList.add('d-none');
    document.getElementById('pdfViewer').innerHTML = '';
    convertBtn.disabled = false;
    convertBtn.innerHTML = '<i class="bi bi-file-earmark-pdf"></i> Convert to PDF';
  }

  // File selection handler
  fileInput.addEventListener('change', function(e) {
    if (e.target.files.length > 0) {
      currentFile = e.target.files[0];
      fileName.textContent = currentFile.name;
      fileInfo.classList.remove('d-none');
      pdfDisplayArea.classList.add('d-none');
    }
  });

  // Convert button handler
  convertBtn.addEventListener('click', async function() {
    if (!currentFile) {
      alert('Please select a file first');
      return;
    }

    convertBtn.disabled = true;
    convertBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Converting...';

    try {
      const formData = new FormData();
      formData.append('file', currentFile);

      // Step 1: Convert the file
      const conversionResponse = await fetch(`${BACKEND_URL}/convert-iwork-to-pdf`, {
        method: 'POST',
        body: formData
      });

      if (!conversionResponse.ok) throw new Error('Conversion failed');
      
      const result = await conversionResponse.json();
      if (!result.success) throw new Error(result.message || 'Conversion failed');
      
      // Step 2: Get the PDF as blob
      const pdfResponse = await fetch(`${BACKEND_URL}${result.pdfUrl}`);
      pdfBlob = await pdfResponse.blob();
      
      // Step 3: Display PDF using PDF.js
      const pdfObjectUrl = URL.createObjectURL(pdfBlob);
      
      // Show the display area immediately
      pdfDisplayArea.classList.remove('d-none');
      fileInfo.classList.add('d-none');
      
      // Render PDF using PDF.js
     // Inside your convertBtn click handler, replace the PDF rendering part with:
const loadingTask = pdfjsLib.getDocument(pdfObjectUrl);
loadingTask.promise.then(function(pdf) {
  const viewer = document.getElementById('pdfViewer');
  viewer.innerHTML = '';
  
  // Get the container dimensions
  const container = viewer.parentElement;
  const containerWidth = container.clientWidth - 30; // Account for padding
  
  // Show first page
  pdf.getPage(1).then(function(page) {
    // Calculate scale to fit container width
    const viewport = page.getViewport({ scale: 1.0 });
    const scale = (containerWidth - 20) / viewport.width; // -20 for some margin
    const scaledViewport = page.getViewport({ scale: scale });
    
    const canvas = document.createElement('canvas');
    const context = canvas.getContext('2d');
    canvas.height = scaledViewport.height;
    canvas.width = scaledViewport.width;
    
    // Add some styling classes
    canvas.classList.add('img-fluid');
    canvas.classList.add('mb-2');
    canvas.style.maxWidth = '100%';
    canvas.style.height = 'auto';
    
    viewer.appendChild(canvas);
    
    const renderContext = {
      canvasContext: context,
      viewport: scaledViewport
    };
    
    page.render(renderContext);
  });
});

      // Set up download
      downloadBtn.onclick = function() {
        const a = document.createElement('a');
        a.href = pdfObjectUrl;
        a.download = currentFile.name.replace(/\.[^.]+$/, '') + '.pdf';
        document.body.appendChild(a);
        a.click();
        setTimeout(() => {
          document.body.removeChild(a);
          URL.revokeObjectURL(pdfObjectUrl);
        }, 100);
      };

    } catch (error) {
      console.error('Error:', error);
      alert('Conversion failed: ' + error.message);
      resetConversionUI();
    } finally {
      convertBtn.disabled = false;
      convertBtn.innerHTML = '<i class="bi bi-file-earmark-pdf"></i> Convert to PDF';
    }
  });

  // Convert another handler
  convertAnotherBtn.addEventListener('click', resetConversionUI);
});