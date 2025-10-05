<?php
header('Content-Type: application/json; charset=utf-8');

$url = $_GET['url'] ?? '';
$OPENAI_API_KEY = "api_aqui_nao_tem_ela";

if (!$url) {
    echo json_encode(["preview" => "URL inválida"]);
    exit;
}

$prompt = "Resuma o conteúdo do site: $url em no minim0 150 e no maximo 300 caracteres de forma clara e objetiva.";

$data = [
    "model" => "gpt-4o-mini",
    "messages" => [
        ["role"=>"system","content"=>"Você é um assistente que cria resumos curtos, claros e objetivos."],
        ["role"=>"user","content"=>$prompt]
    ],
    "max_tokens"=>200
];

$ch = curl_init("https://api.openai.com/v1/chat/completions");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer {$OPENAI_API_KEY}"
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
curl_close($ch);

$res = json_decode($response,true);
$preview = $res['choices'][0]['message']['content'] ?? "Preview indisponível.";

echo json_encode(["preview" => $preview]);
?>
