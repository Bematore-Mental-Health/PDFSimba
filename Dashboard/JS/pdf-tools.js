// Global backend URL - change this when deploying to production
// const BACKEND_URL = 'https://server.pdfsimba.com';
const BACKEND_URL = '/dashapi';

// Main document ready wrapper
document.addEventListener("DOMContentLoaded", function() {
  
  // ==============================================
  // 1. OFFICE TO PDF CONVERSIONS
  // ==============================================

  // Word to PDF Conversion
  const wordToPdfForm = document.getElementById("wordToPdfForm");
  if (wordToPdfForm) {
    wordToPdfForm.addEventListener("submit", async function(e) {
      e.preventDefault();
      
      const previewContainer = document.getElementById("previewContainer");
      const loadingIndicator = document.getElementById("loadingIndicator");
      const pdfPreview = document.getElementById("pdfPreview");
      const downloadLink = document.getElementById("downloadLink");
      const currentUserId = document.getElementById("currentUserId").value;

      // Reset UI
      previewContainer.style.display = "none";
      loadingIndicator.style.display = "block";
      
      try {
        // Get file details
        const fileInput = this.querySelector('input[type="file"]');
        const file = fileInput.files[0];
        const originalFilename = file.name;
        const fileSize = file.size;
        
        // Send file to backend
        const formData = new FormData(this);
        const response = await fetch(`${BACKEND_URL}/convert-word-to-pdf`, {
          method: "POST",
          body: formData
        });
        
        if (!response.ok) throw new Error("Conversion failed");
        
        const data = await response.json();
        const pdfUrl = `${BACKEND_URL}/${data.pdf_path}`;
        const convertedFilename = data.pdf_path.split('/').pop();
        
        // Record conversion in database
        await fetch(`${BACKEND_URL}/record-conversion`, {
          method: "POST",
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            user_id: currentUserId,
            conversion_type: 'word_to_pdf',
            original_filename: originalFilename,
            converted_filename: convertedFilename,
            file_size: fileSize,
            status: 'completed',
            conversion_id: data.conversion_id || null
          })
        });
        
        // Set up download
        downloadLink.onclick = async function(e) {
          e.preventDefault();
          const downloadResponse = await fetch(pdfUrl);
          const blob = await downloadResponse.blob();
          const url = window.URL.createObjectURL(blob);
          const a = document.createElement('a');
          a.style.display = 'none';
          a.href = url;
          a.download = convertedFilename;
          document.body.appendChild(a);
          a.click();
          window.URL.revokeObjectURL(url);
          document.body.removeChild(a);
        };
        
        // Set up preview
        pdfPreview.src = pdfUrl;
        
        // Show results
        loadingIndicator.style.display = "none";
        previewContainer.style.display = "block";
        
      } catch (error) {
        loadingIndicator.style.display = "none";
        
        // Record failed conversion
        if (currentUserId) {
          await fetch(`${BACKEND_URL}/record-conversion`, {
            method: "POST",
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              user_id: currentUserId,
              conversion_type: 'word_to_pdf',
              original_filename: file ? file.name : 'unknown',
              file_size: file ? file.size : 0,
              status: 'failed',
              error_message: error.message
            })
          });
        }
        
        alert("Error: " + error.message);
      }
    });
  }

  // Excel to PDF Conversion
  const excelToPdfForm = document.getElementById("excelToPdfForm");
  if (excelToPdfForm) {
    excelToPdfForm.addEventListener("submit", async function(e) {
      e.preventDefault();
      
      const previewContainer = document.getElementById("excelPreviewContainer");
      const loadingIndicator = document.getElementById("excelLoadingIndicator");
      const pdfPreview = document.getElementById("excelPdfPreview");
      const downloadLink = document.getElementById("excelDownloadLink");
      const currentUserId = document.getElementById("currentUserId").value;

      // Reset UI
      previewContainer.style.display = "none";
      loadingIndicator.style.display = "block";
      
      try {
        // Get file details
        const fileInput = this.querySelector('input[type="file"]');
        const file = fileInput.files[0];
        const originalFilename = file.name;
        const fileSize = file.size;
        
        // Send file to backend
        const formData = new FormData(this);
        const response = await fetch(`${BACKEND_URL}/convert-excel-to-pdf`, {
          method: "POST",
          body: formData
        });
        
        if (!response.ok) throw new Error("Conversion failed");
        
        const data = await response.json();
        const pdfUrl = `${BACKEND_URL}/${data.pdf_path}`;
        const convertedFilename = data.pdf_path.split('/').pop();
        
        // Record conversion in database
        await fetch(`${BACKEND_URL}/record-conversion`, {
          method: "POST",
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            user_id: currentUserId,
            conversion_type: 'excel_to_pdf',
            original_filename: originalFilename,
            converted_filename: convertedFilename,
            file_size: fileSize,
            status: 'completed',
            conversion_id: data.conversion_id || null
          })
        });
        
        // Set up download
        downloadLink.onclick = async function(e) {
          e.preventDefault();
          const downloadResponse = await fetch(pdfUrl);
          const blob = await downloadResponse.blob();
          const url = window.URL.createObjectURL(blob);
          const a = document.createElement('a');
          a.style.display = 'none';
          a.href = url;
          a.download = convertedFilename;
          document.body.appendChild(a);
          a.click();
          window.URL.revokeObjectURL(url);
          document.body.removeChild(a);
        };
        
        // Set up preview
        pdfPreview.src = pdfUrl;
        
        // Show results
        loadingIndicator.style.display = "none";
        previewContainer.style.display = "block";
        
      } catch (error) {
        loadingIndicator.style.display = "none";
        
        // Record failed conversion
        if (currentUserId) {
          await fetch(`${BACKEND_URL}/record-conversion`, {
            method: "POST",
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              user_id: currentUserId,
              conversion_type: 'excel_to_pdf',
              original_filename: file ? file.name : 'unknown',
              file_size: file ? file.size : 0,
              status: 'failed',
              error_message: error.message
            })
          });
        }
        
        alert("Error: " + error.message);
      }
    });
  }

  // PowerPoint to PDF Conversion
  const pptToPdfForm = document.getElementById("pptToPdfForm");
  if (pptToPdfForm) {
    pptToPdfForm.addEventListener("submit", async function(e) {
      e.preventDefault();
      
      const previewContainer = document.getElementById("pptPreviewContainer");
      const loadingIndicator = document.getElementById("pptLoadingIndicator");
      const pdfPreview = document.getElementById("pptPdfPreview");
      const downloadLink = document.getElementById("pptDownloadLink");
      const currentUserId = document.getElementById("currentUserId").value;

      // Reset UI
      previewContainer.style.display = "none";
      loadingIndicator.style.display = "block";
      
      try {
        // Get file details
        const fileInput = this.querySelector('input[type="file"]');
        const file = fileInput.files[0];
        const originalFilename = file.name;
        const fileSize = file.size;
        
        // Send file to backend
        const formData = new FormData(this);
        const response = await fetch(`${BACKEND_URL}/convert-ppt-to-pdf`, {
          method: "POST",
          body: formData
        });
        
        if (!response.ok) throw new Error("Conversion failed");
        
        const data = await response.json();
        const pdfUrl = `${BACKEND_URL}/${data.pdf_path}`;
        const convertedFilename = data.pdf_path.split('/').pop();
        
        // Record conversion in database
        await fetch(`${BACKEND_URL}/record-conversion`, {
          method: "POST",
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            user_id: currentUserId,
            conversion_type: 'ppt_to_pdf',
            original_filename: originalFilename,
            converted_filename: convertedFilename,
            file_size: fileSize,
            status: 'completed',
            conversion_id: data.conversion_id || null
          })
        });
        
        // Set up download
        downloadLink.onclick = async function(e) {
          e.preventDefault();
          const downloadResponse = await fetch(pdfUrl);
          const blob = await downloadResponse.blob();
          const url = window.URL.createObjectURL(blob);
          const a = document.createElement('a');
          a.style.display = 'none';
          a.href = url;
          a.download = convertedFilename;
          document.body.appendChild(a);
          a.click();
          window.URL.revokeObjectURL(url);
          document.body.removeChild(a);
        };
        
        // Set up preview
        pdfPreview.src = pdfUrl;
        
        // Show results
        loadingIndicator.style.display = "none";
        previewContainer.style.display = "block";
        
      } catch (error) {
        loadingIndicator.style.display = "none";
        
        // Record failed conversion
        if (currentUserId) {
          await fetch(`${BACKEND_URL}/record-conversion`, {
            method: "POST",
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              user_id: currentUserId,
              conversion_type: 'ppt_to_pdf',
              original_filename: file ? file.name : 'unknown',
              file_size: file ? file.size : 0,
              status: 'failed',
              error_message: error.message
            })
          });
        }
        
        alert("Error: " + error.message);
      }
    });
  }

  // JPG to PDF Conversion
  const jpgToPdfForm = document.getElementById("jpgToPdfForm");
  if (jpgToPdfForm) {
    jpgToPdfForm.addEventListener("submit", async function(e) {
      e.preventDefault();
      
      const previewContainer = document.getElementById("jpgPreviewContainer");
      const loadingIndicator = document.getElementById("jpgLoadingIndicator");
      const pdfPreview = document.getElementById("jpgPdfPreview");
      const downloadLink = document.getElementById("jpgDownloadLink");
      const currentUserId = document.getElementById("currentUserId").value;

      // Reset UI
      previewContainer.style.display = "none";
      loadingIndicator.style.display = "block";
      
      try {
        // Get file details
        const fileInput = this.querySelector('input[type="file"]');
        const files = fileInput.files;
        const originalFilenames = Array.from(files).map(f => f.name).join(', ');
        const totalSize = Array.from(files).reduce((sum, file) => sum + file.size, 0);
        
        // Send files to backend
        const formData = new FormData(this);
        for (let i = 0; i < files.length; i++) {
          formData.append('jpg_file', files[i]);
        }
        
        const response = await fetch(`${BACKEND_URL}/convert-jpg-to-pdf`, {
          method: "POST",
          body: formData
        });
        
        if (!response.ok) throw new Error("Conversion failed");
        
        const data = await response.json();
        const pdfUrl = `${BACKEND_URL}/${data.pdf_path}`;
        const convertedFilename = data.pdf_path.split('/').pop();
        
        // Record conversion in database
        await fetch(`${BACKEND_URL}/record-conversion`, {
          method: "POST",
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            user_id: currentUserId,
            conversion_type: 'jpg_to_pdf',
            original_filename: originalFilenames,
            converted_filename: convertedFilename,
            file_size: totalSize,
            status: 'completed',
            conversion_id: data.conversion_id || null
          })
        });
        
        // Set up download
        downloadLink.onclick = async function(e) {
          e.preventDefault();
          const downloadResponse = await fetch(pdfUrl);
          const blob = await downloadResponse.blob();
          const url = window.URL.createObjectURL(blob);
          const a = document.createElement('a');
          a.style.display = 'none';
          a.href = url;
          a.download = convertedFilename;
          document.body.appendChild(a);
          a.click();
          window.URL.revokeObjectURL(url);
          document.body.removeChild(a);
        };
        
        // Set up preview
        pdfPreview.src = pdfUrl;
        
        // Show results
        loadingIndicator.style.display = "none";
        previewContainer.style.display = "block";
        
      } catch (error) {
        loadingIndicator.style.display = "none";
        
        // Record failed conversion
        if (currentUserId) {
          await fetch(`${BACKEND_URL}/record-conversion`, {
            method: "POST",
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              user_id: currentUserId,
              conversion_type: 'jpg_to_pdf',
              original_filename: fileInput.files.length > 0 ? 
                Array.from(fileInput.files).map(f => f.name).join(', ') : 'unknown',
              file_size: fileInput.files.length > 0 ? 
                Array.from(fileInput.files).reduce((sum, file) => sum + file.size, 0) : 0,
              status: 'failed',
              error_message: error.message
            })
          });
        }
        
        alert("Error: " + error.message);
      }
    });
  }

  // ==============================================
  // 2. PDF TO OTHER FORMATS CONVERSIONS
  // ==============================================

  // PDF to Word Conversion
  const pdfToWordForm = document.getElementById("pdfToWordForm");
  if (pdfToWordForm) {
    pdfToWordForm.addEventListener("submit", async function(e) {
      e.preventDefault();

      const loading = document.getElementById("pdfToWordLoading");
      const result = document.getElementById("pdfToWordResult");
      const downloadLink = document.getElementById("downloadWordLink");
      const editLink = document.getElementById("editWordLink");
      const previewContainer = document.getElementById("pdfToWordPreview");
      const previewContent = document.getElementById("previewContent");
      const currentUserId = document.getElementById("currentUserId").value;

      // Reset UI states
      loading.style.display = "block";
      result.style.display = "none";
      previewContainer.style.display = "none";
      previewContent.innerHTML = "";

      try {
        // Get file details
        const fileInput = this.querySelector('input[type="file"]');
        const file = fileInput.files[0];
        const originalFilename = file.name;
        const fileSize = file.size;

        const formData = new FormData(this);

        const response = await fetch(`${BACKEND_URL}/convert-pdf-to-word`, {
          method: "POST",
          body: formData
        });
        
        if (!response.ok) throw new Error("Conversion failed");
        
        const data = await response.json();
        const htmlPath = `${BACKEND_URL}/${data.word_path}`;
        const docxPath = `${BACKEND_URL}/${data.word_docx_path}`;
        const editorUrl = `${BACKEND_URL}/editor/${data.word_filename}`;
        const convertedFilename = data.word_docx_path.split('/').pop();
        
        // Record conversion in database
        await fetch(`${BACKEND_URL}/record-conversion`, {
          method: "POST",
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            user_id: currentUserId,
            conversion_type: 'pdf_to_word',
            original_filename: originalFilename,
            converted_filename: convertedFilename,
            file_size: fileSize,
            status: 'completed',
            conversion_id: data.conversion_id || null
          })
        });

        // Set up download
        downloadLink.href = docxPath;
        downloadLink.setAttribute('download', convertedFilename);
        
        // Set up edit link
        editLink.href = editorUrl;

        // Set up preview
        const previewResponse = await fetch(htmlPath);
        const html = await previewResponse.text();
        previewContent.innerHTML = html;
        previewContainer.style.display = "block";

        // Show results
        loading.style.display = "none";
        result.style.display = "block";

      } catch (error) {
        loading.style.display = "none";
        
        // Record failed conversion
        if (currentUserId) {
          await fetch(`${BACKEND_URL}/record-conversion`, {
            method: "POST",
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              user_id: currentUserId,
              conversion_type: 'pdf_to_word',
              original_filename: file ? file.name : 'unknown',
              file_size: file ? file.size : 0,
              status: 'failed',
              error_message: error.message
            })
          });
        }
        
        alert("Error: " + error.message);
      }
    });
  }

  // PDF to Excel Conversion
  const pdfToExcelForm = document.getElementById("pdfToExcelForm");
  if (pdfToExcelForm) {
    pdfToExcelForm.addEventListener("submit", async function(e) {
      e.preventDefault();

      const loading = document.getElementById("pdfToExcelLoading");
      const result = document.getElementById("pdfToExcelResult");
      const downloadLink = document.getElementById("downloadExcelLink");
      const editLink = document.getElementById("editExcelLink");
      const previewContainer = document.getElementById("pdfToExcelPreview");
      const previewContent = document.getElementById("excelPreviewContent");
      const currentUserId = document.getElementById("currentUserId").value;
      const fileInput = this.querySelector('input[type="file"]');
      const file = fileInput.files[0];
      const originalFilename = file.name;
      const fileSize = file.size;

      loading.style.display = "block";
      result.style.display = "none";
      previewContainer.style.display = "none";
      previewContent.textContent = "";

      try {
        const formData = new FormData(this);
        
        // Convert PDF to Excel
        const response = await fetch(`${BACKEND_URL}/convert-pdf-to-excel`, {
          method: "POST",
          body: formData
        });
        
        const data = await response.json();
        if (!response.ok) {
          throw new Error(data.error || "Conversion failed");
        }

        // Record the conversion in database
        if (currentUserId) {
          await fetch(`${BACKEND_URL}/record-conversion`, {
            method: "POST",
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              user_id: currentUserId,
              conversion_type: 'pdf_to_excel',
              original_filename: originalFilename,
              converted_filename: data.excel_filename,
              file_size: fileSize,
              status: 'completed',
              conversion_id: data.conversion_id || null
            })
          });
        }

        const excelPath = `${BACKEND_URL}/download/${data.excel_filename}`;
        const editorUrl = `${BACKEND_URL}/excel-editor/${data.excel_filename}`;

        downloadLink.href = excelPath;
        editLink.href = editorUrl;
        downloadLink.download = data.excel_filename;

        // Basic preview
        const previewResponse = await fetch(`${BACKEND_URL}/converted/${data.excel_filename}`);
        const buffer = await previewResponse.arrayBuffer();
        const dataArray = new Uint8Array(buffer);
        const workbook = XLSX.read(dataArray, { type: "array" });
        const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
        const csv = XLSX.utils.sheet_to_csv(firstSheet);
        previewContent.textContent = csv;
        previewContainer.style.display = "block";

        result.style.display = "block";
        this.reset();

      } catch (error) {
        console.error("Error:", error);
        
        // Record failed conversion
        if (currentUserId) {
          await fetch(`${BACKEND_URL}/record-conversion`, {
            method: "POST",
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              user_id: currentUserId,
              conversion_type: 'pdf_to_excel',
              original_filename: originalFilename,
              file_size: fileSize,
              status: 'failed',
              error_message: error.message
            })
          });
        }
        
        alert("Conversion failed: " + error.message);
      } finally {
        loading.style.display = "none";
      }
    });
  }

  // PDF to PowerPoint Conversion
  const pdfToPptForm = document.getElementById("pdfToPptForm");
  if (pdfToPptForm) {
    pdfToPptForm.addEventListener("submit", async function(e) {
      e.preventDefault();

      const loading = document.getElementById("pdfToPptLoading");
      const result = document.getElementById("pdfToPptResult");
      const downloadLink = document.getElementById("downloadPptLink");
      const currentUserId = document.getElementById("currentUserId").value;
      const fileInput = this.querySelector('input[type="file"]');
      const file = fileInput.files[0];
      const originalFilename = file.name;
      const fileSize = file.size;

      loading.style.display = "block";
      result.style.display = "none";

      try {
        const formData = new FormData(this);
        
        // Convert PDF to PPT
        const response = await fetch(`${BACKEND_URL}/convert-pdf-to-ppt`, {
          method: "POST",
          body: formData
        });
        
        const data = await response.json();
        if (!response.ok) {
          throw new Error(data.error || "Conversion failed");
        }

        // Record the conversion in database
        if (currentUserId) {
          await fetch(`${BACKEND_URL}/record-conversion`, {
            method: "POST",
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              user_id: currentUserId,
              conversion_type: 'pdf_to_ppt',
              original_filename: originalFilename,
              converted_filename: data.ppt_filename,
              file_size: fileSize,
              status: 'completed',
              conversion_id: data.conversion_id || null
            })
          });
        }

        const pptPath = `${BACKEND_URL}/download/${data.ppt_filename}`;
        downloadLink.href = pptPath;
        downloadLink.download = data.ppt_filename;
        result.style.display = "block";
        this.reset();

      } catch (error) {
        console.error("Error:", error);
        
        // Record failed conversion
        if (currentUserId) {
          await fetch(`${BACKEND_URL}/record-conversion`, {
            method: "POST",
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              user_id: currentUserId,
              conversion_type: 'pdf_to_ppt',
              original_filename: originalFilename,
              file_size: fileSize,
              status: 'failed',
              error_message: error.message
            })
          });
        }
        
        alert("Conversion failed: " + error.message);
      } finally {
        loading.style.display = "none";
      }
    });
  }

  // PDF to JPG Conversion
  const pdfToJpgForm = document.getElementById("pdfToJpgForm");
  if (pdfToJpgForm) {
    pdfToJpgForm.addEventListener("submit", function(e) {
      e.preventDefault();

      const loading = document.getElementById("pdfToJpgLoading");
      const result = document.getElementById("pdfToJpgResult");
      const downloadLink = document.getElementById("downloadZipLink");
      const previewImages = document.getElementById("previewImages");
      const currentUserId = document.getElementById("currentUserIdPdfToJpg").value;

      loading.style.display = "block";
      result.style.display = "none";
      previewImages.innerHTML = "";

      const formData = new FormData(this);
      const fileInput = this.querySelector('input[type="file"]');
      const file = fileInput.files[0];
      const originalFilename = file.name;
      const fileSize = file.size;

      // First convert the PDF to JPG
      fetch(`${BACKEND_URL}/convert-pdf-to-jpg`, {
        method: "POST",
        body: formData
      })
      .then((res) => res.json())
      .then((data) => {
        loading.style.display = "none";

        if (data.error) {
          throw new Error(data.error);
        }

        const zipPath = `${BACKEND_URL}${data.zipUrl}`;
        downloadLink.href = zipPath;
        downloadLink.download = data.zipFilename || "converted_images.zip";

        // Create preview images
        data.imageUrls.forEach((url) => {
          const img = document.createElement("img");
          img.src = `${BACKEND_URL}${url}`;
          img.className = "img-thumbnail";
          img.style.maxWidth = "150px";
          img.style.maxHeight = "150px";
          previewImages.appendChild(img);
        });

        // Record successful conversion (fire-and-forget)
        if (currentUserId) {
          fetch(`${BACKEND_URL}/record-conversion`, {
            method: "POST",
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              user_id: currentUserId,
              conversion_type: 'pdf_to_jpg',
              original_filename: originalFilename,
              converted_filename: data.zipFilename || "converted_images.zip",
              file_size: fileSize,
              status: 'completed',
              conversion_id: data.conversion_id || null
            })
          }).catch(err => console.log("Recording failed (non-critical):", err));
        }

        result.style.display = "block";
        this.reset();
      })
      .catch((error) => {
        loading.style.display = "none";
        
        // Record failed conversion (fire-and-forget)
        if (currentUserId) {
          fetch(`${BACKEND_URL}/record-conversion`, {
            method: "POST",
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              user_id: currentUserId,
              conversion_type: 'pdf_to_jpg',
              original_filename: originalFilename,
              file_size: fileSize,
              status: 'failed',
              error_message: error.message
            })
          }).catch(err => console.log("Recording failed (non-critical):", err));
        }
        
        alert("Error: " + error.message);
      });
    });
  }

  // PDF to PNG Conversion
  const pdfToPngForm = document.getElementById("pdfToPngForm");
  if (pdfToPngForm) {
    pdfToPngForm.addEventListener("submit", function(e) {
      e.preventDefault();

      const loading = document.getElementById("pdfToPngLoading");
      const result = document.getElementById("pdfToPngResult");
      const downloadLink = document.getElementById("downloadPngZipLink");
      const previewImages = document.getElementById("previewPngImages");
      const currentUserId = document.getElementById("currentUserIdPdfToPng").value;

      loading.style.display = "block";
      result.style.display = "none";
      previewImages.innerHTML = "";

      const formData = new FormData(this);
      const fileInput = this.querySelector('input[type="file"]');
      const file = fileInput.files[0];
      const originalFilename = file.name;
      const fileSize = file.size;

      fetch(`${BACKEND_URL}/convert-pdf-to-png`, {
        method: "POST",
        body: formData
      })
      .then((res) => res.json())
      .then((data) => {
        loading.style.display = "none";

        if (data.error) {
          throw new Error(data.error);
        }

        const zipPath = `${BACKEND_URL}${data.zipUrl}`;
        downloadLink.href = zipPath;
        downloadLink.download = data.zipFilename || "converted_images.zip";

        data.imageUrls.forEach((url) => {
          const img = document.createElement("img");
          img.src = `${BACKEND_URL}${url}`;
          img.className = "img-thumbnail";
          img.style.maxWidth = "150px";
          img.style.maxHeight = "150px";
          previewImages.appendChild(img);
        });

        // Record successful conversion
        if (currentUserId) {
          fetch(`${BACKEND_URL}/record-conversion`, {
            method: "POST",
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              user_id: currentUserId,
              conversion_type: 'pdf_to_png',
              original_filename: originalFilename,
              converted_filename: data.zipFilename || "converted_images.zip",
              file_size: fileSize,
              status: 'completed',
              conversion_id: data.conversion_id || null
            })
          }).catch(err => console.log("Recording failed (non-critical):", err));
        }

        result.style.display = "block";
        this.reset();
      })
      .catch((error) => {
        loading.style.display = "none";
        
        // Record failed conversion
        if (currentUserId) {
          fetch(`${BACKEND_URL}/record-conversion`, {
            method: "POST",
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              user_id: currentUserId,
              conversion_type: 'pdf_to_png',
              original_filename: originalFilename,
              file_size: fileSize,
              status: 'failed',
              error_message: error.message
            })
          }).catch(err => console.log("Recording failed (non-critical):", err));
        }
        
        alert("Error: " + error.message);
      });
    });
  }

  // PDF to PDF/A Conversion
  const pdfToPdfaForm = document.getElementById("pdfToPdfaForm");
  if (pdfToPdfaForm) {
    pdfToPdfaForm.addEventListener("submit", function(e) {
      e.preventDefault();

      const resultDiv = document.getElementById("pdfaResult");
      const loadingIndicator = document.getElementById("pdfaLoading");
      const currentUserId = document.getElementById("currentUserIdPdfToPdfa").value;
      const fileInput = this.querySelector('input[type="file"]');
      const file = fileInput.files[0];
      const originalFilename = file.name;
      const fileSize = file.size;
      const pdfaVersion = this.querySelector('#pdfaVersion').value;
      
      // Clear previous results and show loading
      resultDiv.innerHTML = "";
      loadingIndicator.style.display = "block";
      this.querySelector("button[type='submit']").disabled = true;

      const formData = new FormData(this);

      fetch(`${BACKEND_URL}/convert-to-pdfa`, {
        method: "POST",
        body: formData,
        headers: {
          'Accept': 'application/json'
        }
      })
      .then(async response => {
        if (!response.ok) {
          const errorData = await response.json();
          throw new Error(errorData.error || "Failed to convert PDF");
        }
        return await response.json();
      })
      .then(data => {
        // Create direct download URL with encoded filename
        const downloadUrl = `${BACKEND_URL}/download-pdfa/${encodeURIComponent(data.filename)}`;
        
        // Create download button
        const downloadBtn = document.createElement("a");
        downloadBtn.href = downloadUrl;
        downloadBtn.className = "btn btn-success mt-2";
        downloadBtn.textContent = "Download PDF/A File";
        downloadBtn.setAttribute('download', data.filename || "converted.pdf");
        
        // Create result display
        resultDiv.innerHTML = `
          <div class="alert alert-success">
            <i class="bi bi-check-circle-fill"></i> Successfully converted to PDF/A-${data.pdfa_version}
          </div>
        `;
        resultDiv.appendChild(downloadBtn);

        // Auto-click the download button after a short delay
        setTimeout(() => {
          downloadBtn.click();
        }, 300);

        // Record successful conversion
        if (currentUserId) {
          fetch(`${BACKEND_URL}/record-conversion`, {
            method: "POST",
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              user_id: currentUserId,
              conversion_type: 'pdf_to_pdfa',
              original_filename: originalFilename,
              converted_filename: data.filename,
              file_size: fileSize,
              status: 'completed',
              conversion_id: data.conversion_id || null,
              pdfa_version: data.pdfa_version
            })
          }).catch(err => console.error("Conversion recording error:", err));
        }
      })
      .catch(error => {
        console.error("Conversion error:", error);
        resultDiv.innerHTML = `
          <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle-fill"></i> ${error.message || 'An error occurred during conversion'}
          </div>`;
        
        // Record failed conversion
        if (currentUserId) {
          fetch(`${BACKEND_URL}/record-conversion`, {
            method: "POST",
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              user_id: currentUserId,
              conversion_type: 'pdf_to_pdfa',
              original_filename: originalFilename,
              file_size: fileSize,
              status: 'failed',
              error_message: error.message.substring(0, 255),
              pdfa_version: pdfaVersion,
              conversion_id: 'failed_' + Date.now()
            })
          }).catch(err => console.error("Error recording failed conversion:", err));
        }
      })
      .finally(() => {
        loadingIndicator.style.display = "none";
        this.querySelector("button[type='submit']").disabled = false;
      });
    });
  }

  // ==============================================
  // 3. PDF MANIPULATION TOOLS
  // ==============================================

  // Edit PDF
  const uploadForm = document.getElementById("uploadForm");
  if (uploadForm) {
    uploadForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      const formData = new FormData(e.target);
      const submitBtn = e.target.querySelector('button[type="submit"]');
      const originalBtnText = submitBtn.innerHTML;
      const currentUserId = document.getElementById("currentUserId")?.value || 'anonymous';

      try {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';

        // Add user_id to form data if not already included
        if (!formData.has('user_id')) {
          formData.append('user_id', currentUserId);
        }

        const response = await fetch(`${BACKEND_URL}/upload-edit-pdf`, {
          method: "POST",
          body: formData,
          headers: {
            'Accept': 'application/json'
          }
        });

        if (!response.ok) {
          const errorData = await response.json().catch(() => ({}));
          // Record failed conversion
          if (currentUserId) {
            await fetch(`${BACKEND_URL}/record-conversion`, {
              method: "POST",
              headers: {
                'Content-Type': 'application/json',
              },
              body: JSON.stringify({
                user_id: currentUserId,
                conversion_type: 'pdf_editing',
                original_filename: formData.get('pdfFile').name,
                file_size: formData.get('pdfFile').size,
                status: 'failed',
                error_message: errorData.error || `HTTP ${response.status}`
              })
            });
          }
          throw new Error(errorData.error || `Failed to process PDF (status: ${response.status})`);
        }

        const result = await response.json();

        if (result.success) {
          const editorUrl = `${BACKEND_URL}/pdf-editor-view?file=${encodeURIComponent(result.pdfFileName)}&user_id=${currentUserId}&conversion_id=${result.conversion_id || ''}`;
          window.open(editorUrl, "_blank");
        } else {
          throw new Error(result.error || "Failed to process PDF");
        }
      } catch (error) {
        let errorMsg = error.message;
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
  }

  // Merge PDFs
  const mergePDFForm = document.getElementById("mergePDFForm");
  if (mergePDFForm) {
    mergePDFForm.addEventListener("submit", function(e) {
      e.preventDefault();

      // Get user info for recording
      const currentUserId = document.getElementById("currentUserIdMergePDF")?.value;
      const files = this.querySelector('input[type="file"]').files;
      const fileNames = Array.from(files).map(file => file.name).join(', ');
      const totalSize = Array.from(files).reduce((sum, file) => sum + file.size, 0);

      // Reset UI
      const pdfPreviewContainer = document.getElementById("pdfPreviewContainer");
      const downloadLink = document.getElementById("downloadMergedPDF");
      const closePreviewBtn = document.getElementById("closePreview");
      const loadingIndicator = document.getElementById("mergePDFLoading");
      
      pdfPreviewContainer.style.display = "none";
      downloadLink.style.display = "none";
      loadingIndicator.style.display = "block";

      const formData = new FormData(this);

      fetch(`${BACKEND_URL}/merge-pdfs`, {
        method: "POST",
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(response => {
        if (!response.ok) {
          return response.json().then(err => { 
            // Record failed conversion
            if (currentUserId) {
              fetch(`${BACKEND_URL}/record-conversion`, {
                method: "POST",
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                  user_id: currentUserId,
                  conversion_type: 'merge_pdfs',
                  original_filename: fileNames,
                  file_size: totalSize,
                  status: 'failed',
                  error_message: err.error || "Merge failed"
                })
              }).catch(e => console.error("Recording failed silently"));
            }
            throw new Error(err.error || "Failed to merge PDFs"); 
          });
        }
        return response.json();
      })
      .then(data => {
        if (data.error) throw new Error(data.error);

        // Update preview iframe
        const iframeContainer = pdfPreviewContainer.querySelector('.ratio');
        iframeContainer.innerHTML = ''; 
        
        const newIframe = document.createElement("iframe");
        newIframe.id = "pdfPreview";
        newIframe.style.width = "100%";
        newIframe.style.height = "100%";
        newIframe.style.border = "none";
        newIframe.src = `${BACKEND_URL}${data.pdf_path}?t=${Date.now()}`;
        iframeContainer.appendChild(newIframe);

        // Update UI
        pdfPreviewContainer.style.display = "block";
        downloadLink.href = `${BACKEND_URL}${data.pdf_path}?download=true`;
        downloadLink.download = data.filename || "merged.pdf";
        downloadLink.style.display = "inline-block";
        bootstrap.Modal.getInstance(document.getElementById('mergePDFModal')).show();

        // Record successful conversion
        if (currentUserId) {
          fetch(`${BACKEND_URL}/record-conversion`, {
            method: "POST",
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              user_id: currentUserId,
              conversion_type: 'merge_pdfs',
              original_filename: fileNames,
              converted_filename: data.filename,
              file_size: totalSize,
              status: 'completed',
              conversion_id: data.conversion_id || null
            })
          }).catch(e => console.error("Recording failed silently"));
        }
      })
      .catch(error => {
        console.error("Error:", error);
        alert("Error: " + error.message);
      })
      .finally(() => {
        loadingIndicator.style.display = "none";
      });
    });

    // Close preview handler
    const closePreviewBtn = document.getElementById("closePreview");
    if (closePreviewBtn) {
      closePreviewBtn.addEventListener("click", function() {
        document.getElementById("pdfPreviewContainer").style.display = "none";
      });
    }

    // Improved download handler
    const downloadLink = document.getElementById("downloadMergedPDF");
    if (downloadLink) {
      downloadLink.addEventListener("click", function(e) {
        e.preventDefault();
        const url = this.href;
        
        fetch(url)
          .then(response => response.blob())
          .then(blob => {
            const downloadUrl = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = downloadUrl;
            a.download = this.download || "merged.pdf";
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(downloadUrl);
            a.remove();
          })
          .catch(err => {
            console.error("Download failed:", err);
            alert("Download failed. Please try again.");
          });
      });
    }
  }

  // Split PDF
  const splitPDFForm = document.getElementById("splitPDFForm");
  if (splitPDFForm) {
    splitPDFForm.addEventListener("submit", function(e) {
      e.preventDefault();

      // Get user info for recording
      const currentUserId = document.getElementById("currentUserIdSplitPDF")?.value;
      const file = this.querySelector('input[type="file"]').files[0];
      const originalFilename = file?.name || "unknown.pdf";
      const fileSize = file?.size || 0;

      const formData = new FormData(this);

      fetch(`${BACKEND_URL}/upload-pdf`, {
        method: "POST",
        body: formData,
      })
      .then(response => response.json())
      .then(data => {
        if (data.error) throw new Error(data.error);

        // Record successful upload (fire-and-forget)
        if (currentUserId) {
          fetch(`${BACKEND_URL}/record-conversion`, {
            method: "POST",
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              user_id: currentUserId,
              conversion_type: 'split_pdf_upload',
              original_filename: originalFilename,
              file_size: fileSize,
              status: 'uploaded',
              conversion_id: data.conversion_id || null
            })
          }).catch(e => console.error("Recording failed silently"));
        }

        // Open editor with the uploaded file
        const encodedFilename = encodeURIComponent(data.file_path);
        const editorUrl = `${BACKEND_URL}/split-editor?file=${encodedFilename}&user_id=${currentUserId}`;
        window.open(editorUrl, "_blank");
      })
      .catch(error => {
        console.error("Upload error:", error);
        alert("Error: " + error.message);
        
        // Record failed upload (fire-and-forget)
        if (currentUserId) {
          fetch(`${BACKEND_URL}/record-conversion`, {
            method: "POST",
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              user_id: currentUserId,
              conversion_type: 'split_pdf_upload',
              original_filename: originalFilename,
              file_size: fileSize,
              status: 'failed',
              error_message: error.message.substring(0, 255)
            })
          }).catch(e => console.error("Recording failed silently"));
        }
      });
    });
  }

  // Protect PDF
  const protectPDFForm = document.getElementById("protectPDFForm");
  if (protectPDFForm) {
    protectPDFForm.addEventListener("submit", async function(e) {
      e.preventDefault();

      const downloadLinks = document.getElementById("downloadLinksProtect");
      const loadingIndicator = document.getElementById("protectLoading");
      const currentUserId = document.getElementById("currentUserId").value;
      const fileInput = this.querySelector('input[type="file"]');
      const file = fileInput.files[0];
      const originalFilename = file.name;
      const fileSize = file.size;

      // Clear previous results and show loading
      downloadLinks.innerHTML = "";
      loadingIndicator.style.display = "block";
      this.querySelector("button[type='submit']").disabled = true;

      try {
        const formData = new FormData(this);
        
        // Protect the PDF
        const response = await fetch(`${BACKEND_URL}/protect-pdf`, {
          method: "POST",
          body: formData,
          headers: {
            'Accept': 'application/json',
          }
        });
        
        if (!response.ok) {
          const err = await response.json();
          throw new Error(err.error || "Failed to protect PDF");
        }
        
        const data = await response.json();
        if (data.error) throw new Error(data.error);

        // Record the conversion in database
        if (currentUserId) {
          await fetch(`${BACKEND_URL}/record-conversion`, {
            method: "POST",
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              user_id: currentUserId,
              conversion_type: 'protect_pdf',
              original_filename: originalFilename,
              converted_filename: data.filename,
              file_size: fileSize,
              status: 'completed',
              conversion_id: data.conversion_id || null
            })
          });
        }

        // Create download link
        const link = document.createElement("a");
        link.href = data.download_url;
        link.className = "btn btn-success";
        link.textContent = "Download Protected PDF";
        link.download = data.filename || "protected.pdf";

        downloadLinks.innerHTML = "";
        downloadLinks.appendChild(link);

      } catch (error) {
        console.error("Error:", error);
        
        // Record failed conversion
        if (currentUserId) {
          await fetch(`${BACKEND_URL}/record-conversion`, {
            method: "POST",
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              user_id: currentUserId,
              conversion_type: 'protect_pdf',
              original_filename: originalFilename,
              file_size: fileSize,
              status: 'failed',
              error_message: error.message
            })
          });
        }
        
        downloadLinks.innerHTML = `<div class="alert alert-danger">Error: ${error.message}</div>`;
      } finally {
        loadingIndicator.style.display = "none";
        this.querySelector("button[type='submit']").disabled = false;
      }
    });
  }

  // Unlock PDF
  const unlockPDFForm = document.getElementById("unlockPDFForm");
  if (unlockPDFForm) {
    unlockPDFForm.addEventListener("submit", async function(e) {
      e.preventDefault();

      const downloadLinks = document.getElementById("downloadLinksUnlock");
      const loadingIndicator = document.getElementById("unlockLoading");
      const currentUserId = document.getElementById("currentUserId").value;
      const fileInput = this.querySelector('input[type="file"]');
      const file = fileInput.files[0];
      const originalFilename = file.name;
      const fileSize = file.size;

      // Clear previous results and show loading
      downloadLinks.innerHTML = "";
      loadingIndicator.style.display = "block";
      this.querySelector("button[type='submit']").disabled = true;

      try {
        const formData = new FormData(this);
        
        // Unlock the PDF
        const response = await fetch(`${BACKEND_URL}/unlock-pdf`, {
          method: "POST",
          body: formData,
          headers: {
            'Accept': 'application/json'
          }
        });
        
        const data = await response.json();
        if (!response.ok) {
          throw new Error(data.error || "Failed to unlock PDF");
        }

        // Record the conversion in database
        if (currentUserId) {
          await fetch(`${BACKEND_URL}/record-conversion`, {
            method: "POST",
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              user_id: currentUserId,
              conversion_type: 'unlock_pdf',
              original_filename: originalFilename,
              converted_filename: data.filename,
              file_size: fileSize,
              status: 'completed',
              conversion_id: data.conversion_id || null
            })
          });
        }

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

      } catch (error) {
        console.error("Error:", error);
        
        // Record failed conversion
        if (currentUserId) {
          await fetch(`${BACKEND_URL}/record-conversion`, {
            method: "POST",
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              user_id: currentUserId,
              conversion_type: 'unlock_pdf',
              original_filename: originalFilename,
              file_size: fileSize,
              status: 'failed',
              error_message: error.message
            })
          });
        }
        
        downloadLinks.innerHTML = `<div class="alert alert-danger">${error.message}</div>`;
      } finally {
        loadingIndicator.style.display = "none";
        this.querySelector("button[type='submit']").disabled = false;
      }
    });
  }

  // ==============================================
  // 4. SPECIALIZED TOOLS
  // ==============================================

  // iWork to PDF Conversion
  const iworkFileInput = document.getElementById("iworkFileInput");
  if (iworkFileInput) {
    // Set PDF.js worker path if not already set globally
    if (typeof pdfjsLib !== 'undefined' && !pdfjsLib.GlobalWorkerOptions.workerSrc) {
      pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.12.313/pdf.worker.min.js';
    }

    const fileInfo = document.getElementById("fileInfo");
    const fileName = document.getElementById("fileName");
    const convertBtn = document.getElementById("convertBtn");
    const pdfDisplayArea = document.getElementById("pdfDisplayArea");
    const downloadBtn = document.getElementById("downloadBtn");
    const convertAnotherBtn = document.getElementById("convertAnotherBtn");
    const currentUserId = document.getElementById("currentUserId").value;

    let currentFile = null;
    let pdfBlob = null;
    let conversionId = null;

    // Reset UI function
    function resetConversionUI() {
      iworkFileInput.value = '';
      currentFile = null;
      pdfBlob = null;
      conversionId = null;
      fileInfo.classList.add('d-none');
      pdfDisplayArea.classList.add('d-none');
      document.getElementById("pdfViewer").innerHTML = '';
      convertBtn.disabled = false;
      convertBtn.innerHTML = '<i class="bi bi-file-earmark-pdf"></i> Convert to PDF';
    }

    // File selection handler
    iworkFileInput.addEventListener("change", function(e) {
      if (e.target.files.length > 0) {
        currentFile = e.target.files[0];
        fileName.textContent = currentFile.name;
        fileInfo.classList.remove('d-none');
        pdfDisplayArea.classList.add('d-none');
      }
    });

    // Convert button handler
    convertBtn.addEventListener("click", async function() {
      if (!currentFile) {
        alert('Please select a file first');
        return;
      }

      convertBtn.disabled = true;
      convertBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Converting...';

      try {
        const formData = new FormData();
        formData.append('file', currentFile);
        formData.append('user_id', currentUserId);
        conversionId = uuidv4();

        // Convert the file
        const conversionResponse = await fetch(`${BACKEND_URL}/convert-iwork-to-pdf`, {
          method: 'POST',
          body: formData
        });

        if (!conversionResponse.ok) throw new Error('Conversion failed');
        
        const result = await conversionResponse.json();
        if (!result.success) {
          throw new Error(result.message || 'Conversion failed');
        }

        // Record successful conversion
        if (currentUserId) {
          await fetch(`${BACKEND_URL}/record-conversion`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              user_id: currentUserId,
              conversion_type: 'iwork_to_pdf',
              original_filename: currentFile.name,
              converted_filename: result.filename,
              file_size: currentFile.size,
              status: 'completed',
              conversion_id: conversionId
            })
          });
        }

        // Get the PDF as blob
        const pdfResponse = await fetch(`${BACKEND_URL}${result.pdfUrl}`);
        pdfBlob = await pdfResponse.blob();
        
        // Display PDF using PDF.js
        const pdfObjectUrl = URL.createObjectURL(pdfBlob);
        
        // Show the display area 
        pdfDisplayArea.classList.remove('d-none');
        fileInfo.classList.add('d-none');
        
        // Check if PDF.js is loaded
        if (typeof pdfjsLib === 'undefined') {
          throw new Error('PDF preview library failed to load. Please try downloading the file instead.');
        }

        // Render PDF using PDF.js
        try {
          const loadingTask = pdfjsLib.getDocument(pdfObjectUrl);
          loadingTask.promise.then(function(pdf) {
            const viewer = document.getElementById("pdfViewer");
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
          }).catch(function(error) {
            console.error('PDF rendering error:', error);
            document.getElementById("pdfViewer").innerHTML = 
              '<div class="alert alert-warning">PDF preview unavailable. You can still download the file.</div>';
          });
        } catch (error) {
          console.error('PDF.js error:', error);
          document.getElementById("pdfViewer").innerHTML = 
            '<div class="alert alert-warning">PDF preview unavailable. You can still download the file.</div>';
        }

        // Set up download
        downloadBtn.onclick = async function() {
          try {
            const a = document.createElement('a');
            a.href = pdfObjectUrl;
            a.download = currentFile.name.replace(/\.[^.]+$/, '') + '.pdf';
            document.body.appendChild(a);
            a.click();
            
            // Record download event
            if (currentUserId) {
              await fetch(`${BACKEND_URL}/record-download`, {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                  user_id: currentUserId,
                  conversion_id: conversionId,
                  file_name: result.filename,
                  action: 'download'
                })
              });
            }
            
            setTimeout(() => {
              document.body.removeChild(a);
              URL.revokeObjectURL(pdfObjectUrl);
            }, 100);
          } catch (error) {
            console.error('Download tracking error:', error);
          }
        };

      } catch (error) {
        console.error('Error:', error);
        
        // Record failed conversion
        if (currentUserId) {
          await fetch(`${BACKEND_URL}/record-conversion`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              user_id: currentUserId,
              conversion_type: 'iwork_to_pdf',
              original_filename: currentFile.name,
              file_size: currentFile.size,
              status: 'failed',
              error_message: error.message,
              conversion_id: conversionId || null
            })
          });
        }
        
        alert('Conversion failed: ' + error.message);
        resetConversionUI();
      } finally {
        convertBtn.disabled = false;
        convertBtn.innerHTML = '<i class="bi bi-file-earmark-pdf"></i> Convert to PDF';
      }
    });

    // Convert another handler
    convertAnotherBtn.addEventListener('click', resetConversionUI);
  }

  // eBooks to PDF Conversion
  const ebookFileInput = document.getElementById("ebookFileInput");
  if (ebookFileInput) {
    // Initialize modal
    const ebookModal = new bootstrap.Modal(document.getElementById('ebookToPdfModal'));

    const convertEbookBtn = document.getElementById("convertEbookBtn");
    const ebookPreviewContainer = document.getElementById("ebookPreviewContainer");
    const downloadEbookPdfBtn = document.getElementById("downloadEbookPdfBtn");
    const convertAnotherEbookBtn = document.getElementById("convertAnotherEbookBtn");
    const currentUserId = document.getElementById("currentUserId").value;

    let currentEbookFile = null;
    let ebookPdfBlob = null;
    let conversionId = null;

    // Reset function
    function resetEbookConversionUI() {
      ebookFileInput.value = '';
      currentEbookFile = null;
      ebookPdfBlob = null;
      conversionId = null;
      ebookPreviewContainer.classList.add('d-none');
      document.getElementById("ebookPdfViewer").innerHTML = '';
      convertEbookBtn.disabled = false;
      convertEbookBtn.innerHTML = '<i class="bi bi-file-earmark-pdf"></i> Convert to PDF';
    }

    // File selection handler
    ebookFileInput.addEventListener("change", function(e) {
      if (e.target.files.length > 0) {
        currentEbookFile = e.target.files[0];
      }
    });

    // Convert button handler
    convertEbookBtn.addEventListener("click", async function() {
      if (!currentEbookFile) {
        alert('Please select an eBook file first');
        return;
      }

      convertEbookBtn.disabled = true;
      convertEbookBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Converting...';
      conversionId = uuidv4();

      try {
        const formData = new FormData();
        formData.append('file', currentEbookFile);
        formData.append('user_id', currentUserId);

        const conversionResponse = await fetch(`${BACKEND_URL}/convert-ebook-to-pdf`, {
          method: 'POST',
          body: formData
        });

        const result = await conversionResponse.json();

        if (!conversionResponse.ok || !result.success) {
          throw new Error(result.message || 'Conversion failed');
        }

        // Record successful conversion
        if (currentUserId) {
          await fetch(`${BACKEND_URL}/record-conversion`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              user_id: currentUserId,
              conversion_type: 'ebook_to_pdf',
              original_filename: currentEbookFile.name,
              converted_filename: result.filename,
              file_size: currentEbookFile.size,
              status: 'completed',
              conversion_id: conversionId
            })
          });
        }

        const pdfResponse = await fetch(`${BACKEND_URL}${result.pdfUrl}`);
        if (!pdfResponse.ok) {
          throw new Error('Failed to fetch the converted PDF');
        }

        ebookPdfBlob = await pdfResponse.blob();
        const pdfObjectUrl = URL.createObjectURL(ebookPdfBlob);

        ebookPreviewContainer.classList.remove('d-none');

        await renderPdfPreview(pdfObjectUrl);

        downloadEbookPdfBtn.onclick = async function() {
          const a = document.createElement('a');
          a.href = pdfObjectUrl;
          a.download = currentEbookFile.name.replace(/\.[^.]+$/, '') + '.pdf';
          document.body.appendChild(a);
          a.click();
          
          // Record download event
          if (currentUserId) {
            await fetch(`${BACKEND_URL}/record-download`, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
              },
              body: JSON.stringify({
                user_id: currentUserId,
                conversion_id: conversionId,
                file_name: result.filename,
                action: 'download'
              })
            });
          }
          
          setTimeout(() => {
            document.body.removeChild(a);
            URL.revokeObjectURL(pdfObjectUrl);
          }, 100);
        };

      } catch (error) {
        console.error('Conversion error:', error);
        
        // Record failed conversion
        if (currentUserId) {
          await fetch(`${BACKEND_URL}/record-conversion`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              user_id: currentUserId,
              conversion_type: 'ebook_to_pdf',
              original_filename: currentEbookFile?.name || 'unknown',
              file_size: currentEbookFile?.size || 0,
              status: 'failed',
              error_message: error.message,
              conversion_id: conversionId || null
            })
          });
        }
        
        alert('Conversion failed: ' + error.message);
        resetEbookConversionUI();
      } finally {
        convertEbookBtn.disabled = false;
        convertEbookBtn.innerHTML = '<i class="bi bi-file-earmark-pdf"></i> Convert to PDF';
      }
    });

    // PDF preview rendering function
    async function renderPdfPreview(pdfUrl) {
      try {
        if (typeof pdfjsLib === 'undefined') {
          throw new Error('PDF preview library not loaded');
        }
        
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.12.313/pdf.worker.min.js';
        
        const pdf = await pdfjsLib.getDocument(pdfUrl).promise;
        const viewer = document.getElementById("ebookPdfViewer");
        viewer.innerHTML = '';
        
        const page = await pdf.getPage(1);
        const viewport = page.getViewport({ scale: 1.0 });
        const scale = (viewer.parentElement.clientWidth - 40) / viewport.width;
        const scaledViewport = page.getViewport({ scale: scale });

        const canvas = document.createElement('canvas');
        canvas.height = scaledViewport.height;
        canvas.width = scaledViewport.width;
        canvas.classList.add('img-fluid', 'mb-2');
        viewer.appendChild(canvas);

        await page.render({
          canvasContext: canvas.getContext('2d'),
          viewport: scaledViewport
        }).promise;

      } catch (error) {
        console.error('PDF preview error:', error);
        document.getElementById("ebookPdfViewer").innerHTML = `
          <div class="alert alert-warning">
            PDF preview could not be displayed. You can still download the file.
          </div>
        `;
      }
    }

    convertAnotherEbookBtn.addEventListener('click', resetEbookConversionUI);
  }

  // Sign Document
  const signingDocumentForm = document.getElementById("signingDocumentForm");
  if (signingDocumentForm) {
    signingDocumentForm.addEventListener("submit", function(e) {
      e.preventDefault();

      const form = e.target;
      const formData = new FormData(form);
      const submitButton = form.querySelector('button[type="submit"]');
      const currentUserId = document.getElementById("currentUserId").value;
      
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
          // Record failed conversion
          if (currentUserId) {
            fetch(`${BACKEND_URL}/record-conversion`, {
              method: "POST",
              headers: {
                'Content-Type': 'application/json',
              },
              body: JSON.stringify({
                user_id: currentUserId,
                conversion_type: 'document_signing',
                original_filename: formData.get('document_file').name,
                file_size: formData.get('document_file').size,
                status: 'failed',
                error_message: data.error
              })
            });
          }
          
          alert("Error: " + data.error);
          return;
        }

        // Open the signing editor in a new tab with user_id and conversion_id
        const signingEditorUrl = `${BACKEND_URL}/sign-document/${data.document_id}?user_id=${currentUserId}&conversion_id=${data.conversion_id || ''}`;
        window.open(signingEditorUrl, "_blank");
        
        // Close the modal
        bootstrap.Modal.getInstance(document.getElementById('uploadSigningDocumentModal')).hide();
      })
      .catch((err) => {
        // Record failed conversion
        if (currentUserId) {
          fetch(`${BACKEND_URL}/record-conversion`, {
            method: "POST",
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              user_id: currentUserId,
              conversion_type: 'document_signing',
              original_filename: formData.get('document_file')?.name || 'unknown',
              file_size: formData.get('document_file')?.size || 0,
              status: 'failed',
              error_message: err.message
            })
          });
        }
        
        alert("Failed to upload document: " + err.message);
      })
      .finally(() => {
        // Reset button state
        submitButton.disabled = false;
        submitButton.textContent = originalText;
      });
    });
  }

  // Simple UUID generator for the frontend
  function uuidv4() {
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
      const r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
      return v.toString(16);
    });
  }
});  