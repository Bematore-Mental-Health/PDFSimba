import os
import uuid
import pandas as pd
from flask import jsonify
from PyPDF2 import PdfReader
from PyPDF2 import PdfReader
import pandas as pd
from pdf2image import convert_from_path
from PIL import Image
import pytesseract
import ocrmypdf
import pdfplumber
from flask import jsonify, request




BASE_DIR = os.path.dirname(os.path.abspath(__file__))
UPLOAD_FOLDER = os.path.abspath(os.path.join(BASE_DIR, '..', 'uploads'))
OUTPUT_FOLDER = os.path.abspath(os.path.join(BASE_DIR, '..', 'converted'))

def convert_pdf_to_excel():
    file = request.files.get('pdf_file')
    if not file:
        return jsonify({'error': 'No file uploaded'}), 400

    filename = f"{uuid.uuid4()}.pdf"
    input_path = os.path.join(UPLOAD_FOLDER, filename)
    file.save(input_path)

    # OCR output file
    ocr_filename = f"ocr_{filename}"
    ocr_path = os.path.join(UPLOAD_FOLDER, ocr_filename)

    # Output Excel
    output_filename = f"{os.path.splitext(filename)[0]}.xlsx"
    output_path = os.path.join(OUTPUT_FOLDER, output_filename)

    try:
        # Step 1: OCR the PDF (if it's not already text-searchable)
        ocrmypdf.ocr(input_path, ocr_path, force_ocr=True, output_type='pdf')

        # Step 2: Extract tables using pdfplumber
        all_data = []

        with pdfplumber.open(ocr_path) as pdf:
            for page in pdf.pages:
                tables = page.extract_tables()
                for table in tables:
                    if table:
                        all_data.extend(table)

        if not all_data:
            return jsonify({'error': 'No tables found in PDF'}), 400

        df = pd.DataFrame(all_data)
        df.to_excel(output_path, index=False, header=False)

        return jsonify({
            'excel_filename': output_filename
        })

    except Exception as e:
        return jsonify({'error': f'PDF to Excel conversion failed: {str(e)}'}), 500