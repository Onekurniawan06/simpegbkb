<?php

namespace App\Http\Controllers;

use App\Models\FilePersyaratanPensiun;
use Illuminate\Support\Facades\Storage;

class DokumenController extends Controller
{
    public function viewFile($id): mixed
    {
        $document = FilePersyaratanPensiun::findOrFail($id);

        // Dapatkan jalur file absolut di server menggunakan storage_path()
        $pathToFile = storage_path('app/' . $document->path_file_server); //

        // Pastikan file ada di storage sebelum ditampilkan
        if (file_exists($pathToFile)) {
            // Gunakan helper global response()->file() atau response()->download()
            // file() akan mencoba menampilkan file secara inline (di browser, misal PDF)
            // download() akan memaksa browser untuk mengunduh file
            return response()->file($pathToFile, [
                'Content-Disposition' => 'inline; filename="' . $document->nama_file_asli . '"'
            ]);
            // Jika ingin paksa download: return response()->download($pathToFile, $document->nama_file_asli);
        }

        abort(404, 'File not found.');
    }
}
