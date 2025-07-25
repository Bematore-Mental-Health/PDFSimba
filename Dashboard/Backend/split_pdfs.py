import os 
from PyPDF2 import PdfReader, PdfWriter
import uuid

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
UPLOAD_FOLDER = os.path.abspath(os.path.join(BASE_DIR, '..', 'uploads'))
OUTPUT_FOLDER = os.path.abspath(os.path.join(BASE_DIR, '..', 'converted'))

def split_pdf_logic(file_path, options, output_folder):
    reader = PdfReader(file_path)
    total_pages = len(reader.pages)
    if not os.path.exists(output_folder):
        os.makedirs(output_folder)

    output_files = []

    def unique_file_name(base_name):
        return f"{uuid.uuid4()}_{base_name}"

    if options['splitAfter']:
        splitAfter = options['splitAfter']
        for i in range(0, total_pages, splitAfter):
            writer = PdfWriter()
            for page in range(i, min(i + splitAfter, total_pages)):
                writer.add_page(reader.pages[page])
            output_file = os.path.join(output_folder, unique_file_name(f"split_{i // splitAfter + 1}.pdf"))
            with open(output_file, "wb") as f:
                writer.write(f)
            output_files.append(output_file)

    elif options['rangeStart'] and options['rangeEnd']:
        writer = PdfWriter()
        for page in range(options['rangeStart'] - 1, options['rangeEnd']):
            writer.add_page(reader.pages[page])
        output_file = os.path.join(output_folder, unique_file_name("range_split.pdf"))
        with open(output_file, "wb") as f:
            writer.write(f)
        output_files.append(output_file)

    elif options['customPages']:
        writer = PdfWriter()
        for custom_range in options['customPages']:
            if isinstance(custom_range, list):
                for page in range(custom_range[0] - 1, custom_range[1]):
                    writer.add_page(reader.pages[page])
            else:
                writer.add_page(reader.pages[custom_range - 1])
        output_file = os.path.join(output_folder, unique_file_name("custom_pages.pdf"))
        with open(output_file, "wb") as f:
            writer.write(f)
        output_files.append(output_file)

    elif options['oddPages']:
        writer = PdfWriter()
        for page in range(0, total_pages, 2):
            writer.add_page(reader.pages[page])
        output_file = os.path.join(output_folder, unique_file_name("odd_pages.pdf"))
        with open(output_file, "wb") as f:
            writer.write(f)
        output_files.append(output_file)

    elif options['evenPages']:
        writer = PdfWriter()
        for page in range(1, total_pages, 2):
            writer.add_page(reader.pages[page])
        output_file = os.path.join(output_folder, unique_file_name("even_pages.pdf"))
        with open(output_file, "wb") as f:
            writer.write(f)
        output_files.append(output_file)

    return output_files