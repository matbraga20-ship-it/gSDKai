<?php
require __DIR__ . '/../bootstrap.php';
$service = new OpenAI\Services\TextService();
try {
    $res = $service->generateTitle('Este é um teste rápido para gerar título');
    var_dump($res->toArray());
} catch (Throwable $e) {
    echo "Exception: " . $e->getMessage() . PHP_EOL;
    echo $e->getTraceAsString() . PHP_EOL;
}
