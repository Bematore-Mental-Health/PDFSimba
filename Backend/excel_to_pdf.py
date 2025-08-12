import os
import uuid
import subprocess
from flask import jsonify

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
UPLOAD_FOLDER = os.path.abspath(os.path.join(BASE_DIR, '..', 'uploads'))
OUTPUT_FOLDER = os.path.abspath(os.path.join(BASE_DIR, '..', 'converted'))
SOFFICE_PATH = r"C:\Program Files\LibreOffice\program\soffice.com"

def convert_excel_file(file):
    if not file:
        return jsonify({'error': 'No file uploaded'}), 400

    ext = os.path.splitext(file.filename)[1].lower()
    if ext not in ['.xls', '.xlsx']:
        return jsonify({'error': 'Unsupported Excel format'}), 400

    # Create unique filenames
    filename = f"{uuid.uuid4()}{ext}"
    filepath = os.path.join(UPLOAD_FOLDER, filename)
    pdf_filename = f"{os.path.splitext(filename)[0]}.pdf"
    pdf_filepath = os.path.join(OUTPUT_FOLDER, pdf_filename)

    try:
        # Save the uploaded file
        file.save(filepath)

        # Run LibreOffice conversion
        cmd = [
            SOFFICE_PATH,
            "--headless",
            "--convert-to", "pdf",
            "--outdir", OUTPUT_FOLDER,
            filepath
        ]
        
        result = subprocess.run(
            cmd,
            capture_output=True,
            text=True,
            timeout=30
        )

        if result.returncode != 0:
            return jsonify({
                'error': 'Conversion failed',
                'details': result.stderr or result.stdout
            }), 500

        # Verify PDF was created
        if not os.path.exists(pdf_filepath):
            # Check for alternative naming (LibreOffice sometimes adds extra extensions)
            alt_pdf = os.path.join(OUTPUT_FOLDER, f"{filename}.pdf")
            if os.path.exists(alt_pdf):
                os.rename(alt_pdf, pdf_filepath)
            else:
                return jsonify({
                    'error': 'PDF not generated',
                    'details': 'LibreOffice completed but no PDF found'
                }), 500

        return jsonify({
            'pdf_path': f"converted/{pdf_filename}",
            'message': 'Conversion successful'
        })

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