// Global backend URL - change this when deploying to production
// const BACKEND_URL = 'https://server.pdfsimba.com';
const BACKEND_URL = 'http://localhost:5000';

  // Word to PDF
document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("wordToPdfForm");
    const previewContainer = document.getElementById("previewContainer");
    const loadingIndicator = document.getElementById("loadingIndicator");
    const pdfPreview = document.getElementById("pdfPreview");
    const downloadLink = document.getElementById("downloadLink");
    

    form.addEventListener("submit", async function(e) {
        e.preventDefault();
        
        // Reset UI
        previewContainer.style.display = "none";
        loadingIndicator.style.display = "block";
        
        try {
            // Send file to backend
            const formData = new FormData(form);
            const response = await fetch(`${BACKEND_URL}/convert-word-to-pdf`, {
                method: "POST",
                body: formData
            });
            
            if (!response.ok) throw new Error("Conversion failed");
            
            const data = await response.json();
            const pdfUrl = `${BACKEND_URL}/${data.pdf_path}`;
            
            // 2. SET UP DOWNLOAD (NO REDIRECT)
            downloadLink.onclick = async function(e) {
                e.preventDefault();
                const downloadResponse = await fetch(pdfUrl);
                const blob = await downloadResponse.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.style.display = 'none';
                a.href = url;
                a.download = 'document.pdf';
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
            };
            
            // 3. SET UP PREVIEW
            pdfPreview.src = pdfUrl;
            
            // Show results
            loadingIndicator.style.display = "none";
            previewContainer.style.display = "block";
            
        } catch (error) {
            loadingIndicator.style.display = "none";
            alert("Error: " + error.message);
        }
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

    fetch(`${BACKEND_URL}/convert-excel-to-pdf`, {
        method: "POST",
        body: formData,
    })
    .then(response => {
        if (!response.ok) throw new Error("Failed to convert Excel file.");
        return response.json();
    })
    .then(data => {
        const pdfUrl = `${BACKEND_URL}/${data.pdf_path}`;
        
        // Set PDF preview
        excelPdfPreview.src = pdfUrl;
        
        // Fix download to prevent redirect
        excelDownloadLink.onclick = function(e) {
            e.preventDefault();
            fetch(pdfUrl)
                .then(response => response.blob())
                .then(blob => {
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'converted_excel.pdf';
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                });
        };

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

    fetch(`${BACKEND_URL}/convert-ppt-to-pdf`, {
        method: "POST",
        body: formData,
    })
    .then(response => {
        if (!response.ok) throw new Error("Failed to convert PowerPoint file.");
        return response.json();
    })
    .then(data => {
        const pdfUrl = `${BACKEND_URL}/${data.pdf_path}`;
        
        // Set PDF preview
        pptPdfPreview.src = pdfUrl;
        
        // Set up download without redirect
        pptDownloadLink.onclick = function(e) {
            e.preventDefault();
            fetch(pdfUrl)
                .then(response => response.blob())
                .then(blob => {
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'converted_ppt.pdf';
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                })
                .catch(error => {
                    alert("Download failed: " + error.message);
                });
        };

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

    fetch(`${BACKEND_URL}/convert-jpg-to-pdf`, {
        method: "POST",
        body: formData,
    })
    .then(response => {
        if (!response.ok) throw new Error("Failed to convert JPG files.");
        return response.json();
    })
    .then(data => {
        const pdfUrl = `${BACKEND_URL}/${data.pdf_path}`;
        
        // Set PDF preview
        jpgPdfPreview.src = pdfUrl;
        
        // Set up download without redirect
        jpgDownloadLink.onclick = function(e) {
            e.preventDefault();
            fetch(pdfUrl)
                .then(response => response.blob())
                .then(blob => {
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'converted_images.pdf';
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                })
                .catch(error => {
                    alert("Download failed: " + error.message);
                });
        };

        jpgLoadingIndicator.style.display = "none";
        jpgPreviewContainer.style.display = "block";
        jpgForm.reset();
    })
    .catch(error => {
        jpgLoadingIndicator.style.display = "none";
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

  fetch(`${BACKEND_URL}/convert-pdf-to-word`, {
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

      const htmlPath = `${BACKEND_URL}/` + data.word_path;
      const docxPath = `${BACKEND_URL}/` + data.word_docx_path;
      const editorUrl = `${BACKEND_URL}/editor/` + data.word_filename;

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

  fetch(`${BACKEND_URL}/convert-pdf-to-excel`, {
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

      const excelPath = `${BACKEND_URL}/download/${data.excel_filename}`;
      const editorUrl = `${BACKEND_URL}/excel-editor/${data.excel_filename}`;

      downloadLink.href = excelPath;
      editLink.href = editorUrl;

      // Basic preview
      fetch(`${BACKEND_URL}/converted/${data.excel_filename}`)
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

  loading.style.display = "block";
  result.style.display = "none";

  const formData = new FormData(form);

  fetch(`${BACKEND_URL}/convert-pdf-to-ppt`, {
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

      const pptPath = `${BACKEND_URL}/download/${data.ppt_filename}`;
      downloadLink.href = pptPath;
      result.style.display = "block";
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

    fetch(`${BACKEND_URL}/convert-pdf-to-jpg`, {
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

        const zipPath = `${BACKEND_URL}${data.zipUrl}`;
        downloadLink.href = zipPath;

        data.imageUrls.forEach((url) => {
          const img = document.createElement("img");
          img.src = `${BACKEND_URL}${url}`;
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

  fetch(`${BACKEND_URL}/convert-pdf-to-png`, {
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

      const zipPath = `${BACKEND_URL}${data.zipUrl}`;
      downloadLink.href = zipPath;

      data.imageUrls.forEach((url) => {
        const img = document.createElement("img");
        img.src = `${BACKEND_URL}${url}`;
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

  fetch(`${BACKEND_URL}/convert-to-pdfa`, {
    method: "POST",
    body: formData,
    headers: {
      'Accept': 'application/json'
    }
  })
  .then(async response => {
    const contentType = response.headers.get('content-type');
    
    // Parse as JSON
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
    
    // REMOVED THE AUTO-CLICK CODE HERE
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

// Protect PDF

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

  fetch(`${BACKEND_URL}/protect-pdf`, {
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

  fetch(`${BACKEND_URL}/unlock-pdf`, {
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


// Sign Document
document.getElementById("signingDocumentForm").addEventListener("submit", function (e) {
  e.preventDefault();

  const form = e.target;
  const formData = new FormData(form);
  const submitButton = form.querySelector('button[type="submit"]');
  
  // Store original button text
  const originalText = submitButton.textContent;
  
  // Change button state
  submitButton.disabled = true;
  submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';

  fetch(`${BACKEND_URL}/upload-signing-document`, {
    method: "POST",
    body: formData,
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.error) {
        alert("Error: " + data.error);
        return;
      }

      // Open the signing editor in a new tab
      const signingEditorUrl = `${BACKEND_URL}/sign-document/${data.document_id}`;
      window.open(signingEditorUrl, "_blank");
      
      // Close the modal
      bootstrap.Modal.getInstance(document.getElementById('uploadSigningDocumentModal')).hide();
    })
    .catch((err) => {
      alert("Failed to upload document: " + err.message);
    })
    .finally(() => {
      // Reset button state
      submitButton.disabled = false;
      submitButton.textContent = originalText;
    });
});

  // Edit PDF 
  document.getElementById("uploadForm").addEventListener("submit", async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;

    try {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';

        const response = await fetch(`${BACKEND_URL}/upload-edit-pdf`, {
            method: "POST",
            body: formData,
            headers: {
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            const errorData = await response.json().catch(() => ({}));
            throw new Error(errorData.error || `Failed to process PDF (status: ${response.status})`);
        }

        const result = await response.json();

        if (result.success) {
            const editorUrl = `${BACKEND_URL}/pdf-editor-view?file=${encodeURIComponent(result.pdfFileName)}`;
            window.open(editorUrl, "_blank");
        } else {
            throw new Error(result.error || "Failed to process PDF");
        }
    } catch (error) {
        let errorMsg = error.message;
        // Handle specific error cases
        if (errorMsg.includes('unsupported colorspace') || errorMsg.includes('Image conversion failed')) {
            errorMsg = "The PDF contains complex images that couldn't be processed. The document has been converted without images.";
        }
        alert("Error: " + errorMsg);
        console.error('Error:', error);
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    }
  });
});

// iWork to PDF 
document.addEventListener('DOMContentLoaded', function() {
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

      // Convert the file
      const conversionResponse = await fetch(`${BACKEND_URL}/convert-iwork-to-pdf`, {
        method: 'POST',
        body: formData
      });

      if (!conversionResponse.ok) throw new Error('Conversion failed');
      
      const result = await conversionResponse.json();
      if (!result.success) throw new Error(result.message || 'Conversion failed');
      
      // Get the PDF as blob
      const pdfResponse = await fetch(`${BACKEND_URL}${result.pdfUrl}`);
      pdfBlob = await pdfResponse.blob();
      
      // Display PDF using PDF.js
      const pdfObjectUrl = URL.createObjectURL(pdfBlob);
      
      // Show the display area 
      pdfDisplayArea.classList.remove('d-none');
      fileInfo.classList.add('d-none');
      
      // Render PDF using PDF.js
const loadingTask = pdfjsLib.getDocument(pdfObjectUrl);
loadingTask.promise.then(function(pdf) {
  const viewer = document.getElementById('pdfViewer');
  viewer.innerHTML = '';
  
  // Get the container dimensions
  const container = viewer.parentElement;
  const containerWidth = container.clientWidth - 30; 
  
  // Show first page
  pdf.getPage(1).then(function(page) {
    // Calculate scale to fit container width
    const viewport = page.getViewport({ scale: 1.0 });
    const scale = (containerWidth - 20) / viewport.width; 
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

document.addEventListener('DOMContentLoaded', function() {
  const ebookTool = document.querySelector('.ebook-tool-box');
  const ebookFileInput = document.getElementById('ebookFileInput');
  const ebookFileInfo = document.getElementById('ebookFileInfo');
  const ebookFileName = document.getElementById('ebookFileName');
  const convertEbookBtn = document.getElementById('convertEbookBtn');
  const ebookPreviewContainer = document.getElementById('ebookPreviewContainer');
  const downloadEbookPdfBtn = document.getElementById('downloadEbookPdfBtn');
  const convertAnotherEbookBtn = document.getElementById('convertAnotherEbookBtn');

  let currentEbookFile = null;
  let ebookPdfBlob = null;


  // Initialize modal
  const ebookModal = new bootstrap.Modal(document.getElementById('ebookToPdfModal'));
  
  // Clean up when modal hides
  ebookModal._element.addEventListener('hidden.bs.modal', function() {
    resetEbookConversionUI();
    // Clear any remaining backdrop
    const backdrops = document.querySelectorAll('.modal-backdrop');
    backdrops.forEach(backdrop => backdrop.remove());
    // Reset body class
    document.body.classList.remove('modal-open');
  });

  // Update click handler
  ebookTool.addEventListener('click', function(e) {
    e.preventDefault();
    ebookModal.show();
  });

  ebookTool.addEventListener('click', function(e) {
    e.preventDefault();
    const modal = new bootstrap.Modal(document.getElementById('ebookToPdfModal'));
    modal.show();
    resetEbookConversionUI();
  });

  function resetEbookConversionUI() {
    ebookFileInput.value = '';
    currentEbookFile = null;
    ebookPdfBlob = null;
    ebookFileInfo.classList.add('d-none');
    ebookPreviewContainer.classList.add('d-none');
    document.getElementById('ebookPdfViewer').innerHTML = '';
    convertEbookBtn.disabled = false;
    convertEbookBtn.innerHTML = '<i class="bi bi-file-earmark-pdf"></i> Convert to PDF';
  }

  ebookFileInput.addEventListener('change', function(e) {
    if (e.target.files.length > 0) {
      currentEbookFile = e.target.files[0];
      ebookFileName.textContent = currentEbookFile.name;
      ebookFileInfo.classList.remove('d-none');
      ebookPreviewContainer.classList.add('d-none');
    }
  });

  convertEbookBtn.addEventListener('click', async function() {
    if (!currentEbookFile) {
      showAlert('Please select an eBook file first', 'warning');
      return;
    }

    convertEbookBtn.disabled = true;
    convertEbookBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Converting...';

    try {
      const formData = new FormData();
      formData.append('file', currentEbookFile);

      const conversionResponse = await fetch(`${BACKEND_URL}/convert-ebook-to-pdf`, {
        method: 'POST',
        body: formData
      });

      const result = await conversionResponse.json();

      if (!conversionResponse.ok || !result.success) {
        throw new Error(result.message || 'Conversion failed with unknown error');
      }

      const pdfResponse = await fetch(`${BACKEND_URL}${result.pdfUrl}`);
      if (!pdfResponse.ok) {
        throw new Error('Failed to fetch the converted PDF');
      }

      ebookPdfBlob = await pdfResponse.blob();
      const pdfObjectUrl = URL.createObjectURL(ebookPdfBlob);

      ebookPreviewContainer.classList.remove('d-none');
      ebookFileInfo.classList.add('d-none');

      await renderPdfPreview(pdfObjectUrl);

      downloadEbookPdfBtn.onclick = createDownloadHandler(pdfObjectUrl);

    } catch (error) {
      console.error('Conversion error:', error);
      showAlert(error.message, 'danger');
      resetEbookConversionUI();
    } finally {
      convertEbookBtn.disabled = false;
      convertEbookBtn.innerHTML = '<i class="bi bi-file-earmark-pdf"></i> Convert to PDF';
    }
  });

async function renderPdfPreview(pdfUrl) {
    try {
        // Load PDF.js with matching versions
        const PDFJS_VERSION = '2.12.313'; 
        pdfjsLib.GlobalWorkerOptions.workerSrc = `https://cdnjs.cloudflare.com/ajax/libs/pdf.js/${PDFJS_VERSION}/pdf.worker.min.js`;
        
        const loadingTask = pdfjsLib.getDocument({
            url: pdfUrl,
            cMapUrl: `https://cdnjs.cloudflare.com/ajax/libs/pdf.js/${PDFJS_VERSION}/cmaps/`,
            cMapPacked: true
        });

        const pdf = await loadingTask.promise;
        const viewer = document.getElementById('ebookPdfViewer');
        viewer.innerHTML = '';

        const container = viewer.parentElement;
        const containerWidth = container.clientWidth - 40;

        // Get first page
        const page = await pdf.getPage(1);
        const viewport = page.getViewport({ scale: 1.0 });
        const scale = containerWidth / viewport.width;
        const scaledViewport = page.getViewport({ scale: scale });

        // Create canvas for rendering
        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d');
        canvas.height = scaledViewport.height;
        canvas.width = scaledViewport.width;
        canvas.classList.add('img-fluid', 'mb-2', 'shadow-sm');
        canvas.style.maxWidth = '100%';
        canvas.style.height = 'auto';

        viewer.appendChild(canvas);

        // Render PDF page
        await page.render({
            canvasContext: context,
            viewport: scaledViewport
        }).promise;

    } catch (error) {
        console.error('PDF preview error:', error);
        document.getElementById('ebookPdfViewer').innerHTML = `
            <div class="alert alert-warning">
                PDF preview could not be displayed, but you can still download the file.
                <p class="mt-2 small">${error.message}</p>
            </div>
        `;
    }
}

  function createDownloadHandler(pdfObjectUrl) {
    return function() {
      const a = document.createElement('a');
      a.href = pdfObjectUrl;
      a.download = currentEbookFile.name.replace(/\.[^.]+$/, '') + '.pdf';
      document.body.appendChild(a);
      a.click();
      setTimeout(() => {
        document.body.removeChild(a);
      }, 100);
    };
  }

  convertAnotherEbookBtn.addEventListener('click', resetEbookConversionUI);
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

    // Handle download click
    downloadLink?.addEventListener("click", function(e) {
        e.preventDefault();
        const downloadUrl = this.href;
        
        // Create a hidden iframe to trigger download
        const iframe = document.createElement('iframe');
        iframe.style.display = 'none';
        iframe.src = downloadUrl;
        document.body.appendChild(iframe);
        
        // Clean up after download starts
        setTimeout(() => {
            document.body.removeChild(iframe);
        }, 1000);
    });

    form.addEventListener("submit", function (e) {
        e.preventDefault();

        // Reset UI
        pdfPreviewContainer.style.display = "none";
        downloadLink.style.display = "none";
        loadingIndicator.style.display = "block";

        const formData = new FormData(form);

        fetch(`${BACKEND_URL}/merge-pdfs`, {
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
            iframeContainer.innerHTML = ''; 
            
            const newIframe = document.createElement("iframe");
            newIframe.id = "pdfPreview";
            newIframe.style.width = "100%";
            newIframe.style.height = "100%";
            newIframe.style.border = "none";
            
            // Set the source with cache-busting parameter
            const pdfUrl = `${BACKEND_URL}${data.pdf_path}?t=${Date.now()}`;
            newIframe.src = pdfUrl;
            iframeContainer.appendChild(newIframe);

            // Update UI
            pdfPreviewContainer.style.display = "block";
            downloadLink.href = `${BACKEND_URL}${data.pdf_path}?download=true`; // Add download flag
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
        fetch(`${BACKEND_URL}/upload-pdf`, {
            method: "POST",
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) throw new Error(data.error);

            // Encode the filename for URL
            const encodedFilename = encodeURIComponent(data.file_path);
            const editorUrl = `${BACKEND_URL}/split-editor?file=${encodedFilename}`;
            window.open(editorUrl, "_blank");
        })
        .catch(error => {
            console.error("Upload error:", error);
            alert("Error: " + error.message);
        });
    });
});


