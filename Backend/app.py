from flask import Flask, request, jsonify, send_from_directory
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
from PyPDF2 import PdfReader
from flask_cors import CORS
from flask import Flask, request, jsonify, send_from_directory
from flask_cors import CORS
from flask import render_template


from flask import Flask, request, jsonify, send_from_directory, send_file, render_template
import os
import uuid
from pdf2image import convert_from_path
from pptx import Presentation
from pptx.util import Inches
import base64
from io import BytesIO



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
