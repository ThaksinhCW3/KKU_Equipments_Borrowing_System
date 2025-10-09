<x-app-layout>
    <div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-md border border-gray-200">

            <!-- Header -->
            <div class="px-6 py-6 border-b border-gray-200">
                <h1 class="text-2xl font-semibold text-gray-800">ข้อมูลส่วนตัว</h1>
                <p class="text-sm text-gray-500 mt-1">จัดการข้อมูลและบัญชีของคุณ</p>
            </div>

            <!-- Alerts -->
            @if (session('success'))
                <div class="mx-6 mt-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mx-6 mt-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    <ul class="list-disc list-inside space-y-1 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Profile Form -->
            <div class="px-6 py-8 space-y-10">
                <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-sm text-gray-700">ชื่อ-นามสกุล</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                class="mt-2 w-full rounded-lg border-gray-300 bg-gray-50 text-gray-800 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="text-sm text-gray-700">รหัสนักศึกษา/รหัสพนักงาน</label>
                            <input type="text" name="uid" value="{{ old('uid', $user->uid) }}"
                                class="mt-2 w-full rounded-lg border-gray-300 bg-gray-50 text-gray-800 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="text-sm text-gray-700">อีเมล</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                class="mt-2 w-full rounded-lg border-gray-300 bg-gray-50 text-gray-800 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="text-sm text-gray-700">เบอร์โทรศัพท์</label>
                            <input type="tel" name="phonenumber"
                                value="{{ old('phonenumber', $user->phonenumber) }}"
                                class="mt-2 w-full rounded-lg border-gray-300 bg-gray-50 text-gray-800 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="px-6 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-medium transition">
                            บันทึกการเปลี่ยนแปลง
                        </button>
                    </div>
                </form>

                <!-- Password Section -->
                <div class="border-t border-gray-200 pt-8">
                    <h2 class="text-lg font-medium text-gray-800 mb-4">เปลี่ยนรหัสผ่าน</h2>

                    <form action="{{ route('profile.password.update') }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Current Password (full width) -->
                            <div class="md:col-span-2 relative">
                                <label class="text-sm text-gray-700">รหัสผ่านปัจจุบัน</label>
                                <input type="password" name="current_password"
                                    class="mt-2 w-full rounded-lg border-gray-300 bg-gray-50 text-gray-800 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- New Password -->
                            <div>
                                <label class="text-sm text-gray-700">รหัสผ่านใหม่</label>
                                <input type="password" name="password"
                                    class="mt-2 w-full rounded-lg border-gray-300 bg-gray-50 text-gray-800 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label class="text-sm text-gray-700">ยืนยันรหัสผ่านใหม่</label>
                                <input type="password" name="password_confirmation"
                                    class="mt-2 w-full rounded-lg border-gray-300 bg-gray-50 text-gray-800 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>




                        <div class="flex justify-end">
                            <button type="submit"
                                class="px-6 py-2.5 rounded-lg bg-green-600 hover:bg-green-700 text-white font-medium transition">
                                เปลี่ยนรหัสผ่าน
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Verification Section -->
                <div class="border-t border-gray-200 pt-8">
                    <h2 class="text-lg font-medium text-gray-800 mb-4">การยืนยันตัวตน</h2>

                    <div class="bg-gray-50 rounded-xl p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    @if ($user->verificationRequest && $user->verificationRequest->status === 'approved')
                                        <div
                                            class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                    @elseif($user->verificationRequest && $user->verificationRequest->status === 'pending')
                                        <div
                                            class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                    @else
                                        <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                                                </path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">สถานะการยืนยันตัวตน</p>
                                    <p class="text-sm text-gray-500">
                                        @if ($user->verificationRequest && $user->verificationRequest->status === 'approved')
                                            <span class="text-green-600 font-medium"> ยืนยันแล้ว</span>
                                        @elseif($user->verificationRequest && $user->verificationRequest->status === 'pending')
                                            <span class="text-yellow-600 font-medium"> รอการยืนยัน</span>
                                        @else
                                            <span class="text-red-600 font-medium"> ยังไม่ได้ยืนยัน</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div>
                                @if (!$user->verificationRequest || $user->verificationRequest->status !== 'approved')
                                    <a href="{{ route('verification.index') }}"
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        ยืนยันตัวตน
                                    </a>
                                @else
                                    <span
                                        class="inline-flex items-center px-4 py-2 bg-green-100 text-green-800 text-sm font-medium rounded-lg">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        ยืนยันแล้ว
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
