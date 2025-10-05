<x-admin-layout>
    @if(request('search'))
        <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <span class="text-sm text-blue-700">กำลังค้นหา: <strong>"{{ request('search') }}"</strong></span>
                </div>
                <a href="{{ route('admin.equipment.index') }}" class="text-sm text-blue-600 hover:text-blue-800 underline">
                    ล้างตัวกรอง
                </a>
            </div>
        </div>
    @endif

    @if(request('status'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <span class="text-sm text-green-700">กำลังแสดงอุปกรณ์สถานะ: <strong>{{ ucfirst(request('status')) }}</strong></span>
                </div>
                <a href="{{ route('admin.equipment.index') }}" class="text-sm text-green-600 hover:text-green-800 underline">
                    ล้างตัวกรอง
                </a>
            </div>
        </div>
    @endif

    <div id="equipment-table"
        data-equipments='@json($equipments)'
        data-categories='@json($categories)'
        data-role="{{ Auth::user()->role }}">
    </div>
</x-admin-layout>
