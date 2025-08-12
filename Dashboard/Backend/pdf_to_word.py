import os
import uuid
from flask import jsonify
from PyPDF2 import PdfReader
from html2docx import html2docx
from html2docx import html2docx
from io import BytesIO

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
UPLOAD_FOLDER = os.path.abspath(os.path.join(BASE_DIR, '..', 'uploads'))
OUTPUT_FOLDER = os.path.abspath(os.path.join(BASE_DIR, '..', 'converted'))

def convert_pdf_file(file):
    if not file:
        return jsonify({'error': 'No file uploaded'}), 400

    ext = os.path.splitext(file.filename)[1].lower()
    if ext != '.pdf':
        return jsonify({'error': 'Only PDF files are supported'}), 400

    filename = f"{uuid.uuid4()}.pdf"
    input_path = os.path.join(UPLOAD_FOLDER, filename)
    file.save(input_path)

    base_name = os.path.splitext(filename)[0]
    html_filename = f"{base_name}.html"
    docx_filename = f"{base_name}.docx"
    html_path = os.path.join(OUTPUT_FOLDER, html_filename)
    docx_path = os.path.join(OUTPUT_FOLDER, docx_filename)

    try:
        reader = PdfReader(input_path)
        text = ''
        for page in reader.pages:
            text += page.extract_text() or ""

        html_content = f"<html><body>{text.replace('\n', '<br>')}</body></html>"

        # Save HTML for preview
        with open(html_path, 'w', encoding='utf-8') as f:
            f.write(html_content)

        # Convert to DOCX using html2docx
        buffer = html2docx(html_content, title="Converted PDF")
        with open(docx_path, "wb") as f:
            f.write(buffer.getvalue())

        return jsonify({
            'word_path': f"converted/{html_filename}",
            'word_docx_path': f"converted/{docx_filename}",
            'word_filename': html_filename
        })

    except Exception as e:
        return jsonify({'error': f'PDF conversion failed: {str(e)}'}), 500