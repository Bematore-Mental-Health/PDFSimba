// API Configuration
const API_BASE_URL = 'http://localhost:5000'; // For conversion endpoints
const AUTH_BASE_URL = 'http://localhost/PD';  // For authentication endpoints

let isLoggedIn = false;
let currentUserId = null;

// ====================
// Core Functions
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

function checkPendingDownload() {
    const pendingDownload = sessionStorage.getItem('pendingDownload');
    if (pendingDownload) {
        const { url, filename } = JSON.parse(pendingDownload);
        sessionStorage.removeItem('pendingDownload');
        
        setTimeout(() => {
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            
            showSuccessMessage("Your file has been downloaded successfully!");
        }, 500);
    }
}

// Replace your initiateDownload function with this:
async function initiateDownload(downloadUrl, filename) {
    try {
        // First check if we're logged in
        const authCheck = await fetch(`${AUTH_BASE_URL}/check-login-status.php`, {
            credentials: 'include',
            headers: {
                'Accept': 'application/json'
            }
        });
        
        const authData = await authCheck.json();
        
        if (!authData.isLoggedIn) {
            // Store download info for after login
            sessionStorage.setItem('pendingDownload', JSON.stringify({
                url: downloadUrl,
                filename: filename
            }));
            
            // Show login modal
            const loginModal = new bootstrap.Modal(document.getElementById('loginRequiredModal'));
            loginModal.show();
            return false;
        }
        
        // If logged in, create a hidden iframe to force download
        const iframe = document.createElement('iframe');
        iframe.src = downloadUrl;
        iframe.style.display = 'none';
        document.body.appendChild(iframe);
        
        // Also create a regular link as fallback
        setTimeout(() => {
            const a = document.createElement('a');
            a.href = downloadUrl;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            
            showSuccessMessage("Your file has been downloaded successfully!");
        }, 100);
        
        return true;
    } catch (error) {
        console.error('Download error:', error);
        showSuccessMessage("Failed to download file: " + error.message);
        return false;
    }
}

function downloadFile(url, filename) {
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    showSuccessMessage("Your file has been downloaded successfully!");
}

// ====================
// Auth System
// ====================

async function checkLoginStatus() {
    try {
        const response = await fetch(`${AUTH_BASE_URL}/check-login-status.php`, {
            credentials: 'include',
            headers: {
                'Accept': 'application/json'
            }
        });
        
        if (!response.ok) throw new Error('Auth check failed');
        
        const data = await response.json();
        return {
            isLoggedIn: data.isLoggedIn || false,
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
        updateLoginState(isLoggedIn);
        
        if (isLoggedIn) {
            checkPendingDownload();
        }
        
        // Handle messages from iframes
// In login.php
window.addEventListener('message', function(event) {
    if (event.origin !== window.location.origin) return;
    
    try {
        const data = typeof event.data === 'object' ? event.data : JSON.parse(event.data);
        
        if (data.showMessage) {
            const errorDiv = document.getElementById('loginError');
            if (errorDiv) {
                errorDiv.textContent = data.showMessage;
                errorDiv.style.display = 'block';
                errorDiv.className = data.isError ? 'alert alert-danger' : 'alert alert-success';
                
                // Scroll to error
                errorDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        }
    } catch (e) {
        console.log('Non-JSON message:', event.data);
    }
});
    } catch (error) {
        console.error('Auth system init error:', error);
        isLoggedIn = false;
        updateLoginState(false);
    }
}

// ====================
// Conversion Forms
// ====================

function setupConversionForms() {
    // Word to PDF Conversion
    const wordToPdfForm = document.getElementById("wordToPdfForm");
    if (wordToPdfForm) {
        wordToPdfForm.addEventListener("submit", async function(e) {
            e.preventDefault();
            
            const previewContainer = document.getElementById("previewContainer");
            const loadingIndicator = document.getElementById("loadingIndicator");
            const pdfPreview = document.getElementById("pdfPreview");
            const downloadLink = document.getElementById("downloadLink");

            previewContainer.style.display = "none";
            loadingIndicator.style.display = "block";
            
            try {
                const fileInput = this.querySelector('input[type="file"]');
                const file = fileInput.files[0];
                const formData = new FormData(this);
                
                const response = await fetch(`${API_BASE_URL}/convert-word-to-pdf`, {
                    method: "POST",
                    body: formData
                });
                
                if (!response.ok) throw new Error("Conversion failed");
                
                const data = await response.json();
                const pdfUrl = `${API_BASE_URL}/${data.pdf_path}`;
                const convertedFilename = data.pdf_path.split('/').pop();
                
                downloadLink.onclick = function(e) {
                    e.preventDefault();
                    initiateDownload(pdfUrl, convertedFilename);
                };
                
                pdfPreview.src = pdfUrl;
                loadingIndicator.style.display = "none";
                previewContainer.style.display = "block";
                
            } catch (error) {
                loadingIndicator.style.display = "none";
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

            previewContainer.style.display = "none";
            loadingIndicator.style.display = "block";
            
            try {
                const fileInput = this.querySelector('input[type="file"]');
                const file = fileInput.files[0];
                const formData = new FormData(this);
                
                const response = await fetch(`${API_BASE_URL}/convert-excel-to-pdf`, {
                    method: "POST",
                    body: formData
                });
                
                if (!response.ok) throw new Error("Conversion failed");
                
                const data = await response.json();
                const pdfUrl = `${API_BASE_URL}/${data.pdf_path}`;
                const convertedFilename = data.pdf_path.split('/').pop();
                
                downloadLink.onclick = function(e) {
                    e.preventDefault();
                    initiateDownload(pdfUrl, convertedFilename);
                };
                
                pdfPreview.src = pdfUrl;
                loadingIndicator.style.display = "none";
                previewContainer.style.display = "block";
                
            } catch (error) {
                loadingIndicator.style.display = "none";
                alert("Error: " + error.message);
            }
        });
    }
}

// ====================
// Initialization
// ====================

document.addEventListener("DOMContentLoaded", function() {
    initializeAuthSystem();
    setupConversionForms();
});