<?php
// Tampilkan semua error untuk debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Izinkan CORS dan set header JSON
header('Access-Control-Allow-Origin: *');

$year = filter_input(INPUT_GET, 'year', FILTER_VALIDATE_INT) ?: date('Y');

// *** PASTIKAN BARIS INI BENAR ***
$url = "https://" . "dayoffapi.vercel.app/api?year=" . $year;
// *******************************

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);

if ($response === FALSE) {
    $error_msg = curl_error($ch);
    $error_code = curl_errno($ch);
    http_response_code(500);
    echo json_encode([
        "error" => "cURL Error: " . $error_code . " - " . $error_msg,
        "status_code" => 500,
        "url" => $url
    ]);
} else {
    header('Content-Type: application/json');
    echo $response;
}
?>
