import os
import uuid
import subprocess

def convert_word_file(file, upload_folder, output_folder):
    """Convert Word document to PDF using LibreOffice"""
    if not file:
        return {'error': 'No file uploaded'}, 400

    ext = os.path.splitext(file.filename)[1].lower()
    if ext not in ['.doc', '.docx', '.odt', '.rtf']:
        return {'error': 'Unsupported file format'}, 400

    # Generate unique filenames
    filename = f"{uuid.uuid4()}{ext}"
    filepath = os.path.join(upload_folder, filename)
    pdf_filename = f"{os.path.splitext(filename)[0]}.pdf"
    pdf_filepath = os.path.join(output_folder, pdf_filename)

    # Save the uploaded file
    file.save(filepath)

    # Windows-specific LibreOffice path
    libreoffice_path = r'C:\Program Files\LibreOffice\program\soffice.exe'
    if not os.path.exists(libreoffice_path):
        return {'error': 'LibreOffice not found at default location'}, 500

    # Convert using LibreOffice
    try:
        result = subprocess.run(
            [libreoffice_path, '--headless', '--convert-to', 'pdf', '--outdir', output_folder, filepath],
            check=True,
            stdout=subprocess.PIPE,
            stderr=subprocess.PIPE,
            timeout=30
        )
        
        # Verify conversion was successful
        if not os.path.exists(pdf_filepath):
            error_msg = result.stderr.decode('utf-8', errors='ignore') if result.stderr else 'Unknown error'
            return {'error': f'Conversion failed: {error_msg}'}, 500

        return {
            'pdf_path': f"converted/{pdf_filename}",
            'original_filename': file.filename,
            'converted_filename': pdf_filename
        }

    except subprocess.TimeoutExpired:
        return {'error': 'Conversion timed out'}, 500
    except subprocess.CalledProcessError as e:
        error_msg = e.stderr.decode('utf-8', errors='ignore') if e.stderr else str(e)
        return {'error': f"Conversion failed: {error_msg}"}, 500
    except Exception as e:
        return {'error': str(e)}, 500