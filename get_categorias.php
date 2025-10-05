<?php
header('Content-Type: application/json; charset=utf-8');

$mysqli = new mysqli("localhost", "root", "", "biospace");
if ($mysqli->connect_errno) {
    echo json_encode([]);
    exit;
}

$query = "
  SELECT c.id AS categoria_id, c.nome AS categoria_nome, 
         s.titulo AS sub_titulo, s.url AS sub_url
  FROM categorias c
  LEFT JOIN subitens s ON c.id = s.categoria_id
  ORDER BY c.id, s.id
";

$result = $mysqli->query($query);
$dados = [];

// Sua chave da OpenAI
$OPENAI_API_KEY = "chave_aqui_gordo";

// Função para gerar resumo
function gerarResumo($url, $apiKey) {
    if (empty($url)) return "Sem preview disponível.";

    $prompt = "Resuma o conteúdo do site: $url em até 200 caracteres.";
    $data = [
        "model" => "gpt-4o-mini",
        "messages" => [
            ["role" => "system", "content" => "Você é um assistente que cria resumos curtos e claros."],
            ["role" => "user", "content" => $prompt]
        ],
        "max_tokens" => 200
    ];

    $ch = curl_init("https://api.openai.com/v1/chat/completions");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer {$apiKey}"
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        curl_close($ch);
        return "Erro ao gerar preview.";
    }
    curl_close($ch);

    $result = json_decode($response, true);
    if (isset($result['choices'][0]['message']['content'])) {
        return trim($result['choices'][0]['message']['content']);
    }
    return "Preview indisponível.";
}

while ($row = $result->fetch_assoc()) {
    $id = $row['categoria_id'];
    if (!isset($dados[$id])) {
        $dados[$id] = [
            'nome' => $row['categoria_nome'],
            'subitens' => []
        ];
    }
    if ($row['sub_titulo']) {
        $overview = gerarResumo($row['sub_url'], $OPENAI_API_KEY);

        $dados[$id]['subitens'][] = [
            'titulo' => $row['sub_titulo'],
            'url' => $row['sub_url'],
            'preview' => $overview
        ];
    }
}

echo json_encode(array_values($dados), JSON_UNESCAPED_UNICODE);
?>
