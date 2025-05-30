import os
import uuid
import subprocess
from flask import jsonify

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
UPLOAD_FOLDER = os.path.abspath(os.path.join(BASE_DIR, '..', 'uploads'))
OUTPUT_FOLDER = os.path.abspath(os.path.join(BASE_DIR, '..', 'converted'))

def convert_openoffice_file(file):
    if not file:
        return jsonify({'error': 'No file uploaded'}), 400

    ext = os.path.splitext(file.filename)[1].lower()
    if ext not in ['.odt', '.ods', '.odp']:
        return jsonify({'error': 'Unsupported OpenOffice format'}), 400

    # Save the uploaded file
    filename = f"{uuid.uuid4()}{ext}"
    input_path = os.path.join(UPLOAD_FOLDER, filename)
    file.save(input_path)

    try:
        # Run LibreOffice headless conversion
        result = subprocess.run([
            'soffice',
            '--headless',
            '--convert-to', 'pdf',
            '--outdir', OUTPUT_FOLDER,
            input_path
        ], stdout=subprocess.PIPE, stderr=subprocess.PIPE, check=True)

        # Build output path
        output_filename = f"{os.path.splitext(filename)[0]}.pdf"
        output_path = os.path.join(OUTPUT_FOLDER, output_filename)

        if os.path.exists(output_path):
            return jsonify({'pdf_path': f"converted/{output_filename}"})
        else:
            return jsonify({'error': 'Conversion failed. PDF file not found.'}), 500

    except subprocess.CalledProcessError as e:
        return jsonify({
            'error': 'LibreOffice conversion failed.',
            'details': e.stderr.decode()
        }), 500
    except Exception as e:
        return jsonify({'error': f'Unexpected error: {str(e)}'}), 500
