import subprocess
import os

def convert_iwork_to_pdf(input_path):
    # LibreOffice should be installed and in system PATH
    output_dir = os.path.dirname(input_path)
    cmd = [
        "libreoffice",
        "--headless",
        "--convert-to",
        "pdf",
        "--outdir",
        output_dir,
        input_path
    ]

    result = subprocess.run(cmd, stdout=subprocess.PIPE, stderr=subprocess.PIPE)
    if result.returncode != 0:
        raise RuntimeError(f"Conversion failed: {result.stderr.decode()}")

    base_name = os.path.splitext(os.path.basename(input_path))[0]
    output_path = os.path.join(output_dir, f"{base_name}.pdf")

    if not os.path.exists(output_path):
        raise FileNotFoundError("PDF file not created.")

    return output_path
