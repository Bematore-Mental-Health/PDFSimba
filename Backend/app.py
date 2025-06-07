from flask import Flask, request, jsonify, send_from_directory, render_template, url_for
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
from flask import Flask, request, jsonify, send_from_directory
from PyPDF2 import PdfReader,  PdfWriter
from flask_cors import CORS
from flask import Flask, request, jsonify, send_from_directory
from flask_cors import CORS
from flask import render_template
from urllib.parse import unquote
from werkzeug.utils import secure_filename
import tempfile
import subprocess





from flask import Flask, request, jsonify, send_from_directory, send_file, render_template
from pdf2image import convert_from_path
from pptx import Presentation
from pptx.util import Inches
import base64
from io import BytesIO
from flask import Flask, request, jsonify, send_file, make_response
from PyPDF2 import PdfMerger




from word_to_pdf import convert_word_file
from excel_to_pdf import convert_excel_file
from powerpoint_to_pdf import convert_ppt_file
from jpg_to_pdf import convert_jpg_file
from cad_to_pdf import convert_cad_file
from openoffice_to_pdf import convert_openoffice_file  
from pdf_to_word import convert_pdf_file
from pdf_to_excel import convert_pdf_to_excel
from pdf_to_ppt import convert_pdf_to_ppt 
from pdf_to_jpg import convert_pdf_to_jpg
from pdf_to_png import convert_pdf_to_png
from pdf_to_pdfa import convert_to_pdfa
from merge_pdfs import merge_pdfs
from split_pdfs import  split_pdf_logic




app = Flask(__name__)
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

# AutoCAD to PDF
@app.route('/convert-cad-to-pdf', methods=['POST'])
def convert_cad_route():
    file = request.files.get('cad_file')
    return convert_cad_file(file)

# OpenOffice to PDF (ODT, ODS, ODP)
@app.route('/convert-openoffice-to-pdf', methods=['POST'])
def convert_openoffice_route():
    file = request.files.get('openoffice_file')
    return convert_openoffice_file(file)

# Serve converted PDF files
@app.route('/converted/<path:filename>')
def serve_pdf(filename):
    return send_from_directory(OUTPUT_FOLDER, filename)


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

@app.route('/ppt-editor/<filename>')
def ppt_editor(filename):
    return render_template('ppt_editor.html', filename=filename)


@app.route('/download/<filename>')
def download_file(filename):
    return send_from_directory(OUTPUT_FOLDER, filename, as_attachment=True)

from io import BytesIO
import base64
from pptx import Presentation
from PIL import Image
import tempfile

@app.route('/get-slide-images/<filename>')
def get_slide_images(filename):
    ppt_path = os.path.join(OUTPUT_FOLDER, filename)
    if not os.path.exists(ppt_path):
        return jsonify({'error': 'File not found'}), 404

    prs = Presentation(ppt_path)
    slide_imgs = []

    for i, slide in enumerate(prs.slides):
        # Create a blank white image
        width = prs.slide_width // 9525  # convert EMUs to pixels (approx)
        height = prs.slide_height // 9525
        img = Image.new('RGB', (width, height), 'white')
        buffer = BytesIO()
        img.save(buffer, format="PNG")
        base64_img = base64.b64encode(buffer.getvalue()).decode('utf-8')
        slide_imgs.append(f"data:image/png;base64,{base64_img}")

    return jsonify(slide_imgs)


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
        pdfa_version = request.form.get('pdfa_version', '1B')  # Default to PDF/A-1b
        
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
            as_attachment=False,  # Crucial for preview
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
        # Create a unique filename - preserve original filename exactly
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

# Run server
if __name__ == '__main__':
    cleanup_old_files([UPLOAD_FOLDER, OUTPUT_FOLDER])
    app.run(debug=True, host='0.0.0.0', port=5000)
