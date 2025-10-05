<?php
header('Content-Type: application/json; charset=utf-8');

// ===============================
// Recebe a URL do GET
// ===============================
$url = $_GET['url'] ?? '';
$OPENAI_API_KEY = "";

if (!$url) {
    echo json_encode(["preview" => "Invalid URL"]);
    exit;
}

// ===============================
// Prompt para a OpenAI
// ===============================
$prompt = "Summarize the content of the site: $url in at least 200 and at most 300 words. Make sure no content is omitted, and keep it clear and objective.";

$data = [
    "model" => "gpt-4o",
    "messages" => [
        ["role" => "system", "content" => "You are an assistant that creates short, clear, and objective summaries."],
        ["role" => "user", "content" => $prompt]
    ],
    "max_tokens" => 2000
];

// ===============================
// Requisição cURL para a API
// ===============================
$ch = curl_init("https://api.openai.com/v1/chat/completions");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer {$OPENAI_API_KEY}"
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);

if(curl_errno($ch)) {
    echo json_encode(["preview" => "Connection error: ".curl_error($ch)]);
    curl_close($ch);
    exit;
}

curl_close($ch);

$res = json_decode($response, true);
$preview = $res['choices'][0]['message']['content'] ?? "Preview unavailable.";

echo json_encode(["preview" => $preview]);
?>
