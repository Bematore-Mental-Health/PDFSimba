from flask import Flask, request, jsonify, send_from_directory, render_template, url_for, redirect
from flask_cors import CORS
import os
import threading
import time
from bs4 import BeautifulSoup
from docx import Document
from html2docx import html2docx
import os
import uuid
import pandas as pd
from flask import Flask, request, jsonify, send_from_directory, render_template_string
from PyPDF2 import PdfReader,  PdfWriter
from flask_cors import CORS
from flask import Flask, request, jsonify, send_from_directory
from flask_cors import CORS
from flask import render_template
from urllib.parse import unquote
from werkzeug.utils import secure_filename
import tempfile
import subprocess
from reportlab.pdfgen import canvas
from reportlab.lib.pagesizes import letter
from PIL import Image
import pdf2image
from docx import Document
from docx2pdf import convert
import tempfile
import pypandoc
from flask import send_file




from flask import Flask, request, jsonify, send_from_directory, send_file, render_template
from pdf2image import convert_from_path
from pptx import Presentation
from pptx.util import Inches
import base64
from io import BytesIO
from flask import Flask, request, jsonify, send_file, make_response
from PyPDF2 import PdfMerger
import base64
from reportlab.pdfgen import canvas
from reportlab.lib.pagesizes import letter
import pythoncom





from word_to_pdf import convert_word_file
from excel_to_pdf import convert_excel_file
from powerpoint_to_pdf import convert_ppt_file
from jpg_to_pdf import convert_jpg_file
from pdf_to_word import convert_pdf_file
from pdf_to_excel import convert_pdf_to_excel
from pdf_to_ppt import convert_pdf_to_ppt 
from pdf_to_jpg import convert_pdf_to_jpg
from pdf_to_png import convert_pdf_to_png
from pdf_to_pdfa import convert_to_pdfa
from merge_pdfs import merge_pdfs
from split_pdfs import  split_pdf_logic




app = Flask(__name__)
application = app
CORS(app)

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
UPLOAD_FOLDER = os.path.abspath(os.path.join(BASE_DIR, '..', 'uploads'))
OUTPUT_FOLDER = os.path.abspath(os.path.join(BASE_DIR, '..', 'converted'))

os.makedirs(UPLOAD_FOLDER, exist_ok=True)
os.makedirs(OUTPUT_FOLDER, exist_ok=True)

app.config['UPLOAD_FOLDER'] = UPLOAD_FOLDER
app.config['OUTPUT_FOLDER'] = OUTPUT_FOLDER
app.config['ALLOWED_IWORK_EXTENSIONS'] = {'pages', 'key', 'numbers'}



# Word to PDF
@app.route('/convert-word-to-pdf', methods=['POST'])
def convert_word_route():
    file = request.files.get('word_file')
    return convert_word_file(file)

# Excel to PDF
@app.route('/convert-excel-to-pdf', methods=['POST'])
def convert_excel_route():
    file = request.files.get('excel_file')
    return convert_excel_file(file)

# PowerPoint to PDF
@app.route('/convert-ppt-to-pdf', methods=['POST'])
def convert_ppt_route():
    file = request.files.get('ppt_file')
    return convert_ppt_file(file)

# JPG/PNG to PDF
@app.route('/convert-jpg-to-pdf', methods=['POST'])
def convert_jpg_route():
    return convert_jpg_file()


# Serve converted PDF files
@app.route('/converted/<path:filename>')
def serve_pdf(filename):
    return send_from_directory(OUTPUT_FOLDER, filename)

# PDF To Word
@app.route('/convert-pdf-to-word', methods=['POST'])
def convert_pdf_to_word_route():
    file = request.files.get('pdf_file')
    return convert_pdf_file(file)

@app.route('/editor/<filename>')
def edit_word_document(filename):
    return f"""
    <!DOCTYPE html>
    <html>
    <head>
      <title>PDFSimba| Edit Word Document</title>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
      <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
      <link rel="icon" href="../Media/book3.png" type="image/x-icon">
      <style>
        body {{
          background-color: #f8f9fa;
          padding: 30px;
        }}
        .editor-container {{
          background: #fff;
          padding: 20px;
          border-radius: 10px;
          box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }}
      </style>
    </head>
    <body>
      <div class="container">
        <div class="editor-container">
          <h3 class="mb-4">Edit Your Word Document</h3>
          <button class="btn btn-success" type="submit">Save and Download</button> 
          <br>
          <form method="POST" action="/save-edited-word/{filename}">
            <textarea id="editor" name="content"></textarea>
            <br>
            
          </form>
        </div>
      </div>

      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
 

      <script>
        fetch("/converted/{filename}")
          .then(response => response.text())
          .then(content => {{
            $('#editor').summernote({{
              height: 500,
              tabsize: 2
            }});
            $('#editor').summernote('code', content);
          }});
      </script>
    </body>
    </html>
    """


@app.route('/save-edited-word/<filename>', methods=['POST'])
def save_edited_word(filename):
    content = request.form.get('content')
    output_docx = os.path.join(OUTPUT_FOLDER, f"edited_{filename.replace('.html', '')}.docx")

    try:
        # Convert HTML content to .docx with a title
        docx_content = html2docx(content, title="Edited Document")

        # Write the actual bytes from the BytesIO buffer
        with open(output_docx, "wb") as f:
            f.write(docx_content.getvalue())

        return send_from_directory(OUTPUT_FOLDER, os.path.basename(output_docx), as_attachment=True)
    except Exception as e:
        return f"Error saving Word file: {str(e)}", 500


# PDF to Excel
@app.route('/convert-pdf-to-excel', methods=['POST'])
def convert_pdf_to_excel():
    file = request.files.get('pdf_file')
    if not file:
        return jsonify({'error': 'No file uploaded'}), 400

    filename = f"{uuid.uuid4()}.pdf"
    input_path = os.path.join(UPLOAD_FOLDER, filename)
    file.save(input_path)

    output_filename = f"{os.path.splitext(filename)[0]}.xlsx"
    output_path = os.path.join(OUTPUT_FOLDER, output_filename)


    try:
        reader = PdfReader(input_path)
        text = ''.join(page.extract_text() or "" for page in reader.pages)
        lines = text.splitlines()
        data = [line.split() for line in lines if line.strip()]
        df = pd.DataFrame(data)
        df.to_excel(output_path, index=False, header=False)

        return jsonify({
            'excel_filename': output_filename
        })
    except Exception as e:
        return jsonify({'error': f'PDF to Excel conversion failed: {str(e)}'}), 500

@app.route('/download/<filename>')
def download_excel(filename):
    return send_from_directory(OUTPUT_FOLDER, filename, as_attachment=True)

@app.route('/converted/<filename>')
def serve_excel_for_preview(filename):
    return send_from_directory(OUTPUT_FOLDER, filename)


@app.route('/excel-editor/<filename>')
def edit_excel(filename):
    return f"""
    <!DOCTYPE html>
    <html>
    <head>
        <title>PDFSimba| Edit Excel</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable@13.0.0/dist/handsontable.full.min.css">
        <script src="https://cdn.jsdelivr.net/npm/handsontable@13.0.0/dist/handsontable.full.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
        <style>
            body {{ margin: 20px; font-family: sans-serif; background-color: #f9f9f9; }}
            #excelEditor {{ width: 100%; height: 500px; margin-bottom: 20px; }}
        </style>
    </head>
    <body>
        <h2>Edit Excel File</h2>
        <button onclick="downloadExcel()" class="btn btn-success mb-2">Save and Download</button>

        <div id="excelEditor"></div>

        <script>
            let container = document.getElementById('excelEditor');
            let hot;

            fetch("/converted/{filename}")
                .then(res => res.arrayBuffer())
                .then(buffer => {{
                    const workbook = XLSX.read(buffer, {{ type: "array" }});
                    const ws = workbook.Sheets[workbook.SheetNames[0]];
                    const data = XLSX.utils.sheet_to_json(ws, {{ header: 1 }});
                    hot = new Handsontable(container, {{
                        data: data,
                        rowHeaders: true,
                        colHeaders: true,
                        licenseKey: 'non-commercial-and-evaluation'
                    }});
                }});

            function downloadExcel() {{
                const wb = XLSX.utils.book_new();
                const ws = XLSX.utils.aoa_to_sheet(hot.getData());
                XLSX.utils.book_append_sheet(wb, ws, "Sheet1");
                XLSX.writeFile(wb, "edited_{filename}");
            }}
        </script>
    </body>
    </html>
    """


# PDF to Powerpoint
@app.route('/convert-pdf-to-ppt', methods=['POST'])
def convert_pdf_to_ppt_route():
    file = request.files.get('pdf_file')
    ppt_filename, error = convert_pdf_to_ppt(file)
    if error:
        return jsonify({'error': error}), 400
    return jsonify({'ppt_filename': ppt_filename})

@app.route('/download/<filename>')
def download_file(filename):
    return send_from_directory(OUTPUT_FOLDER, filename, as_attachment=True)

def convert_pdf_to_ppt(file):
    if not file:
        return None, "No file uploaded."

    ext = os.path.splitext(file.filename)[1].lower()
    if ext != '.pdf':
        return None, "Only PDF files are supported."

    filename = f"{uuid.uuid4()}.pdf"
    input_path = os.path.join(UPLOAD_FOLDER, filename)
    file.save(input_path)

    output_filename = f"{os.path.splitext(filename)[0]}.pptx"
    output_path = os.path.join(OUTPUT_FOLDER, output_filename)

    try:
        images = convert_from_path(input_path)
        prs = Presentation()
        blank_slide_layout = prs.slide_layouts[6]  

        for image in images:
            slide = prs.slides.add_slide(blank_slide_layout)
            image_path = os.path.join(UPLOAD_FOLDER, f"temp_{uuid.uuid4()}.jpg")
            image.save(image_path, "JPEG")
            slide.shapes.add_picture(image_path, Inches(0), Inches(0), width=prs.slide_width)
            os.remove(image_path)

        prs.save(output_path)
        return output_filename, None
    except Exception as e:
        return None, str(e)


# PDF to JPG 
from flask import Flask, request, jsonify, send_from_directory
from pdf_to_jpg import convert_pdf_to_jpg

@app.route('/convert-pdf-to-jpg', methods=['POST'])
def convert_pdf_to_jpg_route():
    file = request.files.get('pdf_file')
    if not file:
        return jsonify({'error': 'No file uploaded'}), 400

    try:
        result = convert_pdf_to_jpg(file)
        if result.get("error"):
            return jsonify({'error': result["error"]}), 500

        return jsonify(result)
    except Exception as e:
        return jsonify({'error': f'Unexpected error: {str(e)}'}), 500

@app.route('/converted/<filename>')
def serve_converted_files(filename):
    try:
        return send_from_directory(OUTPUT_FOLDER, filename)
    except FileNotFoundError:
        return jsonify({'error': 'File not found'}), 404
    except Exception as e:
        return jsonify({'error': str(e)}), 500
        

# PDF To PNG
@app.route('/convert-pdf-to-png', methods=['POST'])
def convert_pdf_to_png_route():
    file = request.files.get('pdf_file')
    if not file:
        return jsonify({'error': 'No file uploaded'}), 400

    try:
        result = convert_pdf_to_png(file)
        if result.get("error"):
            return jsonify({'error': result["error"]}), 500

        return jsonify(result)
    except Exception as e:
        return jsonify({'error': f'Unexpected error: {str(e)}'}), 500


@app.route('/converted/<filename>')
def serve_converted_png_files(filename):
    try:
        return send_from_directory(OUTPUT_FOLDER, filename)
    except FileNotFoundError:
        return jsonify({'error': 'File not found'}), 404
    except Exception as e:
        return jsonify({'error': str(e)}), 500


# PDF To PDF/A
def convert_to_pdfa(input_path, output_path, pdfa_version):
    """Convert PDF to PDF/A using Ghostscript"""
    try:
        # Validate PDF/A version
        valid_versions = ['1A', '1B', '2A', '2B', '2U', '3A', '3B', '3U']
        if pdfa_version not in valid_versions:
            raise ValueError(f"Invalid PDF/A version. Must be one of: {', '.join(valid_versions)}")

        # Ghostscript command for PDF/A conversion
        cmd = [
            'gswin64',
            '-dPDFA',
            f'-dPDFACompatibilityPolicy={pdfa_version}',
            '-dBATCH',
            '-dNOPAUSE',
            '-dNOOUTERSAVE',
            '-dUseCIEColor',
            '-sProcessColorModel=DeviceRGB',
            '-sDEVICE=pdfwrite',
            f'-sOutputFile={output_path}',
            input_path
        ]
        
        result = subprocess.run(cmd, capture_output=True, text=True)
        if result.returncode != 0:
            raise Exception(f"Ghostscript error: {result.stderr}")
            
        return True
    except Exception as e:
        raise Exception(f"Conversion failed: {str(e)}")

@app.route('/convert-to-pdfa', methods=['POST'])
def convert_pdf_to_pdfa():
    try:
        # Check if file is present
        if 'pdf_file' not in request.files:
            return jsonify({'error': 'No file uploaded'}), 400
            
        file = request.files['pdf_file']
        pdfa_version = request.form.get('pdfa_version', '1B') 
        
        if file.filename == '':
            return jsonify({'error': 'No selected file'}), 400

        # Generate unique filenames
        original_filename = secure_filename(file.filename)
        temp_filename = f"temp_{uuid.uuid4()}_{original_filename}"
        converted_filename = f"pdfa_{pdfa_version}_{uuid.uuid4()}_{original_filename}"
        
        # Save paths
        temp_path = os.path.join(app.config['UPLOAD_FOLDER'], temp_filename)
        converted_path = os.path.join(app.config['OUTPUT_FOLDER'], converted_filename)
        
        # Save uploaded file temporarily
        file.save(temp_path)
        
        # Convert to PDF/A
        try:
            convert_to_pdfa(temp_path, converted_path, pdfa_version)
            
            # Verify the output file was created
            if not os.path.exists(converted_path):
                raise Exception("Output file not created")
            
            # Return success response
            return jsonify({
                'success': True,
                'filename': converted_filename,
                'pdfa_version': pdfa_version,
                'download_url': url_for('download_pdfa', filename=converted_filename, _external=True)
            })
            
        except Exception as conversion_error:
            raise Exception(f"Conversion error: {str(conversion_error)}")
            
    except Exception as e:
        return jsonify({'error': str(e)}), 500
    finally:
        # Clean up temp file if it exists
        if 'temp_path' in locals() and os.path.exists(temp_path):
            try:
                os.remove(temp_path)
            except:
                pass

@app.route('/download-pdfa/<filename>')
def download_pdfa(filename):
    try:
        file_path = os.path.join(app.config['OUTPUT_FOLDER'], filename)
        
        if not os.path.exists(file_path):
            return jsonify({'error': 'File not found'}), 404
            
        return send_from_directory(
            app.config['OUTPUT_FOLDER'],
            filename,
            as_attachment=True,
            mimetype='application/pdf',
            download_name=filename
        )
    except Exception as e:
        return jsonify({'error': str(e)}), 500

# Merge PDFs
@app.route('/merge-pdfs', methods=['POST'])
def merge_pdfs_route():
    files = request.files.getlist('pdf_files')
    if len(files) < 2:
        return jsonify({'error': 'At least two PDF files are required'}), 400

    try:
        # Ensure directories exist
        os.makedirs(UPLOAD_FOLDER, exist_ok=True)
        os.makedirs(OUTPUT_FOLDER, exist_ok=True)
        
        output_path = merge_pdfs(files, UPLOAD_FOLDER, OUTPUT_FOLDER)
        filename = os.path.basename(output_path)
        
        return jsonify({
            'pdf_path': url_for('download_merged_pdf', filename=filename),
            'filename': filename
        })
    except Exception as e:
        app.logger.error(f"Error merging PDFs: {str(e)}")
        return jsonify({'error': str(e)}), 500

@app.route('/output/<filename>')
def download_merged_pdf(filename):
    try:
        file_path = os.path.join(OUTPUT_FOLDER, filename)
        
        if not os.path.exists(file_path):
            raise FileNotFoundError(f"File {filename} not found")
            
        # Send file with proper headers for inline display
        response = make_response(send_file(
            file_path,
            mimetype='application/pdf',
            as_attachment=False, 
            download_name=filename
        ))
        
        # Disable caching
        response.headers['Cache-Control'] = 'no-cache, no-store, must-revalidate'
        response.headers['Pragma'] = 'no-cache'
        response.headers['Expires'] = '0'
        response.headers['X-Content-Type-Options'] = 'nosniff'
        
        return response
    except Exception as e:
        app.logger.error(f"Error serving merged PDF: {str(e)}")
        return jsonify({'error': str(e)}), 404


# Split PDFs
@app.route('/upload-pdf', methods=['POST'])
def upload_pdf():
    file = request.files.get('pdf_file')
    if not file:
        return jsonify({'error': 'No file uploaded'}), 400

    try:
        # Create a unique filename 
        original_filename = secure_filename(file.filename)  
        filename = f"{uuid.uuid4()}_{original_filename}"
        file_path = os.path.join(UPLOAD_FOLDER, filename)

        # Create upload directory if it doesn't exist
        os.makedirs(UPLOAD_FOLDER, exist_ok=True)
        
        # Save the file
        file.save(file_path)

        return jsonify({
            'file_path': filename,  
            'original_filename': original_filename
        })
    except Exception as e:
        return jsonify({'error': f"Failed to upload file: {str(e)}"}), 500

@app.route('/split-editor', methods=['GET'])
def split_editor():
    filename = request.args.get('file')  
    if not filename:
        return jsonify({'error': 'No file specified'}), 400
    
    # URL decode the filename
    decoded_filename = unquote(filename)
    
    # Check if file exists in uploads directory
    file_path = os.path.join(UPLOAD_FOLDER, decoded_filename)
    if not os.path.exists(file_path):
        app.logger.error(f"File not found: {file_path}")
        return jsonify({'error': 'File not found'}), 404
    
    # Pass the properly encoded URL to the template
    return render_template('split_editor.html', file_url=url_for('serve_uploaded_file', filename=filename))

@app.route('/uploads/<filename>')
def serve_uploaded_file(filename):
    return send_from_directory(UPLOAD_FOLDER, filename)

@app.route('/output/<filename>')
def serve_output_file(filename):
    return send_from_directory(OUTPUT_FOLDER, filename)


@app.route('/split-pdf', methods=['POST'])
def split_pdf():
    data = request.json
    filename = data.get('filePath')  
    
    if not filename:
        return jsonify({'error': 'No filename provided'}), 400
    
    # URL decode the filename and reconstruct the full path
    decoded_filename = unquote(filename)
    file_path = os.path.join(UPLOAD_FOLDER, decoded_filename)
    
    if not os.path.exists(file_path):
        app.logger.error(f"File not found at path: {file_path}")
        app.logger.error(f"Files in upload folder: {os.listdir(UPLOAD_FOLDER)}")
        return jsonify({'error': f"File not found at path: {file_path}"}), 400

    try:
        options = {
            'splitAfter': data.get('splitAfter'),
            'rangeStart': data.get('rangeStart'),
            'rangeEnd': data.get('rangeEnd'),
            'customPages': data.get('customPages'),
            'oddPages': data.get('oddPages'),
            'evenPages': data.get('evenPages'),
        }
        output_files = split_pdf_logic(file_path, options, OUTPUT_FOLDER)
        return jsonify({
            'output_files': [url_for('serve_output_file', filename=os.path.basename(f)) for f in output_files]
        })
    except Exception as e:
        app.logger.error(f"Error splitting PDF: {e}")
        return jsonify({'error': f"Failed to process PDF: {str(e)}"}), 500


# Protect PDF 
@app.route('/protect-pdf', methods=['POST'])
def protect_pdf():
    file = request.files.get('pdf_file')
    password = request.form.get('password')

    if not file or not password:
        return jsonify({'error': 'File and password are required'}), 400

    try:
        original_filename = secure_filename(file.filename)
        temp_filename = f"{uuid.uuid4()}_{original_filename}"
        temp_path = os.path.join(UPLOAD_FOLDER, temp_filename)
        protected_filename = f"protected_{uuid.uuid4()}_{original_filename}"
        protected_path = os.path.join(OUTPUT_FOLDER, protected_filename)

        file.save(temp_path)

        reader = PdfReader(temp_path)
        writer = PdfWriter()
        for page in reader.pages:
            writer.add_page(page)
        writer.encrypt(password)

        with open(protected_path, "wb") as output_file:
            writer.write(output_file)

        os.remove(temp_path)

        return jsonify({
            'success': True,
            'filename': protected_filename,
            'download_url': url_for('download_protected', filename=protected_filename, _external=True)
        })

    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/download-protected/<filename>')
def download_protected(filename):
    try:
        file_path = os.path.join(OUTPUT_FOLDER, filename)
        if not os.path.exists(file_path):
            return jsonify({'error': 'File not found'}), 404
        return send_from_directory(OUTPUT_FOLDER, filename, as_attachment=True)
    except Exception as e:
        return jsonify({'error': str(e)}), 404


# Unlock PDF 

@app.route('/unlock-pdf', methods=['POST'])
def unlock_pdf():
    try:
        if 'pdf_file' not in request.files:
            return jsonify({'error': 'No file uploaded'}), 400

        file = request.files['pdf_file']
        password = request.form.get('password')

        if not password:
            return jsonify({'error': 'Password is required'}), 400

        if file.filename == '':
            return jsonify({'error': 'No selected file'}), 400

        # Generate unique filenames
        original_filename = secure_filename(file.filename)
        temp_filename = f"temp_{uuid.uuid4()}_{original_filename}"
        unlocked_filename = f"unlocked_{uuid.uuid4()}_{original_filename}"

        # Define paths for temporary and output files
        temp_path = os.path.join(app.config['UPLOAD_FOLDER'], temp_filename)
        unlocked_path = os.path.join(app.config['OUTPUT_FOLDER'], unlocked_filename)

        # Save uploaded file
        file.save(temp_path)

        try:
            # Process the uploaded PDF
            reader = PdfReader(temp_path)
            if not reader.is_encrypted:
                return jsonify({'error': 'PDF is not password protected'}), 400

            if not reader.decrypt(password):
                return jsonify({'error': 'Incorrect password'}), 401

            writer = PdfWriter()
            for page in reader.pages:
                writer.add_page(page)

            # Save unlocked PDF
            with open(unlocked_path, "wb") as f:
                writer.write(f)

            return jsonify({
                'success': True,
                'filename': unlocked_filename,
                'download_url': url_for('download_unlocked', filename=unlocked_filename, _external=True)
            })

        except Exception as pdf_error:
            return jsonify({'error': f'PDF processing error: {str(pdf_error)}'}), 500

    except Exception as e:
        return jsonify({'error': f'Server error: {str(e)}'}), 500

    finally:
        # Cleanup temporary files
        if 'temp_path' in locals() and os.path.exists(temp_path):
            os.remove(temp_path)

@app.route('/download-unlocked/<filename>')
def download_unlocked(filename):
    try:
        file_path = os.path.join(app.config['OUTPUT_FOLDER'], filename)

        if not os.path.exists(file_path):
            return jsonify({'error': 'File not found'}), 404

        return send_from_directory(
            app.config['OUTPUT_FOLDER'],
            filename,
            as_attachment=True,
            mimetype='application/pdf',
            download_name=filename
        )
    except Exception as e:
        return jsonify({'error': f'Download error: {str(e)}'}), 500


# iWork to PDF
def allowed_iwork_file(filename):
    return '.' in filename and \
           filename.rsplit('.', 1)[1].lower() in app.config['ALLOWED_IWORK_EXTENSIONS']

def convert_iwork_to_pdf(input_path, output_path):
    try:
        cmd = f"soffice --headless --convert-to pdf {input_path} --outdir {os.path.dirname(output_path)}"
        result = subprocess.run(cmd, shell=True, check=True, capture_output=True, text=True)
        print("Conversion output:", result.stdout)
        print("Conversion errors:", result.stderr)
        return True
    except subprocess.CalledProcessError as e:
        print(f"Conversion error: {e.stderr}")
        return False
    except Exception as e:
        print(f"Unexpected error: {str(e)}")
        return False

@app.route('/convert-iwork-to-pdf', methods=['POST'])
def handle_iwork_conversion():
    if 'file' not in request.files:
        return jsonify({'success': False, 'message': 'No file uploaded'}), 400

    file = request.files['file']
    if file.filename == '':
        return jsonify({'success': False, 'message': 'No selected file'}), 400

    if not allowed_iwork_file(file.filename):
        return jsonify({'success': False, 'message': 'Invalid file type'}), 400

    try:
        filename = secure_filename(file.filename)
        unique_id = uuid.uuid4().hex
        upload_path = os.path.join(app.config['UPLOAD_FOLDER'], f"{unique_id}_{filename}")
        file.save(upload_path)

        output_filename = f"{os.path.splitext(filename)[0]}.pdf"
        output_path = os.path.join(app.config['OUTPUT_FOLDER'], f"{unique_id}_{output_filename}")

        if convert_iwork_to_pdf(upload_path, output_path):
            pdf_url = f"/outputs/{unique_id}_{output_filename}"
            return jsonify({
                'success': True,
                'pdfUrl': pdf_url,
                'filename': output_filename
            })
        return jsonify({'success': False, 'message': 'Conversion failed'}), 500
    except Exception as e:
        print(f"Server error: {str(e)}")
        return jsonify({'success': False, 'message': 'Server error during conversion'}), 500


@app.route('/outputs/<filename>')
def serve_iwork_pdf(filename):
    try:
        file_path = os.path.join(app.config['OUTPUT_FOLDER'], filename)
        if not os.path.exists(file_path):
            return jsonify({'success': False, 'message': 'File not found'}), 404
        
        # Send file with correct headers
        response = make_response(send_file(
            file_path,
            mimetype='application/pdf',
            as_attachment=False,
            download_name=filename
        ))
        
        # Critical headers to prevent caching issues
        response.headers['Cache-Control'] = 'no-store, no-cache, must-revalidate'
        response.headers['Pragma'] = 'no-cache'
        response.headers['Expires'] = '0'
        return response
        
    except Exception as e:
        print(f"Error serving PDF: {str(e)}")
        return jsonify({'success': False, 'message': 'Error serving PDF'}), 500


# eBook to PDF 
def allowed_ebook_file(filename):
    ALLOWED_EBOOK_EXTENSIONS = {'epub', 'mobi', 'azw', 'fb2'}
    return '.' in filename and filename.rsplit('.', 1)[1].lower() in ALLOWED_EBOOK_EXTENSIONS

@app.route('/convert-ebook-to-pdf', methods=['POST'])
def handle_ebook_conversion():
    if 'file' not in request.files:
        return jsonify({'success': False, 'message': 'No file uploaded'}), 400

    file = request.files['file']
    if file.filename == '':
        return jsonify({'success': False, 'message': 'No selected file'}), 400

    if not allowed_ebook_file(file.filename):
        return jsonify({'success': False, 'message': 'Invalid file type. Supported formats: EPUB, MOBI, AZW, FB2'}), 400

    try:
        filename = secure_filename(file.filename)
        unique_id = uuid.uuid4().hex
        upload_path = os.path.join(app.config['UPLOAD_FOLDER'], f"{unique_id}_{filename}")
        output_filename = f"{os.path.splitext(filename)[0]}.pdf"
        output_path = os.path.join(app.config['OUTPUT_FOLDER'], f"{unique_id}_{output_filename}")

        # Create directories if they don't exist
        os.makedirs(app.config['UPLOAD_FOLDER'], exist_ok=True)
        os.makedirs(app.config['OUTPUT_FOLDER'], exist_ok=True)

        # Save the uploaded file
        file.save(upload_path)

        # Verify file is not empty
        if os.path.getsize(upload_path) == 0:
            os.remove(upload_path)
            return jsonify({'success': False, 'message': 'Uploaded file is empty'}), 400

        # Standard conversion command that works for all formats
        cmd = [
            'ebook-convert',
            upload_path,
            output_path,
            '--embed-all-fonts',
            '--enable-heuristics',
            '--pdf-page-margin-left', '20',
            '--pdf-page-margin-right', '20',
            '--pdf-page-margin-top', '20',
            '--pdf-page-margin-bottom', '20',
            '--output-profile', 'tablet'  
        ]

        try:
            print(f"Executing command: {' '.join(cmd)}")
            result = subprocess.run(
                cmd,
                stdout=subprocess.PIPE,
                stderr=subprocess.PIPE,
                text=True,
                errors='replace'
            )
            
            print("Conversion stdout:", result.stdout)
            print("Conversion stderr:", result.stderr)
            
            if result.returncode != 0:
                error_msg = result.stderr if result.stderr else "Conversion failed"
                return jsonify({
                    'success': False,
                    'message': f'Conversion failed: {error_msg}'
                }), 500

            if not os.path.exists(output_path) or os.path.getsize(output_path) == 0:
                return jsonify({
                    'success': False,
                    'message': 'Conversion completed but output file is empty'
                }), 500

            pdf_url = f"/ebook-outputs/{unique_id}_{output_filename}"
            return jsonify({
                'success': True,
                'pdfUrl': pdf_url,
                'filename': output_filename
            })

        except Exception as e:
            print(f"Conversion error: {str(e)}")
            return jsonify({
                'success': False,
                'message': f'Conversion failed: {str(e)}'
            }), 500

    except Exception as e:
        print(f"Server error: {str(e)}")
        return jsonify({
            'success': False,
            'message': f'Server error: {str(e)}'
        }), 500
    finally:
        if 'upload_path' in locals() and os.path.exists(upload_path):
            try:
                os.remove(upload_path)
            except Exception as e:
                print(f"Error cleaning up file: {str(e)}")

@app.route('/ebook-outputs/<path:filename>')
def serve_ebook_pdf(filename):
    """Serve converted eBook PDF files from the output directory"""
    try:
        return send_from_directory(
            app.config['OUTPUT_FOLDER'],
            filename, 
            as_attachment=False,
            mimetype='application/pdf'
        )
    except FileNotFoundError:
        return jsonify({'success': False, 'message': 'PDF file not found'}), 404


# Sign Document 
@app.route('/upload-signing-document', methods=['POST'])
def upload_signing_document():
    file = request.files.get('document_file')
    if not file:
        return jsonify({"error": "No file provided"}), 400

    filename = secure_filename(file.filename)
    if not (filename.lower().endswith('.pdf') or filename.lower().endswith('.doc') or filename.lower().endswith('.docx')):
        return jsonify({"error": "Invalid file type. Only PDF, DOC, and DOCX files are allowed."}), 400

    file_path = os.path.join(UPLOAD_FOLDER, filename)
    file.save(file_path)

    # Convert Word docs to PDF for display
    if filename.lower().endswith(('.doc', '.docx')):
        try:
            pdf_path = os.path.join(UPLOAD_FOLDER, f"{os.path.splitext(filename)[0]}.pdf")
            pypandoc.convert_file(file_path, 'pdf', outputfile=pdf_path)
            display_filename = f"{os.path.splitext(filename)[0]}.pdf"
        except Exception as e:
            return jsonify({"error": f"Failed to convert Word document: {str(e)}"}), 400
    else:
        display_filename = filename

    return jsonify({
        "document_id": filename,
        "display_document_id": display_filename,
        "redirect_url": f"/sign-document/{filename}",
        "original_extension": os.path.splitext(filename)[1].lower()
    })


@app.route('/sign-document/<document_id>')
def sign_document(document_id):
    document_path = os.path.join(UPLOAD_FOLDER, document_id)
    if not os.path.exists(document_path):
        return "Document not found", 404

    # Get original extension
    original_extension = os.path.splitext(document_id)[1].lower()
    
    # Determine which file to display (original or converted PDF)
    if original_extension in ('.doc', '.docx'):
        display_document_id = f"{os.path.splitext(document_id)[0]}.pdf"
    else:
        display_document_id = document_id

    
    return render_template_string("""
    <!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Document</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.13.216/pdf.min.js"></script>
  <style>
    #pdf-container {
      width: 100%;
      display: flex;
      justify-content: center;
      margin-bottom: 20px;
    }
    #pdfViewer {
      width: 100%;
      max-width: 800px;
      height: 80vh;
      overflow-y: scroll;
      border: 1px solid #ccc;
      position: relative;
    }
    .page-container {
      position: relative;
      margin-bottom: 20px;
      background-color: white;
      box-shadow: 0 0 5px rgba(0,0,0,0.3);
    }
    canvas.page {
      display: block;
      margin: 0 auto;
      max-width: 100%;
    }
    .signature-preview {
      position: absolute;
      cursor: move;
      z-index: 100;
      max-width: 200px;
      border: 1px dashed #888;
      overflow: hidden;
    }
    #signatureCanvas {
      border: 1px solid #000;
      width: 100%;
      height: 200px;
      background-color: white;
      touch-action: none;
    }
    #typedPreview {
      font-family: 'Brush Script MT', cursive;
      font-size: 2rem;
      min-height: 50px;
      border: 1px dashed #ccc;
      padding: 10px;
      margin-top: 10px;
    }
    #uploadPreview {
      max-height: 150px;
      display: none;
      margin-top: 10px;
    }
    .instructions {
      background-color: #f8f9fa;
      padding: 15px;
      border-radius: 5px;
      margin-bottom: 20px;
    }
    .resize-handle {
      position: absolute;
      width: 10px;
      height: 10px;
      background: #555;
      right: 0;
      bottom: 0;
      cursor: nwse-resize;
    }
  </style>
</head>
<body>
<div class="container mt-4">
  <h1>Sign Document</h1>
  <div class="instructions alert alert-info">
    <strong>Instructions:</strong> Click anywhere on the document to place your signature. 
    Drag to reposition and resize as needed. When finished, click "Save & Download".
  </div>
  <div id="pdf-container">
    <div id="pdfViewer"></div>
  </div>
  <button id="saveDocument" class="btn btn-success mt-3">Save & Download Signed Document</button>
</div>

<!-- Signature Modal -->
<div class="modal fade" id="signatureModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Create Signature</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <ul class="nav nav-tabs">
          <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#draw">Draw</button></li>
          <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#type">Type</button></li>
          <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#upload">Upload</button></li>
        </ul>

        <div class="tab-content mt-3">
          <div class="tab-pane fade show active" id="draw">
            <canvas id="signatureCanvas"></canvas>
            <button id="clearDraw" class="btn btn-warning mt-2">Clear</button>
          </div>

          <div class="tab-pane fade" id="type">
            <input type="text" id="typedSignature" class="form-control" placeholder="Type your name">
            <div id="typedPreview">Signature will appear here</div>
            <button id="clearType" class="btn btn-warning mt-2">Clear</button>
          </div>

          <div class="tab-pane fade" id="upload">
            <input type="file" id="uploadSignature" class="form-control" accept="image/*">
            <img id="uploadPreview" class="img-fluid mt-2">
            <button id="clearUpload" class="btn btn-warning mt-2">Clear</button>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button id="addSignature" class="btn btn-primary">Add Signature</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const pdfUrl = "/uploads/{{ document_id }}";
const pdfViewer = document.getElementById("pdfViewer");
const originalExtension = "{{ original_extension }}";
let currentPage = 0;
let pageContainers = [];
let signatures = [];
let pdfDoc = null;
let pageRects = [];

// Load PDF with page containers
pdfjsLib.getDocument(pdfUrl).promise.then(pdf => {
  pdfDoc = pdf;
  for (let i = 1; i <= pdf.numPages; i++) {
    const pageContainer = document.createElement("div");
    pageContainer.className = "page-container";
    pageContainer.dataset.pageNumber = i;
    pdfViewer.appendChild(pageContainer);
    pageContainers.push(pageContainer);

    pdf.getPage(i).then(page => {
      const viewport = page.getViewport({ scale: 1.0 });
      const canvas = document.createElement("canvas");
      canvas.className = "page";
      const context = canvas.getContext("2d");
      
      // Adjust canvas size to fit container while maintaining aspect ratio
      const containerWidth = pdfViewer.clientWidth - 40; // Account for padding
      const scale = containerWidth / viewport.width;
      const scaledViewport = page.getViewport({ scale: scale });
      
      canvas.width = scaledViewport.width;
      canvas.height = scaledViewport.height;
      
      page.render({
        canvasContext: context,
        viewport: scaledViewport
      });
      
      pageContainer.appendChild(canvas);
      
      // Store the page dimensions and scale factor
      pageRects[i-1] = {
        viewport: viewport,
        scaledViewport: scaledViewport,
        scale: scale,
        containerWidth: containerWidth
      };

      // Add click handler for each page
      pageContainer.addEventListener('click', function(e) {
        if (e.target.tagName !== 'CANVAS') return;
        
        const rect = canvas.getBoundingClientRect();
        const clickX = e.clientX - rect.left;
        const clickY = e.clientY - rect.top;
        
        currentPage = i;
        const modal = new bootstrap.Modal(document.getElementById('signatureModal'));
        modal.show();
      });
    });
  }
});

// Drawing Canvas Setup
const canvas = document.getElementById("signatureCanvas");
canvas.width = 400;
canvas.height = 150;
const ctx = canvas.getContext("2d");
ctx.fillStyle = "rgba(255,255,255,0)";
ctx.fillRect(0, 0, canvas.width, canvas.height);
ctx.strokeStyle = 'black';
ctx.lineWidth = 2;
ctx.lineCap = 'round';

let isDrawing = false;
let lastX = 0, lastY = 0;

canvas.addEventListener('mousedown', e => {
    isDrawing = true;
    [lastX, lastY] = [e.offsetX, e.offsetY];
});
canvas.addEventListener('mousemove', e => {
    if (!isDrawing) return;
    ctx.beginPath();
    ctx.moveTo(lastX, lastY);
    ctx.lineTo(e.offsetX, e.offsetY);
    ctx.stroke();
    [lastX, lastY] = [e.offsetX, e.offsetY];
});
canvas.addEventListener('mouseup', () => isDrawing = false);
canvas.addEventListener('mouseout', () => isDrawing = false);
canvas.addEventListener('touchstart', e => {
    const touch = e.touches[0];
    const rect = canvas.getBoundingClientRect();
    const mouseEvent = new MouseEvent('mousedown', {
        clientX: touch.clientX,
        clientY: touch.clientY
    });
    canvas.dispatchEvent(mouseEvent);
});
canvas.addEventListener('touchmove', e => {
    const touch = e.touches[0];
    const rect = canvas.getBoundingClientRect();
    const mouseEvent = new MouseEvent('mousemove', {
        clientX: touch.clientX,
        clientY: touch.clientY
    });
    canvas.dispatchEvent(mouseEvent);
});
canvas.addEventListener('touchend', () => {
    const mouseEvent = new MouseEvent('mouseup', {});
    canvas.dispatchEvent(mouseEvent);
});

document.getElementById("clearDraw").addEventListener("click", function () {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.fillStyle = "rgba(255,255,255,0)";
    ctx.fillRect(0, 0, canvas.width, canvas.height);
});

// Typed Signature
document.getElementById("typedSignature").addEventListener("input", function () {
    document.getElementById("typedPreview").textContent = this.value || "Signature will appear here";
});
document.getElementById("clearType").addEventListener("click", function () {
    document.getElementById("typedSignature").value = "";
    document.getElementById("typedPreview").textContent = "Signature will appear here";
});

// Upload Signature
document.getElementById("uploadSignature").addEventListener("change", function (e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function (event) {
            const preview = document.getElementById("uploadPreview");
            preview.src = event.target.result;
            preview.style.display = "block";
        };
        reader.readAsDataURL(file);
    }
});
document.getElementById("clearUpload").addEventListener("click", function () {
    document.getElementById("uploadSignature").value = "";
    document.getElementById("uploadPreview").src = "";
    document.getElementById("uploadPreview").style.display = "none";
});

// Add Signature Button
document.getElementById("addSignature").addEventListener("click", function () {
    const activeTab = document.querySelector('.tab-pane.active').id;
    let signatureData = null;

    if (activeTab === 'draw') {
        const blankCanvas = document.createElement('canvas');
        blankCanvas.width = canvas.width;
        blankCanvas.height = canvas.height;
        const blankCtx = blankCanvas.getContext('2d');
        blankCtx.clearRect(0, 0, blankCanvas.width, blankCanvas.height);
        if (canvas.toDataURL() === blankCanvas.toDataURL()) {
            alert("Please draw your signature first");
            return;
        }
        signatureData = canvas.toDataURL("image/png");
    } else if (activeTab === 'type') {
        const text = document.getElementById("typedSignature").value.trim();
        if (!text) {
            alert("Please type your signature first");
            return;
        }
        const tempCanvas = document.createElement("canvas");
        tempCanvas.width = 300;
        tempCanvas.height = 100;
        const ctx = tempCanvas.getContext("2d");
        ctx.clearRect(0, 0, tempCanvas.width, tempCanvas.height);
        ctx.font = "36px 'Brush Script MT', cursive";
        ctx.fillStyle = "black";
        ctx.fillText(text, 10, 50);
        signatureData = tempCanvas.toDataURL("image/png");
    } else if (activeTab === 'upload') {
        const preview = document.getElementById("uploadPreview");
        if (!preview.src) {
            alert("Please upload a signature image first");
            return;
        }
        signatureData = preview.src;
    }

    // Get the page container for the current page
    const pageContainer = document.querySelector(`.page-container[data-page-number="${currentPage}"]`);
    if (!pageContainer) return;

    // Create signature element
    const wrapper = document.createElement("div");
    wrapper.className = "signature-preview";
    wrapper.style.position = "absolute";
    wrapper.style.left = "50px";
    wrapper.style.top = "50px";
    wrapper.style.width = "200px";
    wrapper.style.height = "100px";

    const img = document.createElement("img");
    img.src = signatureData;
    img.style.width = "100%";
    img.style.height = "100%";
    img.style.objectFit = "contain";
    img.style.pointerEvents = "none";

    const closeBtn = document.createElement("div");
    closeBtn.textContent = "×";
    closeBtn.style.position = "absolute";
    closeBtn.style.top = "0";
    closeBtn.style.right = "0";
    closeBtn.style.background = "rgba(255,255,255,0.7)";
    closeBtn.style.border = "1px solid #aaa";
    closeBtn.style.cursor = "pointer";
    closeBtn.style.padding = "0 6px";
    closeBtn.style.fontSize = "16px";
    closeBtn.style.fontWeight = "bold";
    closeBtn.addEventListener("click", () => {
        wrapper.remove();
        signatures = signatures.filter(sig => sig.element !== wrapper);
    });

    // Add resize handle
    const resizeHandle = document.createElement("div");
    resizeHandle.className = "resize-handle";
    resizeHandle.addEventListener('mousedown', initResize);

    wrapper.appendChild(img);
    wrapper.appendChild(closeBtn);
    wrapper.appendChild(resizeHandle);
    pageContainer.appendChild(wrapper);

    // Store signature data with accurate position info
    const pageInfo = pageRects[currentPage-1];
    const signature = {
        element: wrapper,
        page: currentPage,
        data: signatureData,
        position: { 
            left: 50, 
            top: 50, 
            width: 200, 
            height: 100,
            scale: pageInfo.scale,
            originalWidth: pageInfo.viewport.width,
            originalHeight: pageInfo.viewport.height
        }
    };
    signatures.push(signature);

    // Make draggable
    makeDraggable(wrapper, signature);
    
    bootstrap.Modal.getInstance(document.getElementById('signatureModal')).hide();
});

function makeDraggable(el, signature) {
    let pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;

    el.onmousedown = dragMouseDown;
    el.ontouchstart = dragMouseDown;

    function dragMouseDown(e) {
        e.preventDefault();
        
        // Don't drag if clicking on close button or resize handle
        if (e.target === el.querySelector('div') || e.target.className === 'resize-handle') {
            return;
        }

        pos3 = e.clientX || e.touches[0].clientX;
        pos4 = e.clientY || e.touches[0].clientY;
        
        document.onmouseup = closeDragElement;
        document.ontouchend = closeDragElement;
        document.onmousemove = elementDrag;
        document.ontouchmove = elementDrag;
    }

    function elementDrag(e) {
        e.preventDefault();
        const clientX = e.clientX || e.touches[0].clientX;
        const clientY = e.clientY || e.touches[0].clientY;
        
        pos1 = pos3 - clientX;
        pos2 = pos4 - clientY;
        pos3 = clientX;
        pos4 = clientY;
        
        const pageContainer = el.parentElement;
        const canvas = pageContainer.querySelector('canvas');
        const canvasRect = canvas.getBoundingClientRect();
        
        let newTop = el.offsetTop - pos2;
        let newLeft = el.offsetLeft - pos1;
        
        // Constrain to page container
        newTop = Math.max(0, Math.min(newTop, canvasRect.height - el.offsetHeight));
        newLeft = Math.max(0, Math.min(newLeft, canvasRect.width - el.offsetWidth));
        
        el.style.top = newTop + "px";
        el.style.left = newLeft + "px";
        
        // Update signature position with accurate coordinates
        signature.position = {
            left: newLeft,
            top: newTop,
            width: el.offsetWidth,
            height: el.offsetHeight,
            scale: signature.position.scale,
            originalWidth: signature.position.originalWidth,
            originalHeight: signature.position.originalHeight
        };
    }

    function closeDragElement() {
        document.onmouseup = null;
        document.ontouchend = null;
        document.onmousemove = null;
        document.ontouchmove = null;
    }
}

function initResize(e) {
    e.preventDefault();
    e.stopPropagation();
    
    const el = this.parentElement;
    const signature = signatures.find(sig => sig.element === el);
    const startX = e.clientX;
    const startY = e.clientY;
    const startWidth = parseInt(document.defaultView.getComputedStyle(el).width, 10);
    const startHeight = parseInt(document.defaultView.getComputedStyle(el).height, 10);
    
    function doResize(e) {
        const width = startWidth + (e.clientX - startX);
        const height = startHeight + (e.clientY - startY);
        if (width > 50 && height > 30) {
            el.style.width = width + 'px';
            el.style.height = height + 'px';
            
            // Update signature position
            signature.position.width = width;
            signature.position.height = height;
        }
    }
    
    function stopResize() {
        window.removeEventListener('mousemove', doResize, false);
        window.removeEventListener('mouseup', stopResize, false);
    }
    
    window.addEventListener('mousemove', doResize, false);
    window.addEventListener('mouseup', stopResize, false);
}

// Save Signed Document
const saveButton = document.getElementById("saveDocument");
saveButton.addEventListener("click", async function () {
    if (signatures.length === 0) {
        alert("Please add at least one signature first");
        return;
    }

    // Prepare signature data for all pages with accurate positioning
    const signatureData = signatures.map(sig => {
        const pageContainer = sig.element.parentElement;
        const canvas = pageContainer.querySelector('canvas');
        const canvasRect = canvas.getBoundingClientRect();
        
        // Calculate the position in the original document coordinates
        const scale = sig.position.scale;
        const originalLeft = sig.position.left / scale;
        const originalTop = sig.position.top / scale;
        const originalWidth = sig.position.width / scale;
        const originalHeight = sig.position.height / scale;
        
        return {
            page: sig.page,
            signature: sig.data,
            position: {
                x: originalLeft,
                y: sig.position.originalHeight - originalTop - originalHeight, // PDF coordinates have Y inverted
                width: originalWidth,
                height: originalHeight,
                originalWidth: sig.position.originalWidth,
                originalHeight: sig.position.originalHeight
            }
        };
    });

    try {
        const response = await fetch("/save-signed-document", {
            method: "POST",
            headers: { 
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                document_id: "{{ document_id }}",
                original_extension: originalExtension,
                signatures: signatureData
            })
        });

        if (!response.ok) throw new Error("Failed to save signed document");

        const blob = await response.blob();
        const a = document.createElement('a');
        a.href = window.URL.createObjectURL(blob);
        a.download = `signed_{{ document_id }}`;
        document.body.appendChild(a);
        a.click();
        a.remove();
    } catch (error) {
        alert("Error: " + error.message);
    }
});
</script>

    </body>
    </html>
    """, document_id=document_id, original_extension=original_extension)


@app.route('/save-signed-document', methods=['POST'])
def save_signed_document():
    data = request.json
    document_id = data.get('document_id')
    original_extension = data.get('original_extension')
    signatures_data = data.get('signatures', [])

    if not document_id or not signatures_data:
        return jsonify({"error": "Invalid data"}), 400

    document_path = os.path.join(UPLOAD_FOLDER, document_id)
    if not os.path.exists(document_path):
        return jsonify({"error": "Document not found"}), 404

    try:
        # Process all signatures
        signature_images = []
        for sig in signatures_data:
            if sig['signature'].startswith('data:'):
                signature_image = base64.b64decode(sig['signature'].split(',')[1])
            else:
                import requests
                response = requests.get(sig['signature'])
                signature_image = response.content

            with tempfile.NamedTemporaryFile(delete=False, suffix='.png') as tmp_sig:
                tmp_sig.write(signature_image)
                signature_images.append({
                    'path': tmp_sig.name,
                    'page': sig['page'] - 1,  # Convert to 0-based index
                    'position': sig['position']
                })

        if original_extension == '.pdf':
            # Handle PDF documents (existing code)
            original_pdf = PdfReader(document_path)
            output = PdfWriter()

            for page_num in range(len(original_pdf.pages)):
                page = original_pdf.pages[page_num]
                page_width = float(page.mediabox.width)
                page_height = float(page.mediabox.height)

                page_signatures = [sig for sig in signature_images if sig['page'] == page_num]
                
                if page_signatures:
                    packet = BytesIO()
                    can = canvas.Canvas(packet, pagesize=(page_width, page_height))
                    
                    for sig in page_signatures:
                        pos = sig['position']
                        x = pos['x']
                        y = pos['y']
                        width = pos['width']
                        height = pos['height']
                        can.drawImage(sig['path'], x, y, width, height, mask='auto')
                    
                    can.save()
                    packet.seek(0)
                    overlay_pdf = PdfReader(packet)
                    page.merge_page(overlay_pdf.pages[0])
                
                output.add_page(page)

            signed_filename = f"signed_{document_id}"
            signed_document_path = os.path.join(OUTPUT_FOLDER, signed_filename)

            with open(signed_document_path, "wb") as output_pdf:
                output.write(output_pdf)

        elif original_extension in ('.doc', '.docx'):
            # Handle Word documents properly
            doc = Document(document_path)
            
            # Create a temporary PDF to work with
            temp_pdf_path = os.path.join(tempfile.gettempdir(), f"temp_{document_id}.pdf")
            pypandoc.convert_file(document_path, 'pdf', outputfile=temp_pdf_path)
            
            # Add signatures to the PDF version
            original_pdf = PdfReader(temp_pdf_path)
            output = PdfWriter()

            for page_num in range(len(original_pdf.pages)):
                page = original_pdf.pages[page_num]
                page_width = float(page.mediabox.width)
                page_height = float(page.mediabox.height)

                page_signatures = [sig for sig in signature_images if sig['page'] == page_num]
                
                if page_signatures:
                    packet = BytesIO()
                    can = canvas.Canvas(packet, pagesize=(page_width, page_height))
                    
                    for sig in page_signatures:
                        pos = sig['position']
                        x = pos['x']
                        y = pos['y']
                        width = pos['width']
                        height = pos['height']
                        can.drawImage(sig['path'], x, y, width, height, mask='auto')
                    
                    can.save()
                    packet.seek(0)
                    overlay_pdf = PdfReader(packet)
                    page.merge_page(overlay_pdf.pages[0])
                
                output.add_page(page)

            # Save the signed PDF
            signed_pdf_filename = f"signed_{os.path.splitext(document_id)[0]}.pdf"
            signed_pdf_path = os.path.join(OUTPUT_FOLDER, signed_pdf_filename)

            with open(signed_pdf_path, "wb") as output_pdf:
                output.write(output_pdf)
            
            # For Word docs, we can either:
            # 1. Return the signed PDF (simpler)
            # 2. Or convert back to Word format (more complex)
            
            # Here we'll return the PDF for simplicity
            signed_document_path = signed_pdf_path
            signed_filename = signed_pdf_filename

        # Clean up temporary files
        for sig in signature_images:
            os.unlink(sig['path'])
        
        if original_extension in ('.doc', '.docx') and os.path.exists(temp_pdf_path):
            os.unlink(temp_pdf_path)

        return send_from_directory(OUTPUT_FOLDER, os.path.basename(signed_document_path), as_attachment=True)

    except Exception as e:
        return jsonify({"error": str(e)}), 500

@app.route('/download-signed/<filename>')
def download_signed(filename):
    return send_from_directory(OUTPUT_FOLDER, filename, as_attachment=True)

        
# Edit PDF Document
@app.route('/upload-pdf-document', methods=['POST'])
def upload_pdf_document():
    if 'pdfFile' not in request.files:
        return jsonify({"error": "No file provided"}), 400

    file = request.files['pdfFile']
    if file.filename == '':
        return jsonify({"error": "No file selected"}), 400

    if not file.filename.endswith('.pdf'):
        return jsonify({"error": "File must be a PDF"}), 400

    unique_filename = f"{uuid.uuid4().hex}.pdf"
    save_path = os.path.join(app.config['UPLOAD_FOLDER'], unique_filename)
    file.save(save_path)

    return jsonify({"fileUrl": f"/edited-uploads/{unique_filename}"}), 200

@app.route('/edited-uploads/<path:filename>')
def serve_edited_pdf(filename):
    return send_from_directory(app.config['UPLOAD_FOLDER'], filename)

@app.route('/pdf-document-editor')
def pdf_document_editor():
    pdf_file = request.args.get('file')
    if not pdf_file:
        return "PDF file not specified", 400

    return render_template_string("""
    <!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Advanced PDF Editor</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.13.216/pdf.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js"></script>
  <style>
    #pdfContainer {
      position: relative;
      width: 100%;
      height: 80vh;
      overflow: auto;
      border: 1px solid #ccc;
    }
    .pdfPage {
      margin: 10px auto;
      position: relative;
    }
    .editable-text {
      position: absolute;
      font-size: 16px;
      background-color: rgba(255, 255, 255, 0.8);
      border: 1px dashed #333;
      padding: 2px 4px;
      cursor: move;
    }
    #toolbar {
      margin-bottom: 1rem;
    }
  </style>
</head>
<body>
  <div class="container mt-4">
    <div id="toolbar" class="d-flex justify-content-between">
      <button id="addTextButton" class="btn btn-primary">Add Text</button>
      <button id="savePdfButton" class="btn btn-success">Save PDF</button>
    </div>
    <div id="pdfContainer"></div>
  </div>

  <script>
    const pdfUrl = "{{ request.args.get('file') }}"; // Backend-supplied PDF URL
    const pdfContainer = document.getElementById("pdfContainer");

    let pdfDoc = null;

    async function loadPdf() {
      const pdf = await pdfjsLib.getDocument(pdfUrl).promise;
      pdfDoc = pdf;

      for (let i = 1; i <= pdf.numPages; i++) {
        const page = await pdf.getPage(i);
        const viewport = page.getViewport({ scale: 1.5 });

        const canvas = document.createElement("canvas");
        canvas.width = viewport.width;
        canvas.height = viewport.height;
        canvas.classList.add("pdfPage");
        pdfContainer.appendChild(canvas);

        const context = canvas.getContext("2d");
        await page.render({ canvasContext: context, viewport }).promise;
      }
    }

    function addTextToPage() {
      const newText = document.createElement("div");
      newText.contentEditable = true;
      newText.textContent = "Editable Text";
      newText.classList.add("editable-text");
      newText.style.left = "50px";
      newText.style.top = "50px";
      pdfContainer.appendChild(newText);

      // Make the text draggable
      newText.onmousedown = function (event) {
        event.preventDefault();
        const shiftX = event.clientX - newText.getBoundingClientRect().left;
        const shiftY = event.clientY - newText.getBoundingClientRect().top;

        function moveAt(pageX, pageY) {
          newText.style.left = pageX - shiftX + "px";
          newText.style.top = pageY - shiftY + "px";
        }

        function onMouseMove(event) {
          moveAt(event.pageX, event.pageY);
        }

        document.addEventListener("mousemove", onMouseMove);

        newText.onmouseup = function () {
          document.removeEventListener("mousemove", onMouseMove);
          newText.onmouseup = null;
        };
      };

      newText.ondragstart = function () {
        return false;
      };
    }

    async function savePdf() {
      const pdfBytes = await fetch(pdfUrl).then((res) => res.arrayBuffer());
      const pdfDoc = await PDFLib.PDFDocument.load(pdfBytes);

      const pages = pdfDoc.getPages();
      const page = pages[0]; // Modify the first page

      // Add all editable text
      const textElements = document.querySelectorAll(".editable-text");
      for (const textElement of textElements) {
        const x = parseInt(textElement.style.left);
        const y = pdfContainer.offsetHeight - parseInt(textElement.style.top); // Flip y-axis
        const text = textElement.textContent;

        page.drawText(text, { x, y, size: 16, color: PDFLib.rgb(0, 0, 0) });
      }

      const pdfBytesFinal = await pdfDoc.save();
      const blob = new Blob([pdfBytesFinal], { type: "application/pdf" });
      const link = document.createElement("a");
      link.href = URL.createObjectURL(blob);
      link.download = "edited-document.pdf";
      link.click();
    }

    document.getElementById("addTextButton").addEventListener("click", addTextToPage);
    document.getElementById("savePdfButton").addEventListener("click", savePdf);

    loadPdf();
  </script>
</body>
</html>

    """)



# Background thread: Auto-cleanup old files
def cleanup_old_files(folder_paths, max_age_minutes=30, check_interval_seconds=600):
    def run_cleanup():
        while True:
            now = time.time()
            for folder in folder_paths:
                for filename in os.listdir(folder):
                    filepath = os.path.join(folder, filename)
                    if os.path.isfile(filepath):
                        age_minutes = (now - os.path.getmtime(filepath)) / 60
                        if age_minutes > max_age_minutes:
                            try:
                                os.remove(filepath)
                                print(f"Deleted old file: {filepath}")
                            except Exception as e:
                                print(f"Error deleting file {filepath}: {e}")
            time.sleep(check_interval_seconds)

    thread = threading.Thread(target=run_cleanup, daemon=True)
    thread.start()

# Default route to display welcome page
@app.route('/')
def welcome():
    return """
    <!DOCTYPE html>
    <html>
    <head>
        <title>PDFSimba Backend</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                line-height: 1.6;
                color: #333;
                max-width: 800px;
                margin: 0 auto;
                padding: 20px;
                text-align: center;
            }
            h1 {
                color: #2c3e50;
                font-size: 2.5em;
                margin-bottom: 10px;
            }
            .logo {
                margin-bottom: 20px;
                font-size: 5em;
            }
            .status {
                background-color: #f1f8e9;
                border-left: 5px solid #7cb342;
                padding: 15px;
                margin-bottom: 20px;
                text-align: left;
            }
            .endpoints {
                background-color: #e8f4fd;
                border-left: 5px solid #2196F3;
                padding: 15px;
                text-align: left;
            }
            .endpoint {
                margin-bottom: 10px;
                font-family: monospace;
            }
        </style>
    </head>
    <body>
        <div class="logo">📄✨</div>
        <h1>Welcome to PDFSimba Backend</h1>
        <p>The PDF conversion service is running successfully.</p>
        
        <div class="status">
            <h2>Server Status</h2>
            <p>The backend server is online and ready to process requests.</p>
        </div>
        
        <div class="endpoints">
            <h2>Available Endpoints</h2>
            <div class="endpoint">/convert-word-to-pdf</div>
            <div class="endpoint">/convert-excel-to-pdf</div>
            <div class="endpoint">/convert-ppt-to-pdf</div>
            <div class="endpoint">/convert-jpg-to-pdf</div>
            <div class="endpoint">/convert-pdf-to-word</div>
            <div class="endpoint">/convert-pdf-to-excel</div>
            <div class="endpoint">/convert-pdf-to-ppt</div>
            <div class="endpoint">/convert-pdf-to-jpg</div>
            <div class="endpoint">/convert-pdf-to-png</div>
            <p>And many more...</p>
        </div>
        
        <p>This is a backend service. Please use the frontend application to interact with these services.</p>
    </body>
    </html>
    """

# Run server
if __name__ == '__main__':
    cleanup_old_files([UPLOAD_FOLDER, OUTPUT_FOLDER])
    app.run(debug=True, host='0.0.0.0', port=5000)
