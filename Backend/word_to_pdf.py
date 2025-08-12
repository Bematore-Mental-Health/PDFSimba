import os
import uuid
import subprocess
from flask import jsonify, current_app

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
UPLOAD_FOLDER = os.path.abspath(os.path.join(BASE_DIR, '..', 'uploads'))
OUTPUT_FOLDER = os.path.abspath(os.path.join(BASE_DIR, '..', 'converted'))
SOFFICE_PATH = r"C:\Program Files\LibreOffice\program\soffice.com"

def convert_word_file(file):
    if not file:
        return jsonify({'error': 'No file uploaded'}), 400

    ext = os.path.splitext(file.filename)[1].lower()
    if ext not in ['.doc', '.docx']:
        return jsonify({'error': 'Unsupported file format'}), 400

    # Create unique filename
    filename = f"{uuid.uuid4()}{ext}"
    filepath = os.path.join(UPLOAD_FOLDER, filename)
    file.save(filepath)

    pdf_filename = f"{os.path.splitext(filename)[0]}.pdf"
    pdf_filepath = os.path.join(OUTPUT_FOLDER, pdf_filename)

    try:
        # Run LibreOffice conversion
        result = subprocess.run(
            [
                SOFFICE_PATH,
                "--headless",
                "--convert-to", "pdf",
                "--outdir", OUTPUT_FOLDER,
                filepath
            ],
            capture_output=True,
            text=True,
            timeout=30
        )
        
        if result.returncode != 0:
            return jsonify({
                'error': 'Conversion failed',
                'details': result.stderr or result.stdout
            }), 500
            
        if not os.path.exists(pdf_filepath):
            return jsonify({
                'error': 'PDF was not created',
                'details': 'LibreOffice exited successfully but no PDF was generated'
            }), 500

    except subprocess.TimeoutExpired:
        return jsonify({'error': 'Conversion timed out'}), 500
    except Exception as e:
        return jsonify({
            'error': 'Conversion error',
            'details': str(e)
        }), 500
    finally:
        # Clean up original file
        if os.path.exists(filepath):
            try:
                os.remove(filepath)
            except:
                pass

    return jsonify({'pdf_path': f"converted/{pdf_filename}"})