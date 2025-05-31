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
