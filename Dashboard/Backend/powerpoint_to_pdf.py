import os
import uuid
import comtypes.client
import comtypes
from flask import jsonify

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
UPLOAD_FOLDER = os.path.abspath(os.path.join(BASE_DIR, '..', 'uploads'))
OUTPUT_FOLDER = os.path.abspath(os.path.join(BASE_DIR, '..', 'converted'))

def convert_ppt_file(file):
    if not file:
        return jsonify({'error': 'No file uploaded'}), 400

    ext = os.path.splitext(file.filename)[1].lower()
    if ext not in ['.ppt', '.pptx']:
        return jsonify({'error': 'Unsupported PowerPoint format'}), 400

    filename = f"{uuid.uuid4()}{ext}"
    filepath = os.path.join(UPLOAD_FOLDER, filename)
    file.save(filepath)

    pdf_filename = f"{os.path.splitext(filename)[0]}.pdf"
    pdf_filepath = os.path.join(OUTPUT_FOLDER, pdf_filename)

    try:
        comtypes.CoInitialize()
        powerpoint = comtypes.client.CreateObject("PowerPoint.Application")
        powerpoint.Visible = 1
        presentation = powerpoint.Presentations.Open(filepath, WithWindow=False)
        presentation.SaveAs(pdf_filepath, 32)  # 32 = PDF
        presentation.Close()
        powerpoint.Quit()
    finally:
        comtypes.CoUninitialize()

    return jsonify({'pdf_path': f"converted/{pdf_filename}"})
