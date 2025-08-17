// ====================
// API Configuration
// ====================
const API_BASE_URL = '/api'; // For conversion endpoints
const AUTH_BASE_URL = './';  // For authentication endpoints

// ====================
// Global State
// ====================
let isLoggedIn = false;
let currentUserId = null;

// ====================
// UI Functions
// ====================
function showSuccessMessage(message) {
    const existingAlert = document.getElementById('downloadSuccessAlert');
    if (existingAlert) {
        existingAlert.remove();
    }
    
    const alertDiv = document.createElement('div');
    alertDiv.id = 'downloadSuccessAlert';
    alertDiv.className = 'alert alert-success position-fixed top-0 start-50 translate-middle-x mt-3';
    alertDiv.style.zIndex = '1100';
    alertDiv.textContent = message;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

function updateLoginState(loggedIn) {
    const loginButtons = document.querySelectorAll('.login-button');
    const logoutButtons = document.querySelectorAll('.logout-button');
    const userAvatars = document.querySelectorAll('.user-avatar');
    
    if (loggedIn) {
        loginButtons.forEach(btn => btn.style.display = 'none');
        logoutButtons.forEach(btn => btn.style.display = 'block');
        userAvatars.forEach(avatar => avatar.style.display = 'block');
    } else {
        loginButtons.forEach(btn => btn.style.display = 'block');
        logoutButtons.forEach(btn => btn.style.display = 'none');
        userAvatars.forEach(avatar => avatar.style.display = 'none');
    }
}

// ====================
// Download Functions
// ====================
async function performDownload(downloadUrl, filename) {
    try {
        const response = await fetch(downloadUrl);
        if (!response.ok) throw new Error('Failed to fetch file');
        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
        showSuccessMessage("Download started successfully!");
        return true;
    } catch (error) {
        console.error('Download error:', error);
        showSuccessMessage("Failed to download file: " + error.message);
        return false;
    }
}

async function initiateDownload(downloadUrl, filename) {
    try {
        // First check login status with strict validation
        const authCheck = await fetch(`${AUTH_BASE_URL}/check-login-status.php`, {
            credentials: 'include',
            headers: {
                'Accept': 'application/json'
            }
        });
        
        if (!authCheck.ok) {
            throw new Error('Authentication check failed');
        }
        
        const authData = await authCheck.json();
        
        if (!authData.isLoggedIn) {
            // Store the download info in sessionStorage
            sessionStorage.setItem('pendingDownload', JSON.stringify({
                url: downloadUrl,
                filename: filename
            }));
            
            // Show login modal
            const loginModal = new bootstrap.Modal(document.getElementById('loginRequiredModal'));
            loginModal.show();
            
            // Post message to iframe to show login tab
            const loginFrame = document.getElementById('loginFrame');
            if (loginFrame && loginFrame.contentWindow) {
                loginFrame.contentWindow.postMessage({
                    action: "showLoginTab"
                }, window.location.origin);
            }
            
            return false;
        }
        
        // If logged in, proceed with download
        return await performDownload(downloadUrl, filename);
        
    } catch (error) {
        console.error('Download initiation error:', error);
        showSuccessMessage("Authentication required: " + error.message);
        return false;
    }
}


// ====================
// Authentication Functions
// ====================
async function checkLoginStatus() {
    try {
        const response = await fetch(`${AUTH_BASE_URL}/check-login-status.php`, {
            credentials: 'include',
            headers: {
                'Accept': 'application/json'
            }
        });
        
        if (!response.ok) {
            throw new Error('Auth check failed with status: ' + response.status);
        }
        
        const data = await response.json();
        if (typeof data.isLoggedIn === 'undefined') {
            throw new Error('Invalid auth response');
        }
        
        return {
            isLoggedIn: data.isLoggedIn,
            userId: data.userId || null
        };
    } catch (error) {
        console.error('Auth check error:', error);
        return {
            isLoggedIn: false,
            userId: null
        };
    }
}


async function initializeAuthSystem() {
    try {
        const { isLoggedIn: loggedIn, userId } = await checkLoginStatus();
        isLoggedIn = loggedIn;
        currentUserId = userId;
        updateLoginState(loggedIn);
        
        // Check for pending download after login
        if (loggedIn) {
            const pendingDownload = sessionStorage.getItem('pendingDownload');
            if (pendingDownload) {
                const { url, filename } = JSON.parse(pendingDownload);
                sessionStorage.removeItem('pendingDownload');
                await performDownload(url, filename);
            }
        }
        
        // Ensure login modal closes completely
        document.getElementById('loginRequiredModal').addEventListener('hidden.bs.modal', function () {
            // Remove any remaining backdrop
            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(backdrop => backdrop.remove());
            
            // Reset the modal state
            this.style.display = 'none';
            document.body.classList.remove('modal-open');
            document.body.style.overflow = 'auto';
            document.body.style.paddingRight = '0';
        });
    } catch (error) {
        console.error('Auth system init error:', error);
        isLoggedIn = false;
        updateLoginState(false);
    }
}

// ====================
// Event Listeners
// ====================
function setupEventListeners() {
    // Handle messages from iframes
    window.addEventListener('message', function(event) {
        if (event.origin !== window.location.origin) return;
        
        try {
            const data = typeof event.data === 'object' ? event.data : 
                        (event.data ? JSON.parse(event.data) : null);
            
            if (!data) return;
            
            // Handle login tab activation
            if (data.action === "showLoginTab" || data.action === "keepLoginTabActive") {
                const loginTab = document.getElementById('login-tab');
                if (loginTab) {
                    const tabInstance = new bootstrap.Tab(loginTab);
                    tabInstance.show();
                    document.getElementById('login-tab-pane').classList.add('show', 'active');
                    document.getElementById('signup-tab-pane').classList.remove('show', 'active');
                }
            }
            // Handle successful login
            else if (data.loginSuccess || data === 'login_success') {
                // Close the login modal
                const loginModal = bootstrap.Modal.getInstance(document.getElementById('loginRequiredModal'));
                if (loginModal) {
                    loginModal.hide();
                    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                    document.body.classList.remove('modal-open');
                }
                
                // Update login state
                isLoggedIn = true;
                updateLoginState(true);
                
                // Retry pending download if exists
                const pendingDownload = sessionStorage.getItem('pendingDownload');
                if (pendingDownload) {
                    const { url, filename } = JSON.parse(pendingDownload);
                    sessionStorage.removeItem('pendingDownload');
                    performDownload(url, filename);
                }
            }
            
            // Handle login errors
            else if (data.loginError || data === 'login_error') {
                const loginModal = document.getElementById('loginRequiredModal');
                if (loginModal) {
                    // Show error in modal
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'alert alert-danger mb-3';
                    errorDiv.textContent = data.message || 'Invalid login credentials';
                    
                    const loginPane = document.getElementById('login-tab-pane');
                    if (loginPane) {
                        // Remove any existing alerts
                        const existingAlerts = loginPane.querySelectorAll('.alert');
                        existingAlerts.forEach(alert => alert.remove());
                        
                        // Add new alert at the top
                        loginPane.insertBefore(errorDiv, loginPane.firstChild);
                        
                        // Ensure login tab is active
                        const loginTab = document.getElementById('login-tab');
                        if (loginTab) {
                            const tabInstance = new bootstrap.Tab(loginTab);
                            tabInstance.show();
                        }
                    }
                }
            }
        } catch (e) {
            console.log('Message handling error:', e);
        }
    });

    // Adjust iframe height dynamically
    function adjustIframeHeight(iframe) {
        if (iframe.contentWindow && iframe.contentWindow.document.body) {
            iframe.style.height = iframe.contentWindow.document.body.scrollHeight + 'px';
        }
    }

    // Set up observers for both iframes
    document.querySelectorAll('#loginFrame, #signupFrame').forEach(iframe => {
        iframe.onload = function() {
            adjustIframeHeight(this);
            // Send initial message to show login tab if there's a pending download
            if (sessionStorage.getItem('pendingDownload')) {
                this.contentWindow.postMessage({
                    action: "showLoginTab"
                }, window.location.origin);
            }
        };
    });
}

// ====================
// Initialization
// ====================
document.addEventListener("DOMContentLoaded", function() {
    initializeAuthSystem();
    setupEventListeners();
    
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
                const response = await fetch(`${API_BASE_URL}/convert-word-to-pdf`, {
                    method: "POST",
                    body: formData
                });
                
                if (!response.ok) throw new Error("Conversion failed");
                
                const data = await response.json();
                const pdfUrl = `${API_BASE_URL}/${data.pdf_path}`;
                const convertedFilename = data.pdf_path.split('/').pop();
                
                // Record conversion in database
                await fetch(`${API_BASE_URL}/record-conversion`, {
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
                
                // Set up download with login check
                downloadLink.onclick = async function(e) {
                    e.preventDefault();
                    await initiateDownload(pdfUrl, convertedFilename);
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
                    await fetch(`${API_BASE_URL}/record-conversion`, {
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
                const response = await fetch(`${API_BASE_URL}/convert-excel-to-pdf`, {
                    method: "POST",
                    body: formData
                });
                
                if (!response.ok) throw new Error("Conversion failed");
                
                const data = await response.json();
                const pdfUrl = `${API_BASE_URL}/${data.pdf_path}`;
                const convertedFilename = data.pdf_path.split('/').pop();
                
                // Record conversion in database
                await fetch(`${API_BASE_URL}/record-conversion`, {
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
                
                // Set up download with login check
                downloadLink.onclick = async function(e) {
                    e.preventDefault();
                    await initiateDownload(pdfUrl, convertedFilename);
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
                    await fetch(`${API_BASE_URL}/record-conversion`, {
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
                const response = await fetch(`${API_BASE_URL}/convert-ppt-to-pdf`, {
                    method: "POST",
                    body: formData
                });
                
                if (!response.ok) throw new Error("Conversion failed");
                
                const data = await response.json();
                const pdfUrl = `${API_BASE_URL}/${data.pdf_path}`;
                const convertedFilename = data.pdf_path.split('/').pop();
                
                // Record conversion in database
                await fetch(`${API_BASE_URL}/record-conversion`, {
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
                
                // Set up download with login check
                downloadLink.onclick = async function(e) {
                    e.preventDefault();
                    await initiateDownload(pdfUrl, convertedFilename);
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
                    await fetch(`${API_BASE_URL}/record-conversion`, {
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
                
                const response = await fetch(`${API_BASE_URL}/convert-jpg-to-pdf`, {
                    method: "POST",
                    body: formData
                });
                
                if (!response.ok) throw new Error("Conversion failed");
                
                const data = await response.json();
                const pdfUrl = `${API_BASE_URL}/${data.pdf_path}`;
                const convertedFilename = data.pdf_path.split('/').pop();
                
                // Record conversion in database
                await fetch(`${API_BASE_URL}/record-conversion`, {
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
                
                // Set up download with login check
                downloadLink.onclick = async function(e) {
                    e.preventDefault();
                    await initiateDownload(pdfUrl, convertedFilename);
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
                    await fetch(`${API_BASE_URL}/record-conversion`, {
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

                const response = await fetch(`${API_BASE_URL}/convert-pdf-to-word`, {
                    method: "POST",
                    body: formData
                });
                
                if (!response.ok) throw new Error("Conversion failed");
                
                const data = await response.json();
                const htmlPath = `${API_BASE_URL}/${data.word_path}`;
                const docxPath = `${API_BASE_URL}/${data.word_docx_path}`;
                const editorUrl = `${API_BASE_URL}/editor/${data.word_filename}`;
                const convertedFilename = data.word_docx_path.split('/').pop();
                
                // Record conversion in database
                await fetch(`${API_BASE_URL}/record-conversion`, {
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

                // Set up download with login check
                downloadLink.onclick = async function(e) {
                    e.preventDefault();
                    await initiateDownload(docxPath, convertedFilename);
                };
                
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
                    await fetch(`${API_BASE_URL}/record-conversion`, {
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
                const response = await fetch(`${API_BASE_URL}/convert-pdf-to-excel`, {
                    method: "POST",
                    body: formData
                });
                
                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data.error || "Conversion failed");
                }

                // Record the conversion in database
                if (currentUserId) {
                    await fetch(`${API_BASE_URL}/record-conversion`, {
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

                const excelPath = `${API_BASE_URL}/download/${data.excel_filename}`;
                const editorUrl = `${API_BASE_URL}/excel-editor/${data.excel_filename}`;

                // Set up download with login check
                downloadLink.onclick = async function(e) {
                    e.preventDefault();
                    await initiateDownload(excelPath, data.excel_filename);
                };
                
                editLink.href = editorUrl;

                // Basic preview
                const previewResponse = await fetch(`${API_BASE_URL}/converted/${data.excel_filename}`);
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
                    await fetch(`${API_BASE_URL}/record-conversion`, {
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
                const response = await fetch(`${API_BASE_URL}/convert-pdf-to-ppt`, {
                    method: "POST",
                    body: formData
                });
                
                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data.error || "Conversion failed");
                }

                // Record the conversion in database
                if (currentUserId) {
                    await fetch(`${API_BASE_URL}/record-conversion`, {
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

                const pptPath = `${API_BASE_URL}/download/${data.ppt_filename}`;
                
                // Set up download with login check
                downloadLink.onclick = async function(e) {
                    e.preventDefault();
                    await initiateDownload(pptPath, data.ppt_filename);
                };
                
                result.style.display = "block";
                this.reset();

            } catch (error) {
                console.error("Error:", error);
                
                // Record failed conversion
                if (currentUserId) {
                    await fetch(`${API_BASE_URL}/record-conversion`, {
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
            const currentUserId = document.getElementById("currentUserId").value;

            loading.style.display = "block";
            result.style.display = "none";
            previewImages.innerHTML = "";

            const formData = new FormData(this);
            const fileInput = this.querySelector('input[type="file"]');
            const file = fileInput.files[0];
            const originalFilename = file.name;
            const fileSize = file.size;

            // First convert the PDF to JPG
            fetch(`${API_BASE_URL}/convert-pdf-to-jpg`, {
                method: "POST",
                body: formData
            })
            .then((res) => res.json())
            .then((data) => {
                loading.style.display = "none";

                if (data.error) {
                    throw new Error(data.error);
                }

                const zipPath = `${API_BASE_URL}${data.zipUrl}`;
                
                // Set up download with login check
                downloadLink.onclick = async function(e) {
                    e.preventDefault();
                    await initiateDownload(zipPath, data.zipFilename || "converted_images.zip");
                };

                // Create preview images
                data.imageUrls.forEach((url) => {
                    const img = document.createElement("img");
                    img.src = `${API_BASE_URL}${url}`;
                    img.className = "img-thumbnail";
                    img.style.maxWidth = "150px";
                    img.style.maxHeight = "150px";
                    previewImages.appendChild(img);
                });

                // Record successful conversion (fire-and-forget)
                if (currentUserId) {
                    fetch(`${API_BASE_URL}/record-conversion`, {
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
                    fetch(`${API_BASE_URL}/record-conversion`, {
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
            const currentUserId = document.getElementById("currentUserId").value;

            loading.style.display = "block";
            result.style.display = "none";
            previewImages.innerHTML = "";

            const formData = new FormData(this);
            const fileInput = this.querySelector('input[type="file"]');
            const file = fileInput.files[0];
            const originalFilename = file.name;
            const fileSize = file.size;

            fetch(`${API_BASE_URL}/convert-pdf-to-png`, {
                method: "POST",
                body: formData
            })
            .then((res) => res.json())
            .then((data) => {
                loading.style.display = "none";

                if (data.error) {
                    throw new Error(data.error);
                }

                const zipPath = `${API_BASE_URL}${data.zipUrl}`;
                
                // Set up download with login check
                downloadLink.onclick = async function(e) {
                    e.preventDefault();
                    await initiateDownload(zipPath, data.zipFilename || "converted_images.zip");
                };

                data.imageUrls.forEach((url) => {
                    const img = document.createElement("img");
                    img.src = `${API_BASE_URL}${url}`;
                    img.className = "img-thumbnail";
                    img.style.maxWidth = "150px";
                    img.style.maxHeight = "150px";
                    previewImages.appendChild(img);
                });

                // Record successful conversion (fire-and-forget)
                if (currentUserId) {
                    fetch(`${API_BASE_URL}/record-conversion`, {
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
                
                // Record failed conversion (fire-and-forget)
                if (currentUserId) {
                    fetch(`${API_BASE_URL}/record-conversion`, {
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
            const currentUserId = document.getElementById("currentUserId").value;
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

            fetch(`${API_BASE_URL}/convert-to-pdfa`, {
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
                const downloadUrl = `${API_BASE_URL}/download-pdfa/${encodeURIComponent(data.filename)}`;
                
                // Create download button
                const downloadBtn = document.createElement("a");
                downloadBtn.href = downloadUrl;
                downloadBtn.className = "btn btn-success mt-2";
                downloadBtn.textContent = "Download PDF/A File";
                downloadBtn.setAttribute('download', data.filename || "converted.pdf");
                
                // Set up download with login check
                downloadBtn.onclick = async function(e) {
                    e.preventDefault();
                    await initiateDownload(downloadUrl, data.filename || "converted.pdf");
                };
                
                // Create result display
                resultDiv.innerHTML = `
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle-fill"></i> Successfully converted to PDF/A-${data.pdfa_version}
                    </div>
                `;
                resultDiv.appendChild(downloadBtn);

                // Record successful conversion
                if (currentUserId) {
                    fetch(`${API_BASE_URL}/record-conversion`, {
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
                    fetch(`${API_BASE_URL}/record-conversion`, {
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

    // Merge PDFs
    const mergePDFForm = document.getElementById("mergePDFForm");
    if (mergePDFForm) {
        mergePDFForm.addEventListener("submit", function(e) {
            e.preventDefault();

            // Get user info for recording
            const currentUserId = document.getElementById("currentUserId").value;
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

            fetch(`${API_BASE_URL}/merge-pdfs`, {
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
                            fetch(`${API_BASE_URL}/record-conversion`, {
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
                newIframe.src = `${API_BASE_URL}${data.pdf_path}?t=${Date.now()}`;
                iframeContainer.appendChild(newIframe);

                // Update UI
                pdfPreviewContainer.style.display = "block";
                
                // Set up download with login check
                downloadLink.onclick = async function(e) {
                    e.preventDefault();
                    await initiateDownload(`${API_BASE_URL}${data.pdf_path}?download=true`, data.filename || "merged.pdf");
                };
                
                downloadLink.style.display = "inline-block";
                bootstrap.Modal.getInstance(document.getElementById('mergePDFModal')).show();

                // Record successful conversion
                if (currentUserId) {
                    fetch(`${API_BASE_URL}/record-conversion`, {
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
    }

    // Split PDF
    const splitPDFForm = document.getElementById("splitPDFForm");
if (splitPDFForm) {
    splitPDFForm.addEventListener("submit", async function(e) {
        e.preventDefault();
        
        // Check login status first
        const { isLoggedIn } = await checkLoginStatus();
        
        if (!isLoggedIn) {
            // Store the pending action
            sessionStorage.setItem('pendingAction', JSON.stringify({
                type: 'split_pdf',
                formData: new FormData(this)
            }));
            
            // Show login modal
            const loginModal = new bootstrap.Modal(document.getElementById('loginRequiredModal'));
            loginModal.show();
            return;
        }
        
        // If logged in, proceed with the original code
        const currentUserId = document.getElementById("currentUserId").value;
        const file = this.querySelector('input[type="file"]').files[0];
        const originalFilename = file?.name || "unknown.pdf";
        const fileSize = file?.size || 0;

        const formData = new FormData(this);

        try {
            const response = await fetch(`${API_BASE_URL}/upload-pdf`, {
                method: "POST",
                body: formData,
            });
            
            const data = await response.json();
            if (data.error) throw new Error(data.error);

            // Record successful upload
            if (currentUserId) {
                await fetch(`${API_BASE_URL}/record-conversion`, {
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
                });
            }

            // Open the editor
            const encodedFilename = encodeURIComponent(data.file_path);
            const editorUrl = `${API_BASE_URL}/split-editor?file=${encodedFilename}&user_id=${currentUserId}&conversion_id=${data.conversion_id || ''}`;
            window.open(editorUrl, "_blank");
            
        } catch (error) {
            console.error("Upload error:", error);
            alert("Error: " + error.message);
            
            // Record failed upload
            if (currentUserId) {
                await fetch(`${API_BASE_URL}/record-conversion`, {
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
                });
            }
        }
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
                const response = await fetch(`${API_BASE_URL}/protect-pdf`, {
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
                    await fetch(`${API_BASE_URL}/record-conversion`, {
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
                
                // Set up download with login check
                link.onclick = async function(e) {
                    e.preventDefault();
                    await initiateDownload(data.download_url, data.filename || "protected.pdf");
                };

                downloadLinks.innerHTML = "";
                downloadLinks.appendChild(link);

            } catch (error) {
                console.error("Error:", error);
                
                // Record failed conversion
                if (currentUserId) {
                    await fetch(`${API_BASE_URL}/record-conversion`, {
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
                const response = await fetch(`${API_BASE_URL}/unlock-pdf`, {
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
                    await fetch(`${API_BASE_URL}/record-conversion`, {
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
                
                // Set up download with login check
                link.onclick = async function(e) {
                    e.preventDefault();
                    await initiateDownload(data.download_url, data.filename || "unlocked.pdf");
                };
                
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
                    await fetch(`${API_BASE_URL}/record-conversion`, {
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

    // eBooks to PDF Conversion
    // eBook to PDF Conversion - Fixed Version
const ebookFileInput = document.getElementById("ebookFileInput");
if (ebookFileInput) {
    const convertEbookBtn = document.getElementById("convertEbookBtn");
    const ebookPreviewContainer = document.getElementById("ebookPreviewContainer");
    const downloadEbookPdfBtn = document.getElementById("downloadEbookPdfBtn");
    const convertAnotherEbookBtn = document.getElementById("convertAnotherEbookBtn");
    const ebookFileInfo = document.getElementById("ebookFileInfo");
    const ebookFileName = document.getElementById("ebookFileName");

    let currentEbookFile = null;
    let pdfObjectUrl = null;

    // File selection handler
    ebookFileInput.addEventListener("change", function(e) {
        if (e.target.files.length > 0) {
            currentEbookFile = e.target.files[0];
            ebookFileName.textContent = currentEbookFile.name;
            ebookFileInfo.classList.remove('d-none');
            ebookPreviewContainer.classList.add('d-none');
        }
    });

    // Convert button handler - Fixed version
    convertEbookBtn.addEventListener("click", async function() {
        if (!currentEbookFile) {
            alert('Please select an eBook file first');
            return;
        }

        convertEbookBtn.disabled = true;
        convertEbookBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Converting...';

        try {
            const formData = new FormData();
            formData.append('file', currentEbookFile); // Changed from 'ebook_file' to 'file'
            
            // Get user ID safely
            const userIdInput = document.getElementById("currentUserId");
            if (userIdInput && userIdInput.value) {
                formData.append('user_id', userIdInput.value);
            }

            const response = await fetch(`${API_BASE_URL}/convert-ebook-to-pdf`, {
    method: 'POST',
    body: formData
});

const responseData = await response.json(); // Always parse JSON first

if (!response.ok) {
    throw new Error(responseData.message || 'Conversion failed');
}

if (!responseData.pdfUrl) {  // Changed from pdf_path to pdfUrl
    throw new Error('Server did not return PDF URL');
}

// Get the PDF - Updated URL construction
const pdfResponse = await fetch(`${API_BASE_URL}${responseData.pdfUrl}`);
if (!pdfResponse.ok) {
    throw new Error('Failed to fetch converted PDF');
}

            const pdfBlob = await pdfResponse.blob();
            pdfObjectUrl = URL.createObjectURL(pdfBlob);

            // Show preview
            ebookPreviewContainer.classList.remove('d-none');
            ebookFileInfo.classList.add('d-none');

            // Set up download with login check
            downloadEbookPdfBtn.onclick = async function(e) {
                e.preventDefault();
                const filename = currentEbookFile.name.replace(/\.[^.]+$/, '') + '.pdf';
                await initiateDownload(pdfObjectUrl, filename);
            };

        } catch (error) {
            console.error('Conversion error:', error);
            alert('Conversion failed: ' + error.message);
        } finally {
            convertEbookBtn.disabled = false;
            convertEbookBtn.innerHTML = '<i class="bi bi-file-earmark-pdf"></i> Convert to PDF';
        }
    });

    // Reset function
    convertAnotherEbookBtn.addEventListener('click', function() {
        if (pdfObjectUrl) {
            URL.revokeObjectURL(pdfObjectUrl);
            pdfObjectUrl = null;
        }
        ebookFileInput.value = '';
        currentEbookFile = null;
        ebookPreviewContainer.classList.add('d-none');
        ebookFileInfo.classList.add('d-none');
        document.getElementById("ebookPdfViewer").innerHTML = '';
    });
}

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
                const conversionResponse = await fetch(`${API_BASE_URL}/convert-iwork-to-pdf`, {
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
                    await fetch(`${API_BASE_URL}/record-conversion`, {
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
                const pdfResponse = await fetch(`${API_BASE_URL}${result.pdfUrl}`);
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

                // Set up download with login check
                downloadBtn.onclick = async function(e) {
    e.preventDefault();
    const filename = currentFile.name.replace(/\.[^.]+$/, '') + '.pdf';
    
    // Set high z-index right before download check
    const loginModal = document.getElementById('loginRequiredModal');
    if (loginModal) {
        loginModal.style.zIndex = '9999'; // Extremely high value
    }
    
    // Use initiateDownload which checks login status
    await initiateDownload(pdfObjectUrl, filename);
    
    // Don't reset z-index automatically - let Bootstrap handle it
    // when the modal is actually closed by the user
    
    // Record download event if logged in
    if (currentUserId) {
        try {
            await fetch(`${API_BASE_URL}/record-download`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    user_id: currentUserId,
                    conversion_id: conversionId,
                    file_name: filename,
                    action: 'download'
                })
            });
        } catch (error) {
            console.error('Download recording failed:', error);
        }
    }
};

            } catch (error) {
                console.error('Error:', error);
                
                // Record failed conversion
                if (currentUserId) {
                    await fetch(`${API_BASE_URL}/record-conversion`, {
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

    // Edit PDF
    const uploadForm = document.getElementById("uploadForm");
if (uploadForm) {
    // Store the file data temporarily while user logs in
    let pendingEditFile = null;
    
    uploadForm.addEventListener("submit", async (e) => {
        e.preventDefault();
        
        const fileInput = e.target.querySelector('input[type="file"]');
        const currentUserId = document.getElementById("currentUserId")?.value;
        
        // Check if we have a pending file from before login
        const fileToProcess = pendingEditFile || fileInput.files[0];
        
        if (!fileToProcess) {
            alert("Please select a PDF file first");
            return;
        }
        
        // Check login status
        const { isLoggedIn } = await checkLoginStatus();
        
        if (!isLoggedIn) {
            // Store the file for after login
            pendingEditFile = fileToProcess;
            
            // Store pending action
            sessionStorage.setItem('pendingAction', JSON.stringify({
                type: 'edit_pdf'
            }));
            
            // Show login modal
            const loginModal = new bootstrap.Modal(document.getElementById('loginRequiredModal'));
            loginModal.show();
            return;
        }
        
        // User is logged in - proceed with upload
        const submitBtn = e.target.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        
        try {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
            
            // Create FormData and append file
            const formData = new FormData();
            formData.append('pdfFile', fileToProcess);
            if (currentUserId) {
                formData.append('user_id', currentUserId);
            }
            
            // Upload and process
            const response = await fetch(`${API_BASE_URL}/upload-edit-pdf`, {
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
                const editorUrl = `${API_BASE_URL}/pdf-editor-view?file=${encodeURIComponent(result.pdfFileName)}&user_id=${currentUserId}&conversion_id=${result.conversion_id || ''}`;
                window.open(editorUrl, "_blank");
                
                // Reset form
                e.target.reset();
                pendingEditFile = null;
            } else {
                throw new Error(result.error || "Failed to process PDF");
            }
        } catch (error) {
            console.error('Edit PDF error:', error);
            let errorMsg = error.message;
            
            if (errorMsg.includes('unsupported colorspace') || errorMsg.includes('Image conversion failed')) {
                errorMsg = "The PDF contains complex images that couldn't be processed. Try a different file.";
            }
            
            alert("Error: " + errorMsg);
            
            // Keep the pending file if there was an error
            if (!pendingEditFile && fileInput.files.length > 0) {
                pendingEditFile = fileInput.files[0];
            }
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        }
    });
    
    // Preserve file if user closes/reopens modal
    document.getElementById('editPdfModal')?.addEventListener('hide.bs.modal', () => {
        const fileInput = uploadForm.querySelector('input[type="file"]');
        if (fileInput.files.length > 0 && !pendingEditFile) {
            pendingEditFile = fileInput.files[0];
        }
    });
}

    // Sign Document
    const signingDocumentForm = document.getElementById("signingDocumentForm");
if (signingDocumentForm) {
    signingDocumentForm.addEventListener("submit", async function(e) {
        e.preventDefault();
        
        // Check login status first
        const { isLoggedIn } = await checkLoginStatus();
        
        if (!isLoggedIn) {
            // Store the pending action
            sessionStorage.setItem('pendingAction', JSON.stringify({
                type: 'sign_document',
                formData: new FormData(this)
            }));
            
            // Show login modal
            const loginModal = new bootstrap.Modal(document.getElementById('loginRequiredModal'));
            loginModal.show();
            return;
        }
        
        // If logged in, proceed with original code
        const form = e.target;
        const formData = new FormData(form);
        const submitButton = form.querySelector('button[type="submit"]');
        const currentUserId = document.getElementById("currentUserId").value;
        
        // Store original button text
        const originalText = submitButton.textContent;
        
        // Change button state
        submitButton.disabled = true;
        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';

        try {
            const response = await fetch(`${API_BASE_URL}/upload-signing-document`, {
                method: "POST",
                body: formData,
            });
            
            const data = await response.json();
            if (data.error) {
                // Record failed conversion
                if (currentUserId) {
                    await fetch(`${API_BASE_URL}/record-conversion`, {
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

            // Open the signing editor
            const signingEditorUrl = `${API_BASE_URL}/sign-document/${data.document_id}?user_id=${currentUserId}&conversion_id=${data.conversion_id || ''}`;
            window.open(signingEditorUrl, "_blank");
            
            // Close the modal
            bootstrap.Modal.getInstance(document.getElementById('uploadSigningDocumentModal')).hide();
        } catch (err) {
            // Record failed conversion
            if (currentUserId) {
                await fetch(`${API_BASE_URL}/record-conversion`, {
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
        } finally {
            // Reset button state
            submitButton.disabled = false;
            submitButton.textContent = originalText;
        }
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

// ====================
// Special Case Handlers
// ====================

// Split PDF
const splitPDFForm = document.getElementById("splitPDFForm");
if (splitPDFForm) {
    splitPDFForm.addEventListener("submit", async function(e) {
        e.preventDefault();
        
        // Check login status first
        const { isLoggedIn } = await checkLoginStatus();
        
        if (!isLoggedIn) {
            // Store the pending action
            sessionStorage.setItem('pendingAction', JSON.stringify({
                type: 'split_pdf',
                formData: new FormData(this)
            }));
            
            // Show login modal
            const loginModal = new bootstrap.Modal(document.getElementById('loginRequiredModal'));
            loginModal.show();
            return;
        }
        
        // If logged in, proceed with the original code
        const currentUserId = document.getElementById("currentUserId").value;
        const file = this.querySelector('input[type="file"]').files[0];
        const originalFilename = file?.name || "unknown.pdf";
        const fileSize = file?.size || 0;

        const formData = new FormData(this);

        try {
            const response = await fetch(`${API_BASE_URL}/upload-pdf`, {
                method: "POST",
                body: formData,
            });
            
            const data = await response.json();
            if (data.error) throw new Error(data.error);

            // Record successful upload
            if (currentUserId) {
                await fetch(`${API_BASE_URL}/record-conversion`, {
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
                });
            }

            // Open the editor
            const encodedFilename = encodeURIComponent(data.file_path);
            const editorUrl = `${API_BASE_URL}/split-editor?file=${encodedFilename}&user_id=${currentUserId}&conversion_id=${data.conversion_id || ''}`;
            window.open(editorUrl, "_blank");
            
        } catch (error) {
            console.error("Upload error:", error);
            alert("Error: " + error.message);
            
            // Record failed upload
            if (currentUserId) {
                await fetch(`${API_BASE_URL}/record-conversion`, {
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
                });
            }
        }
    });
}

// Edit PDF
const uploadForm = document.getElementById("uploadForm");
if (uploadForm) {
    uploadForm.addEventListener("submit", async (e) => {
        e.preventDefault();
        
        // Check login status first
        const { isLoggedIn } = await checkLoginStatus();
        
        if (!isLoggedIn) {
            // Store the pending action
            sessionStorage.setItem('pendingAction', JSON.stringify({
                type: 'edit_pdf',
                formData: new FormData(this)
            }));
            
            // Show login modal
            const loginModal = new bootstrap.Modal(document.getElementById('loginRequiredModal'));
            loginModal.show();
            return;
        }
        
        // If logged in, proceed with original code
        const formData = new FormData(e.target);
        const submitBtn = e.target.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        const currentUserId = document.getElementById("currentUserId")?.value || 'anonymous';

        try {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';

            // Add user_id to form data
            if (!formData.has('user_id')) {
                formData.append('user_id', currentUserId);
            }

            const response = await fetch(`${API_BASE_URL}/upload-edit-pdf`, {
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
                    await fetch(`${API_BASE_URL}/record-conversion`, {
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
                const editorUrl = `${API_BASE_URL}/pdf-editor-view?file=${encodeURIComponent(result.pdfFileName)}&user_id=${currentUserId}&conversion_id=${result.conversion_id || ''}`;
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

// Sign Document
const signingDocumentForm = document.getElementById("signingDocumentForm");
if (signingDocumentForm) {
    signingDocumentForm.addEventListener("submit", async function(e) {
        e.preventDefault();
        
        // Check login status first
        const { isLoggedIn } = await checkLoginStatus();
        
        if (!isLoggedIn) {
            // Store the pending action
            sessionStorage.setItem('pendingAction', JSON.stringify({
                type: 'sign_document',
                formData: new FormData(this)
            }));
            
            // Show login modal
            const loginModal = new bootstrap.Modal(document.getElementById('loginRequiredModal'));
            loginModal.show();
            return;
        }
        
        // If logged in, proceed with original code
        const form = e.target;
        const formData = new FormData(form);
        const submitButton = form.querySelector('button[type="submit"]');
        const currentUserId = document.getElementById("currentUserId").value;
        
        // Store original button text
        const originalText = submitButton.textContent;
        
        // Change button state
        submitButton.disabled = true;
        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';

        try {
            const response = await fetch(`${API_BASE_URL}/upload-signing-document`, {
                method: "POST",
                body: formData,
            });
            
            const data = await response.json();
            if (data.error) {
                // Record failed conversion
                if (currentUserId) {
                    await fetch(`${API_BASE_URL}/record-conversion`, {
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

            // Open the signing editor
            const signingEditorUrl = `${API_BASE_URL}/sign-document/${data.document_id}?user_id=${currentUserId}&conversion_id=${data.conversion_id || ''}`;
            window.open(signingEditorUrl, "_blank");
            
            // Close the modal
            bootstrap.Modal.getInstance(document.getElementById('uploadSigningDocumentModal')).hide();
        } catch (err) {
            // Record failed conversion
            if (currentUserId) {
                await fetch(`${API_BASE_URL}/record-conversion`, {
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
        } finally {
            // Reset button state
            submitButton.disabled = false;
            submitButton.textContent = originalText;
        }
    });
}

// ====================
// Login Success Handler
// ====================
window.addEventListener('message', function(event) {
    if (event.origin !== window.location.origin) return;
    
    try {
        const data = typeof event.data === 'object' ? event.data : 
                    (event.data ? JSON.parse(event.data) : null);
        
        if (!data) return;
        
        if (data.loginSuccess || data === 'login_success') {
            // Check for pending actions
            const pendingAction = sessionStorage.getItem('pendingAction');
            if (pendingAction) {
                const action = JSON.parse(pendingAction);
                sessionStorage.removeItem('pendingAction');
                
                // Execute the pending action
                switch(action.type) {
                    case 'split_pdf':
                        document.getElementById('splitPDFForm').dispatchEvent(new Event('submit'));
                        break;
                    case 'edit_pdf':
                        document.getElementById('uploadForm').dispatchEvent(new Event('submit'));
                        break;
                    case 'sign_document':
                        document.getElementById('signingDocumentForm').dispatchEvent(new Event('submit'));
                        break;
                }
            }
        }
    } catch (e) {
        console.log('Message handling error:', e);
    }
});