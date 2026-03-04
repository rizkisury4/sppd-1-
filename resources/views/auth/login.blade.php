<x-guest-layout>

<div class="min-h-screen flex items-center justify-center
            bg-gradient-to-br from-blue-700 via-blue-600 to-blue-900">

    <!-- LOGIN CARD -->
    <div class="backdrop-blur-lg bg-white/20
                border border-white/30
                shadow-2xl rounded-2xl
                w-full max-w-md p-8 text-white">

        <!-- LOGO -->
        <div class="flex justify-center mb-5">
            <img src="{{ asset('images/pln.png') }}"
                 class="h-16 object-contain">
        </div>

        <!-- TITLE -->
        <h2 class="text-center text-2xl font-bold mb-6">
            Sistem SPPD PLN
        </h2>

        <x-auth-session-status class="mb-4 text-center"
            :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- EMAIL -->
            <div>
                <label class="text-sm">Email</label>
                <input type="email"
                    name="email"
                    required
                    class="w-full mt-1 p-2 rounded-lg
                           bg-white/30 border border-white/40
                           placeholder-white text-white
                           focus:ring-2 focus:ring-yellow-300">
            </div>

            <!-- PASSWORD -->
            <div class="mt-4">
                <label class="text-sm">Password</label>
                <input type="password"
                    name="password"
                    required
                    class="w-full mt-1 p-2 rounded-lg
                           bg-white/30 border border-white/40
                           text-white
                           focus:ring-2 focus:ring-yellow-300">
            </div>

            <!-- REMEMBER -->
            <div class="flex items-center mt-4 text-sm">
                <input type="checkbox" name="remember"
                       class="mr-2">
                Remember me
            </div>
<div class="flex justify-between items-center mt-4 text-sm">
    
   
    @if (Route::has('password.request'))
        <a href="{{ route('password.request') }}"
           class="text-yellow-300 hover:text-yellow-400 underline">
            Forgot Password?
        </a>
    @endif

</div>
            <!-- BUTTON -->
            <button
                class="w-full mt-6 bg-yellow-400
                       hover:bg-yellow-500
                       text-blue-900 font-bold
                       py-2 rounded-lg
                       transition">
                Login
            </button>

        </form>

        <!-- FOOTER -->
        <div class="text-center text-xs mt-6 opacity-80">
            © {{ date('Y') }} PT PLN (Persero)
        </div>

    </div>

</div>

</x-guest-layout>