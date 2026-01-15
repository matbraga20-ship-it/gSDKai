<?php
// Files management view
?>
<div class="bg-white p-6 rounded shadow">
    <h2 class="text-xl font-semibold mb-4">Files</h2>
    <p class="text-sm text-gray-600 mb-4">Upload and list files stored with OpenAI.</p>

    <div class="mb-4">
        <input type="file" id="file-input" />
        <select id="file-purpose" class="ml-2 border rounded px-2 py-1">
            <option value="fine-tune">fine-tune</option>
            <option value="answers">answers</option>
            <option value="search">search</option>
        </select>
        <button id="btn-upload" class="ml-2 px-4 py-2 bg-green-600 text-white rounded">Upload</button>
    </div>

    <div class="mb-4">
        <button id="btn-list-files" class="px-4 py-2 bg-blue-600 text-white rounded">List Files</button>
    </div>

    <pre id="files-output" class="bg-gray-100 p-4 rounded text-sm overflow-auto" style="max-height:400px"></pre>
</div>

<script>
    document.getElementById('btn-list-files').addEventListener('click', async () => {
        const out = document.getElementById('files-output');
        out.textContent = 'Loading...';
        const res = await apiCall('/files', 'GET');
        out.textContent = JSON.stringify(res, null, 2);
    });

    document.getElementById('btn-upload').addEventListener('click', async () => {
        const fileEl = document.getElementById('file-input');
        const purpose = document.getElementById('file-purpose').value;
        const out = document.getElementById('files-output');

        if (!fileEl.files || fileEl.files.length === 0) {
            alert('Select a file to upload');
            return;
        }

        // Client-side validation for fine-tune files
        const fileName = fileEl.files[0].name || '';
        if ((purpose === 'fine-tune' || purpose === 'fine_tune') && !fileName.toLowerCase().endsWith('.jsonl')) {
            alert('Fine-tune uploads must be .jsonl files. Please select a .jsonl file.');
            return;
        }

        const fd = new FormData();
        fd.append('file', fileEl.files[0]);
        fd.append('purpose', purpose);

        out.textContent = 'Uploading...';

        try {
            const response = await fetch('/api/files/upload', {
                method: 'POST',
                body: fd
            });
            const data = await response.json();
            out.textContent = JSON.stringify(data, null, 2);
        } catch (err) {
            out.textContent = 'Upload error: ' + err.message;
        }
    });
</script>
