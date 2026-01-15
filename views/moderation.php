<?php
// Moderation view
?>
<div class="bg-white p-6 rounded shadow">
    <h2 class="text-xl font-semibold mb-4">Moderation</h2>
    <p class="text-sm text-gray-600 mb-4">Check text against OpenAI moderation policies.</p>

    <div class="mb-4">
        <textarea id="moderation-input" rows="6" class="w-full border rounded p-2" placeholder="Enter text to moderate..."></textarea>
    </div>

    <div class="mb-4">
        <button id="btn-moderate" class="px-4 py-2 bg-red-600 text-white rounded">Moderate</button>
    </div>

    <pre id="moderation-output" class="bg-gray-100 p-4 rounded text-sm overflow-auto" style="max-height:400px"></pre>
</div>

<script>
    document.getElementById('btn-moderate').addEventListener('click', async () => {
        const out = document.getElementById('moderation-output');
        const text = document.getElementById('moderation-input').value.trim();
        if (!text) { alert('Provide text to moderate'); return; }

        out.textContent = 'Checking...';
        const res = await apiCall('/moderation', 'POST', { input: text });
        out.textContent = JSON.stringify(res, null, 2);
    });
</script>
