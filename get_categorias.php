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

while ($row = $result->fetch_assoc()) {
    $id = $row['categoria_id'];
    if (!isset($dados[$id])) {
        $dados[$id] = [
            'nome' => $row['categoria_nome'],
            'subitens' => []
        ];
    }
    if ($row['sub_titulo']) {
        $dados[$id]['subitens'][] = [
            'titulo' => $row['sub_titulo'],
            'url' => $row['sub_url']
        ];
    }
}

echo json_encode(array_values($dados), JSON_UNESCAPED_UNICODE);
?>
