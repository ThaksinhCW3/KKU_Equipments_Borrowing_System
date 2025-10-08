<style>
.read-notification {
    opacity: 0.6 !important;
    background-color: #f9fafb;
}
.read-notification .font-semibold {
    color: #6b7280 !important;
}
.read-notification .text-sm {
    color: #9ca3af !important;
}
</style>

<header class="bg-white shadow-sm border-b  border-gray-200 ">
    <!-- Top Navigation Bar -->
    <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-blue-600 rounded flex items-center justify-center">
                        KKU
                    </div>
                    <span class="text-xl font-semibold text-gray-800">Borrow</span>
                </a>
            </div>
            
            <!-- Navigation Links moved here -->
            <div class="hidden md:flex space-x-8">
                <a href="{{ route('home') }}"
                    class="{{ request()->routeIs('home') ? 'text-blue-600 font-medium' : 'text-gray-700 hover:text-blue-700' }}">
                    หน้าหลัก
                </a>

                <!-- Dropdown -->
                <div class="relative group">
                    <button
                        class="flex items-center {{ request()->is('category*') ? 'text-blue-600 font-medium' : 'text-gray-700 hover:text-blue-700' }}">
                        หมวดหมู่
                        <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0
                            111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0
                            010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div
                        class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                        @foreach ($categories ?? [] as $cat)
                            @if (is_object($cat))
                                <a href="/?category={{ $cat->cate_id }}"
                                    class="block px-4 py-2 text-sm {{ request('category') == $cat->cate_id ? 'text-blue-600 font-medium bg-gray-50' : 'text-gray-700 hover:bg-gray-100' }}">
                                    {{ $cat->name }}
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>

                @php
                    $user = Auth::user();
                @endphp

                @auth
                    @if ($user && in_array($user->role, ['admin', 'staff']))
                        <a href="{{ route('admin.index') }}"
                            class="{{ request()->routeIs('admin.*') ? 'text-blue-600 font-medium' : 'text-gray-700 hover:text-blue-700' }}">
                            แอดมิน
                        </a>
                    @endif
                @endauth
            </div>
            <!-- User Authentication - Hidden on mobile -->
            <div class="flex">
                @auth
                    <notification-bell></notification-bell>
                @else
                    <div class="relative inline-block">
                        <button class="p-2 rounded-full hover:bg-gray-200 relative">
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 64 64">
                                <path
                                    d="M 32 10 C 29.662 10 28.306672 11.604938 27.638672 13.085938 C 24.030672 13.809937 17.737984 16.956187 16.958984 24.742188 C 16.665984 29.334188 16.1185 37.883781 13.0625 39.300781 C 12.8505 39.398781 12.655234 39.533219 12.490234 39.699219 C 12.235234 39.954219 10 42.294 10 46 C 10 47.104 10.896 48 12 48 L 25.257812 48 C 25.652433 51.372928 28.522752 54 32 54 C 35.477248 54 38.347567 51.372928 38.742188 48 L 52 48 C 53.104 48 54 47.104 54 46 C 54 42.294 51.764766 39.954219 51.509766 39.699219 C 51.344766 39.534219 51.1495 39.397828 50.9375 39.298828 C 47.8825 37.881828 47.333203 29.333922 47.033203 24.669922 C 46.258203 16.945922 39.966375 13.806984 36.359375 13.083984 C 35.692375 11.603984 34.338 10 32 10 z M 32 14 C 32.603 14 32.766719 14.619859 32.886719 15.255859 C 33.063719 16.190859 33.884422 16.914062 34.857422 16.914062 C 34.931422 16.914063 42.311828 17.650047 43.048828 24.998047 C 43.557828 32.932047 44.389891 40.250797 48.837891 42.716797 C 49.024891 42.956797 49.333937 43.401 49.585938 44 L 14.414062 44 C 14.667063 43.397 14.976203 42.95375 15.158203 42.71875 C 19.609203 40.25475 20.442312 32.935313 20.945312 25.070312 C 21.688313 17.650312 29.068578 16.914062 29.142578 16.914062 C 30.099578 16.914062 30.934375 16.156391 31.109375 15.275391 C 31.232375 14.660391 31.396 14 32 14 z M 29.335938 48 L 34.664062 48 C 34.319789 49.152328 33.262739 50 32 50 C 30.737261 50 29.680211 49.152328 29.335938 48 z">
                                </path>
                            </svg>
                            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                                0
                            </span>
                        </button>
                    </div>
                @endauth


                <div class="hidden md:flex items-center space-x-4">
                    <div class="w-px h-6 bg-gray-300"></div>
                    @auth
                        <!-- User Dropdown -->
                        <div class="relative group">
                            <button class="flex items-center space-x-2 font-medium hover:text-blue-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span>{{ Auth::user()->name }}</span>
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0
                                    111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0
                                    010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div class="absolute right-0 mt-2 w-56 bg-white rounded-md shadow-lg py-1 z-10 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                                <a href="{{ route('profile.show') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    ข้อมูลส่วนตัว
                                </a>
                                <a href="{{ route('borrower.equipments.myreq') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    คำขอของฉัน
                                </a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <form method="POST" action="{{ route('logout') }}" class="logout-form">
                                    @csrf
                                    <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        ออกจากระบบ
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Verification Badge -->
                        @if(!auth()->user()->verificationRequest || auth()->user()->verificationRequest->status !== 'approved')
                            <a href="{{ route('verification.index') }}" 
                               class="inline-flex items-center px-2 py-1 text-sm bg-orange-100 hover:bg-orange-200 text-orange-700 rounded-md transition min-w-0 ml-3">
                                <span class="flex items-center space-x-1 w-full">
                                    <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 8V12M12 16H12.01M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <span class="truncate">ยังไม่ได้ยืนยันตัวตน</span>
                                </span>
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}"
                            class="flex items-center text-gray-700 hover:text-gray-900 font-medium">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            เข้าสู่ระบบ
                        </a>
                        <span class="text-gray-400">/</span>
                        <a href="{{ route('register') }}"
                            class="text-gray-700 hover:text-gray-900 font-medium">ลงทะเบียน</a>
                    @endauth
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button id="mobile-menu-button"
                        class="text-gray-700 hover:text-gray-900 p-2 rounded-md hover:bg-gray-100 transition-colors duration-200">
                        <svg id="menu-icon" class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg id="close-icon" class="w-7 h-7 hidden" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="md:hidden hidden bg-gray-50 border-t border-gray-200">
        <div class="px-4 py-6 space-y-4">
            <!-- Mobile Navigation Links -->
            <div class="space-y-3">
                <a href="{{ route('home') }}" class="block text-blue-600 hover:text-blue-700 font-medium py-2">หน้าหลัก</a>
                <div class="space-y-2">
                    <div class="text-gray-700 font-medium py-2">หมวดหมู่</div>
                    <div class="pl-4 space-y-2">
                        @foreach ($categories ?? [] as $cat)
                            @if (is_object($cat))
                                <a href="/?category={{ $cat->cate_id }}"
                                    class="block text-gray-600 hover:text-gray-800 py-1">{{ $cat->name }}</a>
                            @endif
                        @endforeach
                    </div>
                </div>
                
                @php
                    $user = Auth::user();
                @endphp

                @auth
                    @if ($user && in_array($user->role, ['admin', 'staff']))
                        <a href="{{ route('admin.index') }}"
                            class="block text-gray-700 hover:text-gray-900 font-medium py-2">แอดมิน</a>
                    @endif
                @endauth
            </div>

            <!-- Mobile User Authentication -->
            <div class="pt-4 border-t border-gray-200">
                @auth
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center font-medium text-gray-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>{{ Auth::user()->name }}</span>
                        </div>
                        <!-- Mobile Notification Bell -->
                        <notification-bell></notification-bell>
                    </div>
                    
                    <!-- Mobile User Menu -->
                    <div class="space-y-2">
                        <a href="{{ route('profile.show') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            ข้อมูลส่วนตัว
                        </a>
                        <a href="{{ route('borrower.equipments.myreq') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            คำขอของฉัน
                        </a>
                        @if(!auth()->user()->verificationRequest || auth()->user()->verificationRequest->status !== 'approved')
                            <a href="{{ route('verification.index') }}" class="flex items-center px-3 py-2 text-sm text-orange-700 hover:bg-orange-50 rounded-md">
                                <svg class="w-4 h-4 mr-3" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 8V12M12 16H12.01M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <span>ยังไม่ได้ยืนยันตัวตน</span>
                            </a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}" class="logout-form">
                            @csrf
                            <button type="submit" class="flex items-center w-full px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md">
                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                ออกจากระบบ
                            </button>
                        </form>
                    </div>
                @else
                    <div class="space-y-2">
                        <a href="{{ route('login') }}" class="block text-gray-700 hover:text-gray-900 font-medium py-2">
                            เข้าสู่ระบบ
                        </a>
                        <a href="{{ route('register') }}"
                            class="block text-gray-700 hover:text-gray-900 font-medium py-2">
                            ลงทะเบียน
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>

    <!-- Search Bar Section (Vue mount) -->
    <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between gap-4 mb-4">
            <!-- Breadcrumb -->
            <nav class="flex items-center text-sm text-gray-500 space-x-1" aria-label="Breadcrumb">
            {{-- Home --}}
            <a href="{{ route('home') }}"
                class="flex items-center hover:text-blue-600 {{ request()->routeIs('home') ? 'text-blue-600 font-medium' : '' }}">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 2L2 8h2v8h4V12h4v4h4V8h2L10 2z"></path>
                </svg>
                หน้าแรก
            </a>
            @if (!request()->routeIs('home'))
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            @endif
            @if (request()->routeIs('equipments.show'))
                <span class="text-gray-700 font-medium">
                    {{ $equipment->name ?? 'รายละเอียด' }}
                </span>
            @endif
            @if (request()->routeIs('borrower.equipments.myreq') || request()->routeIs('borrower.equipments.reqdetail'))
                <a href="{{ route('borrower.equipments.myreq') }}"
                    class="hover:text-blue-600 {{ request()->routeIs('borrower.equipments.myreq') ? 'text-blue-600 font-medium' : '' }}">
                    คำขอของฉัน
                </a>
            @endif
            @if (request()->routeIs('borrower.equipments.reqdetail'))
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            @endif
            @if (request()->routeIs('borrower.equipments.reqdetail'))
                <span class="text-gray-700 font-medium">รายละเอียดคำขอ</span>
            @endif
            @if (request()->routeIs('profile.show'))
                <span class="text-gray-700 font-medium">ข้อมูลส่วนตัว</span>
            @endif
            
            {{-- Verification breadcrumb --}}
            @if (request()->routeIs('verification.index'))
                <span class="text-gray-700 font-medium">ยืนยันตัวตน</span>
            @endif
            
            {{-- Category breadcrumb --}}
            @if (request()->filled('category'))
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <a href="{{ route('home') }}" class="hover:text-blue-600 text-gray-700">
                    หมวดหมู่
                </a>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                @php
                    $categoryCode = request()->get('category');
                    $category = $categories->where('cate_id', $categoryCode)->first();
                @endphp
                <span class="text-gray-700 font-medium">
                    {{ $category->name ?? 'หมวดหมู่' }}
                </span>
            @endif
            
            {{-- Equipment detail breadcrumb --}}
            @if (request()->routeIs('equipments.show') && isset($equipment))
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-gray-700 font-medium">
                    {{ $equipment->name ?? 'รายละเอียดอุปกรณ์' }}
                </span>
            @endif
            </nav>
            
            <!-- Filter and Search Bar -->
            <div class="flex items-center gap-3">
                <!-- Existing Blue Filter Component -->
                
                
                <!-- Search Bar -->
                <div id="header-search" class="flex-1 max-w-md">
                </div>
            </div>
        </div>

    </div>
</header>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const menuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        const menuIcon = document.getElementById('menu-icon');
        const closeIcon = document.getElementById('close-icon');

        menuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
            menuIcon.classList.toggle('hidden');
            closeIcon.classList.toggle('hidden');
        });

        document.querySelectorAll('.mobile-submenu-toggle').forEach(btn => {
            btn.addEventListener('click', () => {
                const submenu = btn.nextElementSibling;
                submenu.classList.toggle('hidden');
                const arrow = btn.querySelector('svg');
                arrow.classList.toggle('rotate-180');
            });
        });
    });
    document.addEventListener('DOMContentLoaded', function() {
        const logoutForms = document.querySelectorAll('.logout-form');

        logoutForms.forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'ยืนยันการออกจากระบบ',
                    text: 'คุณต้องการออกจากระบบใช่หรือไม่?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'ตกลง',
                    cancelButtonText: 'ยกเลิก',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
