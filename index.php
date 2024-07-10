<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$validCommands = array("Matikan Lampu", "Nyalakan Lampu Merah", "Nyalakan Lampu Hijau", "Nyalakan Lampu Biru");

// Handle the OPTIONS request
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit();
}

// Handle the GET request
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    if (file_exists('data.csv')) {
        $file = fopen('data.csv', 'r');
        $data = [];
        while (($line = fgetcsv($file)) !== FALSE) {
            $data[] = array("command" => $line[0]);
        }
        fclose($file);
        echo json_encode(array("status" => "success", "data" => $data));
    } else {
        echo json_encode(array("status" => "success", "data" => []));
    }
}
// Handle the POST request
elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
    $input = json_decode(file_get_contents('php://input'), TRUE);
    if (isset($input["command"]) && in_array($input["command"], $validCommands)) {
        $file = fopen('data.csv', 'w');  // Open in 'w' mode to overwrite existing content
        fputcsv($file, array($input["command"]));
        fclose($file);
        echo json_encode(array("status" => "success", "data" => array("command" => $input["command"])));
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Bad Request: Invalid command"));
    }
}
else {
    http_response_code(405);
    echo json_encode(array("message" => "Method Not Allowed"));
}
?>