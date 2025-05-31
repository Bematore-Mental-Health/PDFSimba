from flask import Flask, request, jsonify, send_from_directory
from flask_cors import CORS
import os
import threading
import time
from bs4 import BeautifulSoup
from docx import Document
from html2docx import html2docx



from word_to_pdf import convert_word_file
from excel_to_pdf import convert_excel_file
from powerpoint_to_pdf import convert_ppt_file
from jpg_to_pdf import convert_jpg_file
from cad_to_pdf import convert_cad_file
from openoffice_to_pdf import convert_openoffice_file  
from pdf_to_word import convert_pdf_file


app = Flask(__name__)
CORS(app)

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
UPLOAD_FOLDER = os.path.abspath(os.path.join(BASE_DIR, '..', 'uploads'))
OUTPUT_FOLDER = os.path.abspath(os.path.join(BASE_DIR, '..', 'converted'))

os.makedirs(UPLOAD_FOLDER, exist_ok=True)
os.makedirs(OUTPUT_FOLDER, exist_ok=True)

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
          <form method="POST" action="/save-edited-word/{filename}">
            <textarea id="editor" name="content"></textarea>
            <br>
            <button class="btn btn-success" type="submit">Save and Download</button>
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



# Background thread: Auto-cleanup old files
def cleanup_old_files(folder_paths, max_age_minutes=30, check_interval_seconds=300):
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
