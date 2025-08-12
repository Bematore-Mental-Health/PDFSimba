import fitz  # PyMuPDF
import subprocess
import os

def convert_to_pdfa(input_path, output_path):
    """
    Convert a PDF file to PDF/A format using PyMuPDF for preprocessing and Ghostscript for compliance.
    """
    try:
        # Step 1: Preprocessing with PyMuPDF
        intermediate_path = input_path.replace(".pdf", "_cleaned.pdf")
        try:
            doc = fitz.open(input_path)

            # Save the PDF to optimize structure
            doc.save(intermediate_path, garbage=4, deflate=True, clean=True)
            doc.close()
        except Exception as e:
            print(f"PyMuPDF preprocessing error: {e}")
            return False

        # Step 2: Conversion to PDF/A using Ghostscript
        gs_command = [
            "gswin64c",  # Use "gs" on Linux/MacOS or "gswin64c" on Windows
            "-dPDFA=2",
            "-dBATCH",
            "-dNOPAUSE",
            "-dNOOUTERSAVE",
            "-dUseCIEColor",  # Use CIE-based color spaces for compatibility
            "-sColorConversionStrategy=UseDeviceIndependentColor",
            "-dPDFSETTINGS=/prepress",
            "-dEmbedAllFonts=true",
            "-dSubsetFonts=true",
            "-dCompressFonts=true",
            "-sDEVICE=pdfwrite",
            "-sProcessColorModel=DeviceRGB",
            f"-sOutputFile={output_path}",
            intermediate_path,
        ]

        result = subprocess.run(gs_command, capture_output=True, text=True)
        if result.returncode != 0:
            print(f"Ghostscript Error: {result.stderr}")
            return False

        # Cleanup intermediate file
        os.remove(intermediate_path)

        return True

    except Exception as e:
        print(f"Conversion error: {e}")
        return False
