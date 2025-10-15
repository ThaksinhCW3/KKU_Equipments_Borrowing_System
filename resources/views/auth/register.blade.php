<x-app-layout>
    @section('title', 'ลงทะเบียน')
    
    <style>
        /* Hide breadcrumb and search on register page */
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
        <h2 class="text-2xl font-bold text-gray-900">ลงทะเบียน</h2>
        <p class="text-sm text-gray-500 mt-1">สร้างบัญชีเพื่อใช้งานระบบยืมอุปกรณ์</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Student/Employee ID -->
            <div class="mb-4">
                <label for="uid" class="block font-medium text-sm text-black">
                    รหัสนักศึกษา/รหัสพนักงาน <span class="text-red-500">*</span>
                </label>
                <x-text-input id="uid" class="block mt-1 w-full bg-white text-black border-gray-300"
                    type="text" name="uid" :value="old('uid')" placeholder="รหัสนักศึกษา/รหัสพนักงาน..." />
                <x-input-error :messages="$errors->get('uid')" class="mt-2" />
            </div>

            <!-- Name -->
            <div class="mb-4">
                <label for="name" class="block font-medium text-sm text-black">
                    ชื่อผู้ใช้ <span class="text-red-500">*</span>
                </label>
                <x-text-input id="name" class="block mt-1 w-full bg-white text-black border-gray-300"
                    type="text" name="name" :value="old('name')" placeholder="ชื่อผู้ใช้..." />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div class="mb-4">
                <label for="email" class="block font-medium text-sm text-black">
                    อีเมล <span class="text-red-500">*</span>
                </label>
                <x-text-input id="email" class="block mt-1 w-full bg-white text-black border-gray-300"
                    type="email" name="email" :value="old('email')" placeholder="อีเมล..." />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Phone Number -->
            <div class="mb-4">
                <label for="phonenumber" class="block font-medium text-sm text-black">
                    หมายเลขโทรศัพท์ <span class="text-red-500">*</span>
                </label>
                <x-text-input id="phonenumber" class="block mt-1 w-full bg-white text-black border-gray-300"
                    type="text" name="phonenumber" :value="old('phonenumber')" placeholder="หมายเลขโทรศัพท์..." />
                <x-input-error :messages="$errors->get('phonenumber')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label for="password" class="block font-medium text-sm text-black">
                    รหัสผ่าน <span class="text-red-500">*</span>
                </label>
                <x-text-input id="password" class="block mt-1 w-full bg-white text-black border-gray-300"
                    type="password" name="password" placeholder="อย่างน้อย 8 ตัวอักษร ตัวพิมพ์ใหญ่ และ ตัวเลข" />
                <ul id="password-errors" class="text-red-500 mt-2 text-sm"></ul>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="mb-6">
                <label for="password_confirmation" class="block font-medium text-sm text-black">
                    ยืนยันรหัสผ่าน <span class="text-red-500">*</span>
                </label>
                <x-text-input id="password_confirmation" class="block mt-1 w-full bg-white text-black border-gray-300"
                    type="password" name="password_confirmation" placeholder="ยืนยันรหัสผ่าน..." />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex flex-col gap-3">
                <x-primary-button id="register-button" class="w-3/4 mx-auto justify-center py-3">
                    {{ __('ลงทะเบียน') }}
                </x-primary-button>
                
                <a class="text-sm text-center text-orange-600 hover:text-orange-800" href="{{ route('login') }}">
                    {{ __('ลงทะเบียนไว้ก่อนหน้าแล้ว?') }}
                </a>
            </div>
        </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const uidInput = document.getElementById('uid');
            const nameInput = document.getElementById('name');
            const emailInput = document.getElementById('email');
            const phoneInput = document.getElementById('phonenumber');
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('password_confirmation');
            const registerButton = document.getElementById('register-button');

            function checkFields() {
                const allFilled = uidInput.value.trim() !== '' &&
                                nameInput.value.trim() !== '' &&
                                emailInput.value.trim() !== '' &&
                                phoneInput.value.trim() !== '' &&
                                passwordInput.value.trim() !== '' &&
                                confirmPasswordInput.value.trim() !== '';

                if (allFilled) {
                    registerButton.style.backgroundColor = '#f97316';
                    registerButton.onmouseover = function() {
                        this.style.backgroundColor = '#ea580c';
                    };
                    registerButton.onmouseout = function() {
                        this.style.backgroundColor = '#f97316';
                    };
                } else {
                    registerButton.style.backgroundColor = '#9ca3af';
                    registerButton.onmouseover = function() {
                        this.style.backgroundColor = '#6b7280';
                    };
                    registerButton.onmouseout = function() {
                        this.style.backgroundColor = '#9ca3af';
                    };
                }
            }

            // Check on input for all fields
            uidInput.addEventListener('input', checkFields);
            nameInput.addEventListener('input', checkFields);
            emailInput.addEventListener('input', checkFields);
            phoneInput.addEventListener('input', checkFields);
            passwordInput.addEventListener('input', checkFields);
            confirmPasswordInput.addEventListener('input', checkFields);

            // Password validation
            passwordInput.addEventListener("input", function () {
                const password = this.value;
                const errors = [];

                if (password.length < 8) {
                    errors.push("รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร");
                }
                if (!/[A-Z]/.test(password)) {
                    errors.push("ต้องมีอักษรตัวพิมพ์ใหญ่ (A-Z)");
                }
                if (!/[a-z]/.test(password)) {
                    errors.push("ต้องมีอักษรตัวพิมพ์เล็ก (a-z)");
                }
                if (!/[0-9]/.test(password)) {
                    errors.push("ต้องมีตัวเลข (0-9)");
                }

                const errorList = document.getElementById("password-errors");
                errorList.innerHTML = "";
                errors.forEach(err => {
                    const li = document.createElement("li");
                    li.textContent = err;
                    errorList.appendChild(li);
                });
            });

            // Initial check
            checkFields();
        });
    </script>
    </div>
</x-app-layout>