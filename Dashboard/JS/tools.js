// Global backend URL - change this when deploying to production
// const BACKEND_URL = 'https://server.pdfsimba.com';
const BACKEND_URL = 'http://localhost:5000';
document.addEventListener('DOMContentLoaded', function() {
  const ebookTool = document.querySelector('.ebook-tool-box');
  const ebookFileInput = document.getElementById('ebookFileInput');
  const ebookFileInfo = document.getElementById('ebookFileInfo');
  const ebookFileName = document.getElementById('ebookFileName');
  const convertEbookBtn = document.getElementById('convertEbookBtn');
  const ebookPreviewContainer = document.getElementById('ebookPreviewContainer');
  const downloadEbookPdfBtn = document.getElementById('downloadEbookPdfBtn');
  const convertAnotherEbookBtn = document.getElementById('convertAnotherEbookBtn');
  const currentUserId = document.getElementById('currentUserId')?.value || 'anonymous';

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
      formData.append('user_id', currentUserId);

      const conversionResponse = await fetch(`${BACKEND_URL}/convert-ebook-to-pdf`, {
        method: 'POST',
        body: formData
      });

      const result = await conversionResponse.json();

      if (!conversionResponse.ok || !result.success) {
        // Record failed conversion
        if (currentUserId) {
          await fetch(`${BACKEND_URL}/record-conversion`, {
            method: "POST",
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              user_id: currentUserId,
              conversion_type: 'ebook_to_pdf',
              original_filename: currentEbookFile.name,
              file_size: currentEbookFile.size,
              status: 'failed',
              error_message: result.message || 'Conversion failed with unknown error',
              conversion_id: result.conversion_id || null
            })
          });
        }
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

      downloadEbookPdfBtn.onclick = createDownloadHandler(pdfObjectUrl, result.filename);

    } catch (error) {
      console.error('Conversion error:', error);
      showAlert(error.message, 'danger');
      resetEbookConversionUI();
    } finally {
      convertEbookBtn.disabled = false;
      convertEbookBtn.innerHTML = '<i class="bi bi-file-earmark-pdf"></i> Convert to PDF';
    }
  });

  function createDownloadHandler(pdfObjectUrl, suggestedFilename) {
    return function() {
      const a = document.createElement('a');
      a.href = pdfObjectUrl;
      a.download = suggestedFilename || currentEbookFile.name.replace(/\.[^.]+$/, '') + '.pdf';
      document.body.appendChild(a);
      a.click();
      setTimeout(() => {
        document.body.removeChild(a);
      }, 100);
    };
  }

  convertAnotherEbookBtn.addEventListener('click', resetEbookConversionUI);
});