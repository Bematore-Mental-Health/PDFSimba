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

    # Convert using LibreOffice
    try:
        result = subprocess.run(
            ['libreoffice', '--headless', '--convert-to', 'pdf', '--outdir', output_folder, filepath],
            check=True,
            stdout=subprocess.PIPE,
            stderr=subprocess.PIPE,
            timeout=30
        )
        
        # Verify conversion was successful
        if not os.path.exists(pdf_filepath):
            return {'error': 'Conversion failed - no output file generated'}, 500

        return {
            'pdf_path': f"converted/{pdf_filename}",
            'original_filename': file.filename,
            'converted_filename': pdf_filename
        }

    except subprocess.TimeoutExpired:
        return {'error': 'Conversion timed out'}, 500
    except subprocess.CalledProcessError as e:
        return {'error': f"Conversion failed: {e.stderr.decode('utf-8', errors='ignore')}"}, 500
    except Exception as e:
        return {'error': str(e)}, 500