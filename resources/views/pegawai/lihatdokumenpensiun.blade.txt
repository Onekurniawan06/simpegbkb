<div class="mt-8">
    <h4 class="text-lg font-semibold mb-3 text-blue-700">Daftar Dokumen Persyaratan Terunggah</h4>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 shadow overflow-hidden sm:rounded-lg">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Dokumen (Kode)</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama File Asli</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">

                {{-- Loop melalui dokumen yang terkait dengan pengajuan --}}
                @foreach($pengajuan->dokumenPersyaratan as $index => $doc)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $index + 1 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{-- Fungsi sederhana untuk mengubah kode menjadi nama yang lebih user-friendly --}}
                            <span class="font-semibold">{{ ucfirst(str_replace('_', ' ', $doc->kode_dokumen)) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $doc->nama_file_asli }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            {{-- Tombol Lihat/Download --}}
                            <a href="{{ route('dokumen.view', $doc->id) }}" target="_blank"
                               class="text-blue-600 hover:text-blue-900 mr-3">
                                Lihat / Download
                            </a>
                        </td>
                    </tr>
                @endforeach

            </tbody>
        </table>
    </div>
</div>
