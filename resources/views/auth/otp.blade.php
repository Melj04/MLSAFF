<x-guest-layout>
    <form method="POST" action="{{ route('device.verify') }}">
        @csrf
        <div>
            <label for="otp" class="block font-medium text-sm text-gray-700">{{ __('OTP') }}</label>
            <input id="otp" class="block mt-1 w-full" type="text" name="otp" required autofocus>
            @if (session('status'))
                <div class="alert alert-success text-green-500">
                    {{ session('status') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger text-red-600">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
        <div class="flex items-center justify-end mt-4">
            <!-- Resend OTP Link -->
            @if ($isExpired && $device)
                <a href="#" onclick="event.preventDefault(); document.getElementById('resend-form').submit();"
                    class="underline text-sm text-gray-600 hover:text-gray-900">
                    {{ __('Resend OTP') }}
                </a>
            @else
                <span class="text-sm text-gray-600">{{ __('Need OTP Verification') }}</span>
            @endif

            <button type="submit"
                class="ml-3 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:border-blue-700 focus:shadow-outline-blue active:bg-blue-700 transition ease-in-out duration-150">
                {{ __('Verify Device') }}
            </button>
        </div>
    </form>

    <!-- Hidden form for resending OTP -->
    <form id="resend-form" method="POST" action="{{ route('device.resend') }}" style="display: none;">
        @csrf
    </form>
</x-guest-layout>
