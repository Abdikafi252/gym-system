<!-- Webcam Capture Modal -->
<div id="webcamModal" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true" style="border-radius: 15px; overflow: hidden;">
    <div class="modal-header" style="background: #1e293b; color: white;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color: white; opacity: 0.8;">×</button>
        <h3 style="font-weight: 800; font-size: 18px;"><i class="fas fa-camera"></i> Capture Member Photo</h3>
    </div>
    <div class="modal-body" style="text-align: center; background: #f8fafc; padding: 20px;">
        <div id="webcam-container" style="border-radius: 15px; overflow: hidden; border: 4px solid #e2e8f0; background: #000; position: relative; margin-bottom: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
            <video id="webcam-video" width="100%" height="auto" autoplay muted style="transform: scaleX(-1); display: block;"></video>
            <!-- Face Focus Overlay -->
            <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; border: 2px solid rgba(59, 130, 246, 0.3); border-radius: 12px;">
                <div style="position: absolute; top: 20%; left: 20%; width: 60%; height: 60%; border: 2px dashed rgba(59, 130, 246, 0.5); border-radius: 50%;"></div>
            </div>
        </div>
        <p style="color: #64748b; font-size: 13px;">Member-ka ha istaago goobta loo calaamadeeyey (Circle-ka).</p>
    </div>
    <div class="modal-footer" style="background: #f1f5f9; text-align: center;">
        <button class="btn btn-warning" id="capture-btn" style="border-radius: 999px; padding: 10px 25px; font-weight: 700;">
            <i class="fas fa-camera"></i> SNAP PHOTO
        </button>
        <button class="btn" data-dismiss="modal" id="cancel-webcam" style="border-radius: 999px; padding: 10px 25px;">Cancel</button>
    </div>
</div>

<script>
let webcamStream = null;

function startWebcam() {
    const video = document.getElementById('webcam-video');
    
    // Clear any previous stream
    if (webcamStream) {
        webcamStream.getTracks().forEach(track => track.stop());
    }

    navigator.mediaDevices.getUserMedia({ video: true })
    .then(stream => {
        webcamStream = stream;
        video.srcObject = stream;
        $('#webcamModal').modal('show');
    })
    .catch(err => {
        alert("Camera access denied or not found. Please check your browser settings.");
        console.error("Webcam error:", err);
    });
}

document.getElementById('capture-btn').addEventListener('click', function() {
    const video = document.getElementById('webcam-video');
    const canvas = document.createElement('canvas');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    const ctx = canvas.getContext('2d');
    
    // Flip horizontal for the capture to match preview
    ctx.translate(canvas.width, 0);
    ctx.scale(-1, 1);
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    
    const dataURL = canvas.toDataURL('image/jpeg', 0.9);
    
    // Update Hidden Input (must exist in the main form)
    const hiddenInput = document.getElementById('webcam_image');
    if (hiddenInput) {
        hiddenInput.value = dataURL;
    }
    
    // Update Preview (the main file upload label or img)
    const preview = document.querySelector('.file-upload-label');
    if (preview) {
        preview.style.backgroundContent = 'url("' + dataURL + '")'; // Simplified hack
        preview.innerHTML = '<img src="' + dataURL + '" style="width:100%; height:100%; border-radius:12px; object-fit:cover;">';
    }

    // Stop and Close
    stopWebcam();
    $('#webcamModal').modal('hide');
    alert("Photo captured successfully!");
});

$('#webcamModal').on('hidden.bs.modal', function () {
    stopWebcam();
});

function stopWebcam() {
    if (webcamStream) {
        webcamStream.getTracks().forEach(track => track.stop());
        webcamStream = null;
    }
}
</script>
