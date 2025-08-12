<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Signature Pad</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        #canvas {
            border: 1px solid #000;
            background-color: #f8f8f8;
            touch-action: none;
        }
        .button-group {
            margin-top: 10px;
        }
        button {
            padding: 8px 15px;
            margin-right: 10px;
            cursor: pointer;
        }
        #typedPreview {
            font-family: 'Brush Script MT', cursive;
            font-size: 24px;
            min-height: 50px;
            border: 1px dashed #ccc;
            padding: 10px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h1>Signature Test</h1>
    
    <div id="draw-section">
        <h2>Draw Your Signature</h2>
        <canvas id="canvas" width="600" height="200"></canvas>
        <div class="button-group">
            <button id="clear">Clear</button>
            <button id="save">Save Signature</button>
        </div>
    </div>
    
    <div id="type-section" style="margin-top: 30px;">
        <h2>Type Your Signature</h2>
        <input type="text" id="typedSignature" placeholder="Type your name" style="width: 100%; padding: 8px;">
        <div id="typedPreview">Preview will appear here</div>
        <div class="button-group">
            <button id="saveTyped">Save Typed Signature</button>
        </div>
    </div>
    
    <div id="result" style="margin-top: 30px; border-top: 1px solid #ccc; padding-top: 20px;">
        <h2>Your Signature:</h2>
        <div id="signatureResult"></div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Canvas drawing functionality
            const canvas = document.getElementById('canvas');
            const ctx = canvas.getContext('2d');
            let isDrawing = false;
            let lastX = 0;
            let lastY = 0;
            
            // Set canvas background to white
            ctx.fillStyle = 'white';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.fillStyle = 'black';
            
            // Mouse/touch events
            canvas.addEventListener('mousedown', startDrawing);
            canvas.addEventListener('mousemove', draw);
            canvas.addEventListener('mouseup', stopDrawing);
            canvas.addEventListener('mouseout', stopDrawing);
            
            // Touch support
            canvas.addEventListener('touchstart', handleTouch);
            canvas.addEventListener('touchmove', handleTouch);
            canvas.addEventListener('touchend', stopDrawing);
            
            function handleTouch(e) {
                e.preventDefault();
                const touch = e.touches[0];
                const mouseEvent = new MouseEvent(
                    e.type === 'touchstart' ? 'mousedown' : 'mousemove', {
                    clientX: touch.clientX,
                    clientY: touch.clientY
                });
                canvas.dispatchEvent(mouseEvent);
            }
            
            function startDrawing(e) {
                isDrawing = true;
                [lastX, lastY] = [e.offsetX, e.offsetY];
            }
            
            function draw(e) {
                if (!isDrawing) return;
                
                ctx.beginPath();
                ctx.moveTo(lastX, lastY);
                ctx.lineTo(e.offsetX, e.offsetY);
                ctx.strokeStyle = 'black';
                ctx.lineWidth = 2;
                ctx.lineCap = 'round';
                ctx.stroke();
                
                [lastX, lastY] = [e.offsetX, e.offsetY];
            }
            
            function stopDrawing() {
                isDrawing = false;
            }
            
            // Clear button
            document.getElementById('clear').addEventListener('click', function() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                ctx.fillStyle = 'white';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.fillStyle = 'black';
            });
            
            // Save drawn signature
            document.getElementById('save').addEventListener('click', function() {
                const dataURL = canvas.toDataURL();
                displayResult(dataURL);
            });
            
            // Typed signature
            document.getElementById('typedSignature').addEventListener('input', function() {
                document.getElementById('typedPreview').textContent = this.value || "Preview will appear here";
            });
            
            // Save typed signature
            document.getElementById('saveTyped').addEventListener('click', function() {
                const text = document.getElementById('typedSignature').value.trim();
                if (!text) {
                    alert('Please type your signature first');
                    return;
                }
                
                const tempCanvas = document.createElement('canvas');
                tempCanvas.width = 300;
                tempCanvas.height = 100;
                const tempCtx = tempCanvas.getContext('2d');
                
                tempCtx.font = '36px "Brush Script MT", cursive';
                tempCtx.fillText(text, 10, 50);
                
                displayResult(tempCanvas.toDataURL());
            });
            
            // Display result
            function displayResult(dataURL) {
                const resultDiv = document.getElementById('signatureResult');
                resultDiv.innerHTML = `
                    <img src="${dataURL}" style="max-width: 300px; border: 1px solid #ccc; margin-top: 10px;">
                    <div style="margin-top: 10px;">
                        <a href="${dataURL}" download="signature.png" style="padding: 8px 15px; background: #4CAF50; color: white; text-decoration: none;">Download Signature</a>
                    </div>
                `;
            }
        });
    </script>
</body>
</html>