<?php
header('Content-Type: application/json');

// 1. AMBIL TAHUN DARI PARAMETER URL
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');

// 2. GUNAKAN URL BARU (PASTIKAN PENULISANNYA PERSIS SEPERTI INI)
$apiUrl = "https://libur.deno.dev/api" . $year;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200 || !$response) {
    echo json_encode([]); // Kirim array kosong jika gagal
} else {
    $data = json_decode($response, true);
    $formattedData = [];

    if (is_array($data)) {
        foreach ($data as $holiday) {
            // Kita ubah formatnya agar JS kamu tetap bisa membaca "holiday_date"
            $formattedData[] = [
                "holiday_date" => $holiday['date'],
                "holiday_name" => isset($holiday['holiday_list'][0]) ? $holiday['holiday_list'][0] : "Hari Libur"
            ];
        }
    }
    echo json_encode($formattedData);
}
