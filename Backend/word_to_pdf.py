import os
import uuid
import comtypes.client
import comtypes
from flask import jsonify


BASE_DIR = os.path.dirname(os.path.abspath(__file__))
UPLOAD_FOLDER = os.path.abspath(os.path.join(BASE_DIR, '..', 'uploads'))
OUTPUT_FOLDER = os.path.abspath(os.path.join(BASE_DIR, '..', 'converted'))

def convert_word_file(file):
    if not file:
        return jsonify({'error': 'No file uploaded'}), 400

    ext = os.path.splitext(file.filename)[1].lower()
    if ext not in ['.doc', '.docx']:
        return jsonify({'error': 'Unsupported file format'}), 400

    filename = f"{uuid.uuid4()}{ext}"
    filepath = os.path.join(UPLOAD_FOLDER, filename)
    file.save(filepath)

    pdf_filename = f"{os.path.splitext(filename)[0]}.pdf"
    pdf_filepath = os.path.join(OUTPUT_FOLDER, pdf_filename)

    try:
        comtypes.CoInitialize()
        word = comtypes.client.CreateObject('Word.Application')
        word.Visible = False
        doc = word.Documents.Open(filepath)
        doc.SaveAs(pdf_filepath, FileFormat=17)
        doc.Close()
        word.Quit()
    finally:
        comtypes.CoUninitialize()

    return jsonify({'pdf_path': f"converted/{pdf_filename}"})
