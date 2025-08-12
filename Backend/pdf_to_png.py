import os
import uuid
import zipfile
from pdf2image import convert_from_path

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
UPLOAD_FOLDER = os.path.abspath(os.path.join(BASE_DIR, '..', 'uploads'))
OUTPUT_FOLDER = os.path.abspath(os.path.join(BASE_DIR, '..', 'converted'))

os.makedirs(UPLOAD_FOLDER, exist_ok=True)
os.makedirs(OUTPUT_FOLDER, exist_ok=True)

def convert_pdf_to_png(file):
    pdf_filename = f"{uuid.uuid4()}.pdf"
    pdf_path = os.path.join(UPLOAD_FOLDER, pdf_filename)
    file.save(pdf_path)

    image_filenames = []
    try:
        images = convert_from_path(pdf_path)
        for i, image in enumerate(images):
            image_filename = f"{uuid.uuid4()}.png"
            image_path = os.path.join(OUTPUT_FOLDER, image_filename)
            image.save(image_path, 'PNG')
            image_filenames.append(image_filename)

        zip_filename = f"{uuid.uuid4()}.zip"
        zip_path = os.path.join(OUTPUT_FOLDER, zip_filename)
        with zipfile.ZipFile(zip_path, 'w') as zipf:
            for image_filename in image_filenames:
                zipf.write(os.path.join(OUTPUT_FOLDER, image_filename), image_filename)

        image_urls = [f"/converted/{filename}" for filename in image_filenames]
        return {'imageUrls': image_urls, 'zipUrl': f"/converted/{zip_filename}"}
    except Exception as e:
        return {'error': str(e)}
    finally:
        if os.path.exists(pdf_path):
            os.remove(pdf_path)
