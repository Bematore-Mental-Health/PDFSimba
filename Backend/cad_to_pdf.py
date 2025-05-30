import os
import uuid
import subprocess
from flask import jsonify

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
UPLOAD_FOLDER = os.path.abspath(os.path.join(BASE_DIR, '..', 'uploads'))
OUTPUT_FOLDER = os.path.abspath(os.path.join(BASE_DIR, '..', 'converted'))

ODA_CONVERTER_PATH = r'"C:\Program Files\ODA\ODAFileConverter\ODAFileConverter.exe"' 

def convert_cad_file(file):
    if not file:
        return jsonify({'error': 'No file uploaded'}), 400

    ext = os.path.splitext(file.filename)[1].lower()
    if ext not in ['.dwg', '.dxf']:
        return jsonify({'error': 'Unsupported AutoCAD format'}), 400

    # Save original file
    filename = f"{uuid.uuid4()}{ext}"
    input_path = os.path.join(UPLOAD_FOLDER, filename)
    file.save(input_path)

    # If it's a DWG file, convert it to DXF
    if ext == '.dwg':
        dxf_path = input_path.replace('.dwg', '.dxf')
        try:
            subprocess.run([
                ODA_CONVERTER_PATH,
                os.path.dirname(input_path),
                os.path.dirname(input_path),
                "ACAD2013", "DXF", "0", "1", "0"
            ], check=True)
            input_path = dxf_path
        except subprocess.CalledProcessError as e:
            return jsonify({'error': 'DWG to DXF conversion failed', 'details': str(e)}), 500

    # Convert DXF to PDF using Inkscape
    pdf_filename = f"{uuid.uuid4()}.pdf"
    pdf_path = os.path.join(OUTPUT_FOLDER, pdf_filename)

    try:
        subprocess.run([
            "inkscape", input_path, f"--export-filename={pdf_path}"
        ], check=True)
    except subprocess.CalledProcessError as e:
        return jsonify({'error': 'DXF to PDF conversion failed using Inkscape', 'details': str(e)}), 500

    return jsonify({'pdf_path': f"converted/{pdf_filename}"})
