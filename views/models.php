<?php
// Models management view
?>
<div class="bg-white p-6 rounded shadow">
    <h2 class="text-xl font-semibold mb-4">Models</h2>
    <p class="text-sm text-gray-600 mb-4">List available OpenAI models.</p>

    <div class="mb-4">
        <button id="btn-fetch-models" class="px-4 py-2 bg-blue-600 text-white rounded">Fetch Models</button>
    </div>

    <pre id="models-output" class="bg-gray-100 p-4 rounded text-sm overflow-auto" style="max-height:400px"></pre>
</div>

<script>
    document.getElementById('btn-fetch-models').addEventListener('click', async () => {
        const out = document.getElementById('models-output');
        out.textContent = 'Loading...';
        const res = await apiCall('/models', 'GET');
        out.textContent = JSON.stringify(res, null, 2);
    });
</script>
