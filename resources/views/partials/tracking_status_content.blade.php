<div class="space-y-6 py-4">
    <div class="border-b pb-4 mb-4">
        <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider">Detail Pengajuan</h4>
        <p class="text-lg font-medium">{{ $submission->user->name }} - {{ $submission->type->name }}</p>
        <p class="text-sm text-gray-600">Tanggal Pengajuan: {{ $submission->created_at->format('d F Y') }}</p>
    </div>

    <!-- Timeline UI -->
    <div class="relative">
        @foreach($flowStages as $index => $stage)
            <div class="flex items-start mb-8 last:mb-0">
                <!-- Garis Penghubung -->
                @if(!$loop->last)
                <div class="absolute left-4 top-8 -ml-px h-full w-0.5 bg-gray-200"></div>
                @endif

                <!-- Icon Status -->
                <div class="relative flex items-center justify-center">
                    @if($stage['status'] == 'approved')
                        <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                            <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        </span>
                    @elseif($stage['status'] == 'rejected')
                        <span class="h-8 w-8 rounded-full bg-red-500 flex items-center justify-center ring-8 ring-white">
                            <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                        </span>
                    @else
                        <span class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center ring-8 ring-white">
                            <span class="h-2.5 w-2.5 rounded-full bg-gray-400"></span>
                        </span>
                    @endif
                </div>

                <!-- Konten Teks -->
                <div class="ml-6 min-w-0 flex-1">
                    <div class="flex justify-between items-center">
                        <h3 class="text-sm font-bold {{ $stage['is_current'] ? 'text-blue-600' : 'text-gray-900' }}">
                            {{ $stage['label'] }}
                            @if($stage['is_current'])
                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">Posisi Sekarang</span>
                            @endif
                        </h3>
                        <span class="text-xs text-gray-500">{{ $stage['date'] }}</span>
                    </div>
                    <p class="text-sm text-gray-500">Oleh: {{ $stage['user'] }}</p>
                    @if($stage['note'])
                        <p class="mt-1 text-sm italic text-gray-600 bg-gray-50 p-2 rounded border-l-4 border-gray-300">"{{ $stage['note'] }}"</p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
