<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response; // Import Response Facade
use Illuminate\Support\Facades\File; // Import File Facade

class FileDownloadController extends Controller
{
    public function downloadForm()
    {
        // Gunakan helper storage_path() yang mengarah ke folder "storage" Laravel Anda.
        // Helper ini akan otomatis menyesuaikan dengan OS Windows atau Linux.

        // Jalur relatif di dalam storage/app/
        $relativePath = 'private/forms/form-lembur.docx';

        // Menggabungkan path dasar storage dengan path relatif
        $filePath = storage_path('app/' . $relativePath);

        // Pastikan Anda menggunakan File::exists() untuk pengecekan file yang andal
        if (File::exists($filePath)) {
            // Gunakan Response::download()
            return Response::download($filePath);
        } else {
            // Jika file tidak ditemukan, ini yang akan ditampilkan di konsol browser Anda
            abort(404, 'File lembur tidak ditemukan pada lokasi ' . $filePath);
        }
    }
}

