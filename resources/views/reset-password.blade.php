<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - SIMPEG BKB</title>
    @vite('resources/css/app.css')
</head>

<body class="flex flex-col min-h-screen bg-moving">

    <div class="absolute top-4 left-4 md:top-8 md:left-8">
        <img src="{{ asset('images/logobkb.png') }}" alt="Logo Bank Kota Bogor" class="h-11 w-auto">
    </div>

    <div class="flex-grow flex items-center justify-center p-4">

        <div class="p-8 rounded-lg shadow-lg bg-gray-50 shadow-blue-300 max-w-sm w-full">

            <!-- Form ini mengarah ke named route 'password.update' -->
            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                <!-- Input tersembunyi untuk TOKEN yang kita dapatkan dari URL -->
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="mb-6 text-center">
                    <h1 class="text-xl font-bold text-gray-800 mb-2">Atur Ulang Password</h1>
                    <p class="text-sm text-gray-600">Masukkan email Anda dan password baru.</p>
                </div>

                {{-- Error Message (Laravel Validation Errors) --}}
                @if ($errors->any())
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li class="text-sm">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mb-4">
                    <label for="email" class="block text-gray-700 text-sm font-semibold mb-2">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full px-3 py-2 shadow-md border border-gray-300 rounded-sm focus:outline-none focus:ring-2 focus:ring-blue-200 @error('email') border-red-500 @enderror">
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-gray-700 text-sm font-semibold mb-2">Password Baru</label>
                    <input type="password" id="password" name="password" required
                    class="w-full px-3 py-2 shadow-md border border-gray-300 rounded-sm focus:outline-none focus:ring-2 focus:ring-blue-200 @error('password') border-red-500 @enderror">
                </div>

                <div class="mb-6">
                    <label for="password-confirm" class="block text-gray-700 text-sm font-semibold mb-2">Konfirmasi Password Baru</label>
                    <input type="password" id="password-confirm" name="password_confirmation" required
                    class="w-full px-3 py-2 shadow-md border border-gray-300 rounded-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                </div>

                <div class="mb-4">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-sm shadow-md transition duration-200">
                        Reset Password
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
