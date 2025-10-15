<x-app-layout>
    @section('title', 'เข้าสู่ระบบ')
    
    <style>
        /* Hide breadcrumb and search on login page */
        #header-search,
        nav[aria-label="Breadcrumb"] {
            display: none !important;
        }
        
        /* Extend gray background to fill entire area and remove padding */
        main {
            background-color: #f3f4f6 !important;
            min-height: calc(100vh - 200px);
            padding: 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        
        /* Remove header container padding */
        header .max-w-screen-2xl {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        
        /* Remove footer padding */
        footer,
        footer * {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        
        /* Remove all max-width containers padding */
        .max-w-screen-2xl,
        .max-w-7xl,
        .container {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
    </style>
    
    <div class="w-full max-w-md bg-white rounded-xl shadow-lg p-6 mx-4">
            <!-- Logo -->
            <div class="flex flex-col items-center justify-center mb-6">
                <a href="{{ route('home') }}" class="flex items-center space-x-1 mb-4">
                    <img src="{{ asset('logo/KKU_Engineering.png') }}" alt="KKU Borrow Logo"
                        class="h-16 w-auto object-contain">
                </a>
                <h2 class="text-2xl font-bold text-gray-900">เข้าสู่ระบบ</h2>
                <p class="text-sm text-gray-500 mt-1">กรุณาเข้าสู่ระบบเพื่อใช้งานระบบยืมอุปกรณ์</p>
            </div>
            
            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email -->
        <div class="mb-4">
            <label for="email" class="block font-medium text-sm text-black">
                อีเมล <span class="text-red-500">*</span>
            </label>
            <x-text-input id="email" class="block mt-1 w-full bg-white text-black border-gray-300" type="email"
                name="email" :value="old('email')" placeholder="อีเมล..." />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mb-4">
            <label for="password" class="block font-medium text-sm text-black">
                รหัสผ่าน <span class="text-red-500">*</span>
            </label>
            <x-text-input id="password" class="block mt-1 w-full bg-white text-black border-gray-300" type="password"
                name="password" placeholder="รหัสผ่าน..." />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center mb-4">
            <input id="remember_me" type="checkbox"
                class="rounded border-gray-300 text-orange-600 shadow-sm focus:ring-orange-500" name="remember">
            <label for="remember_me" class="ms-2 text-sm text-black">
                {{ __('จดจำฉันไว้') }}
            </label>
        </div>

        <!-- Actions -->
        <div class="flex flex-col gap-3">
            <x-primary-button id="login-button" class="w-3/4 mx-auto justify-center py-3">
                {{ __('เข้าสู่ระบบ') }}
            </x-primary-button>

            @if (Route::has('password.request'))
                <a class="text-sm text-center text-black hover:text-orange-700" href="{{ route('password.request') }}">
                    {{ __('ลืมรหัสผ่าน?') }}
                </a>
            @endif

            <a href="{{ route('register') }}" class="text-sm text-center text-orange-600 hover:text-orange-800">
                {{ __('ยังไม่มีบัญชี? สมัครสมาชิก') }}
            </a>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const loginButton = document.getElementById('login-button');

            function checkFields() {
                const emailFilled = emailInput.value.trim() !== '';
                const passwordFilled = passwordInput.value.trim() !== '';

                if (emailFilled && passwordFilled) {
                    // Change to lighter orange when all fields are filled
                    loginButton.style.backgroundColor = '#f97316'; // orange-500 (lighter)
                    loginButton.onmouseover = function() {
                        this.style.backgroundColor = '#ea580c'; // orange-600
                    };
                    loginButton.onmouseout = function() {
                        this.style.backgroundColor = '#f97316'; // orange-500
                    };
                } else {
                    // Much lighter gray color
                    loginButton.style.backgroundColor = '#9ca3af'; // gray-400 (much lighter)
                    loginButton.onmouseover = function() {
                        this.style.backgroundColor = '#6b7280'; // gray-500
                    };
                    loginButton.onmouseout = function() {
                        this.style.backgroundColor = '#9ca3af'; // gray-400
                    };
                }
            }

            // Check on input
            emailInput.addEventListener('input', checkFields);
            passwordInput.addEventListener('input', checkFields);

            // Initial check
            checkFields();
        });
    </script>
    </div>
</x-app-layout>