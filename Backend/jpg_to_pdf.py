import os
import uuid
from fpdf import FPDF
from flask import jsonify, request
from PIL import Image

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
UPLOAD_FOLDER = os.path.abspath(os.path.join(BASE_DIR, '..', 'uploads'))
OUTPUT_FOLDER = os.path.abspath(os.path.join(BASE_DIR, '..', 'converted'))

def convert_jpg_file():
    files = request.files.getlist('jpg_file')
    if not files or len(files) == 0:
        return jsonify({'error': 'No files uploaded'}), 400

    images = []
    filenames = []

    for file in files:
        ext = os.path.splitext(file.filename)[1].lower()
        if ext not in ['.jpg', '.jpeg', '.png']:
            return jsonify({'error': f'Unsupported file: {file.filename}'}), 400

        filename = f"{uuid.uuid4()}{ext}"
        filepath = os.path.join(UPLOAD_FOLDER, filename)
        file.save(filepath)
        filenames.append(filepath)

        img = Image.open(filepath).convert("RGB")
        images.append(img)

    if not images:
        return jsonify({'error': 'No valid images found'}), 400

    pdf_filename = f"{uuid.uuid4()}.pdf"
    pdf_filepath = os.path.join(OUTPUT_FOLDER, pdf_filename)

    # Save all images into one PDF
    images[0].save(pdf_filepath, save_all=True, append_images=images[1:], format='PDF')

    return jsonify({'pdf_path': f"converted/{pdf_filename}"})
