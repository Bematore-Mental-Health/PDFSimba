import os
import uuid
import comtypes.client
import comtypes
from flask import jsonify

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
UPLOAD_FOLDER = os.path.abspath(os.path.join(BASE_DIR, '..', 'uploads'))
OUTPUT_FOLDER = os.path.abspath(os.path.join(BASE_DIR, '..', 'converted'))

def convert_excel_file(file):
    if not file:
        return jsonify({'error': 'No file uploaded'}), 400

    ext = os.path.splitext(file.filename)[1].lower()
    if ext not in ['.xls', '.xlsx']:
        return jsonify({'error': 'Unsupported Excel format'}), 400

    filename = f"{uuid.uuid4()}{ext}"
    filepath = os.path.join(UPLOAD_FOLDER, filename)
    file.save(filepath)

    pdf_filename = f"{os.path.splitext(filename)[0]}.pdf"
    pdf_filepath = os.path.join(OUTPUT_FOLDER, pdf_filename)

    try:
        comtypes.CoInitialize()
        excel = comtypes.client.CreateObject('Excel.Application')
        excel.Visible = False
        workbook = excel.Workbooks.Open(filepath)
        workbook.ExportAsFixedFormat(0, pdf_filepath)
        workbook.Close(False)
        excel.Quit()
    finally:
        comtypes.CoUninitialize()

    return jsonify({'pdf_path': f"converted/{pdf_filename}"})
def convert_excel_file(file):
    if not file:
        return jsonify({'error': 'No file uploaded'}), 400

    ext = os.path.splitext(file.filename)[1].lower()
    if ext not in ['.xls', '.xlsx']:
        return jsonify({'error': 'Unsupported Excel format'}), 400

    filename = f"{uuid.uuid4()}{ext}"
    filepath = os.path.join(UPLOAD_FOLDER, filename)
    file.save(filepath)

    pdf_filename = f"{os.path.splitext(filename)[0]}.pdf"
    pdf_filepath = os.path.join(OUTPUT_FOLDER, pdf_filename)

    try:
        comtypes.CoInitialize()
        excel = comtypes.client.CreateObject('Excel.Application')
        excel.Visible = False
        workbook = excel.Workbooks.Open(filepath)

        # Ensure each sheet is properly formatted before export
        for sheet in workbook.Sheets:
            sheet.PageSetup.Orientation = 2  # 2 = Landscape, 1 = Portrait
            sheet.PageSetup.Zoom = False
            sheet.PageSetup.FitToPagesWide = 1
            sheet.PageSetup.FitToPagesTall = False  # Allows vertical scrolling
            sheet.PageSetup.CenterHorizontally = True
            sheet.PageSetup.CenterVertically = True

        # Export to PDF
        workbook.ExportAsFixedFormat(0, pdf_filepath)

        workbook.Close(False)
        excel.Quit()
    finally:
        comtypes.CoUninitialize()

    return jsonify({'pdf_path': f"converted/{pdf_filename}"})
