<aside id="admin-sidebar" class="w-64 bg-white border-r min-h-screen md:sticky md:top-0">
    <nav class="p-4 space-y-4">
        <!-- Dashboard Section -->
        <div class="space-y-1">
            <a href="{{ route('admin.index') }}"
                class="{{ request()->routeIs('admin.index') ? 'flex items-center px-3 py-2 rounded-md text-sm font-medium bg-blue-100 text-gray-900' : 'flex items-center px-3 py-2 rounded-md text-sm text-gray-900' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 1 0 7.5 7.5h-7.5V6Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0 0 13.5 3v7.5Z" />
                </svg>
                แดชบอร์ด
            </a>
        </div>

        <!-- Divider Line -->
        <hr class="border-gray-200">

        <!-- Section Header: MANAGEMENT -->
        <div>
            <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
                การจัดการ
            </h3>

<div x-data="{ open: false }" class="space-y-1">
                <button @click="open = !open"
                    class="w-full flex items-center justify-between px-3 py-2 rounded-md text-sm font-medium text-gray-900 hover:focus:outline-none">
                    <span class="flex items-center gap-2">
                        <!-- Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25M9 16.5v.75m3-3v3M15 12v5.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        จัดการคำขอ
                    </span>
                    <!-- Arrow -->
                    <svg :class="{ 'rotate-180': open }"
                        class="w-4 h-4 text-gray-500 transform transition-transform duration-200" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- Dropdown container -->
                <div x-show="open" x-transition class="pl-6 mt-1 space-y-1 border-l border-gray-200">

                    <a href="{{ route('admin.requests.index') }}"
                        class="{{ request()->routeIs('admin.requests.index') ? 'flex items-center px-3 py-2 rounded-md text-sm font-medium bg-blue-100 text-gray-900' : 'flex items-center px-3 py-2 rounded-md text-sm text-gray-900' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 3 3 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                        </svg>
                        คำขอยืมอุปกรณ์
                    </a>
                    {{-- <a href="{{ route('admin.requests.summary') }}"
                        class="{{ request()->routeIs('admin.requests.summary') ? 'flex items-center px-3 py-2 rounded-md text-sm font-medium bg-blue-100 text-gray-900' : 'flex items-center px-3 py-2 rounded-md text-sm text-gray-900' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                        </svg>
                        สรุปคำขอ
                    </a> --}}
                </div>
            </div>
           
        </div>

        <!-- Divider Line -->
        <hr class="border-gray-200">

        <!-- Section Header: ADMIN TOOLS -->
        @can('admin')
        <div>
            <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
                เครื่องมือผู้ดูแลระบบ
            </h3>


            {{-- หน้ารายงาน --}}
            <div x-data="{ open: false }" class="space-y-1">
                <button @click="open = !open"
                    class="{{ request()->routeIs('admin.report.*') ? 'w-full flex items-center justify-between px-3 py-2 rounded-md text-sm font-medium bg-blue-100 text-gray-900 hover:focus:outline-none' : 'w-full flex items-center justify-between px-3 py-2 rounded-md text-sm font-medium text-gray-900 hover:focus:outline-none' }}">
                    <span class="flex items-center gap-2">
                        <!-- Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25M9 16.5v.75m3-3v3M15 12v5.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        รายงาน
                    </span>
                    <!-- Arrow -->
                    <svg :class="{ 'rotate-180': open }"
                        class="w-4 h-4 text-gray-500 transform transition-transform duration-200" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- Dropdown container -->
                <div x-show="open" x-transition class="pl-6 mt-1 space-y-1 border-l border-gray-200">

                    <a href="{{ route('admin.report.index', ['type' => 'user']) }}"
                        class="{{ request()->routeIs('admin.report.index') && request()->route('type') == 'user' ? 'flex items-center px-3 py-2 rounded-md text-sm font-medium bg-blue-100 text-gray-900' : 'flex items-center px-3 py-2 rounded-md text-sm text-gray-900' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                        </svg>
                        รายงานผู้ใช้
                    </a>

                    <a href="{{ route('admin.report.index', ['type' => 'equipment']) }}"
                        class="{{ request()->routeIs('admin.report.index') && request()->route('type') == 'equipment' ? 'flex items-center px-3 py-2 rounded-md text-sm font-medium bg-blue-100 text-gray-900' : 'flex items-center px-3 py-2 rounded-md text-sm text-gray-900' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                        </svg>
                        รายงานอุปกรณ์
                    </a>

                    <a href="{{ route('admin.report.index', ['type' => 'category']) }}"
                        class="{{ request()->routeIs('admin.report.index') && request()->route('type') == 'category' ? 'flex items-center px-3 py-2 rounded-md text-sm font-medium bg-blue-100 text-gray-900' : 'flex items-center px-3 py-2 rounded-md text-sm text-gray-900' }}">
                        <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                        รายงานหมวดหมู่
                    </a>

                    <a href="{{ route('admin.report.index', ['type' => 'request']) }}"
                        class="{{ request()->routeIs('admin.report.index') && request()->route('type') == 'request' ? 'flex items-center px-3 py-2 rounded-md text-sm font-medium bg-blue-100 text-gray-900' : 'flex items-center px-3 py-2 rounded-md text-sm text-gray-900' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0-3-3m3 3 3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                        </svg>
                        รายงานคำขอ
                    </a>
                    <a href="{{ route('admin.report.index', ['type' => 'log']) }}"
                        class="{{ request()->routeIs('admin.report.index') && request()->route('type') == 'log' ? 'flex items-center px-3 py-2 rounded-md text-sm font-medium bg-blue-100 text-gray-900' : 'flex items-center px-3 py-2 rounded-md text-sm text-gray-900' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                        </svg>
                        รายงานบันทึกการใช้งาน
                    </a>

                    <a href="{{ route('admin.report.index', ['type' => 'transaction']) }}"
                        class="{{ request()->routeIs('admin.report.index') && request()->route('type') == 'transaction' ? 'flex items-center px-3 py-2 rounded-md text-sm font-medium bg-blue-100 text-gray-900' : 'flex items-center px-3 py-2 rounded-md text-sm text-gray-900' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                        </svg>
                        รายงานธุรกรรม
                    </a>
                </div>
            </div>

 {{-- หน้าจัดการอุปกรณ์ --}}
            <div x-data="{ open: false }" class="space-y-1">
                <button @click="open = !open"
                    class="w-full flex items-center justify-between px-3 py-2 rounded-md text-sm font-medium text-gray-900 hover:focus:outline-none">
                    <span class="flex items-center gap-2">
                        <!-- Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25M9 16.5v.75m3-3v3M15 12v5.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        อุปกรณ์และหมวดหมู่
                    </span>
                    <!-- Arrow -->
                    <svg :class="{ 'rotate-180': open }"
                        class="w-4 h-4 text-gray-500 transform transition-transform duration-200" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- Dropdown container -->
                <div x-show="open" x-transition class="pl-6 mt-1 space-y-1 border-l border-gray-200">

                    <a href="{{ route('admin.category.index') }}"
                        class="{{ request()->routeIs('admin.category.index') ? 'flex items-center px-3 py-2 rounded-md text-sm font-medium bg-blue-100 text-gray-900' : 'flex items-center px-3 py-2 rounded-md text-sm text-gray-900' }}">
                        <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                        จัดการหมวดหมู่
                    </a>

                    <a href="{{ route('admin.equipment.index') }}"
                        class="{{ request()->routeIs('admin.equipment.index') ? 'flex items-center px-3 py-2 rounded-md text-sm bg-blue-100 text-gray-900' : 'flex items-center px-3 py-2 rounded-md text-sm text-gray-900' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                        </svg>
                        จัดการอุปกรณ์
                    </a>

                    {{-- <a href="{{ route('admin.equipment.summary') }}"
                        class="{{ request()->routeIs('admin.equipment.summary') ? 'flex items-center px-3 py-2 rounded-md text-sm bg-blue-100 text-gray-900' : 'flex items-center px-3 py-2 rounded-md text-sm text-gray-900' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                        </svg>
                        สรุปอุปกรณ์
                    </a> --}}

                </div>
                        <a href="{{ route('admin.verification.index') }}"
            class="{{ request()->routeIs('admin.verification.*') ? 'flex items-center px-3 py-2 rounded-md text-sm font-medium bg-blue-100 text-gray-900' : 'flex items-center px-3 py-2 rounded-md text-sm text-gray-900' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>

            หน้าจัดการยืนยันตัวตน
        </a>
            </div>
            {{-- <a href="{{ route('admin.user.index') }}"
                class="{{ request()->routeIs('admin.user.*') ? 'flex items-center px-3 py-2 rounded-md text-sm font-medium bg-blue-100 text-gray-900' : 'flex items-center px-3 py-2 rounded-md text-sm text-gray-900' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                </svg>
                จัดการผู้ใช้
            </a> --}}
            
            {{-- <a href="{{ route('admin.verification.index') }}"
                class="{{ request()->routeIs('admin.verification.*') ? 'flex items-center px-3 py-2 rounded-md text-sm font-medium bg-blue-100 text-gray-900' : 'flex items-center px-3 py-2 rounded-md text-sm text-gray-900' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                คำขอยืนยันตัวตน
            </a> --}}
        </div>
        @endcan
    </nav>
</aside>
