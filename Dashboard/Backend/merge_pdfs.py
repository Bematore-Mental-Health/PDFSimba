import os
from PyPDF2 import PdfMerger
import io
import uuid


UPLOAD_FOLDER = './uploads'
OUTPUT_FOLDER = './converted'


def merge_pdfs(files, upload_folder, output_folder):
    uploaded_files = []

    for file in files:
        filename = f"{uuid.uuid4()}_{file.filename}"
        file_path = os.path.join(upload_folder, filename)
        file.save(file_path)
        uploaded_files.append(file_path)

    output_filename = f"{uuid.uuid4()}.pdf"
    output_path = os.path.join(output_folder, output_filename)

    merger = PdfMerger()
    try:
        for file_path in uploaded_files:
            merger.append(file_path)
        merger.write(output_path)
    finally:
        merger.close()
        for file_path in uploaded_files:
            os.remove(file_path)

    return output_path