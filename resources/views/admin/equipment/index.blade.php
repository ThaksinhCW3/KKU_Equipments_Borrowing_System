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

    <script>
        // Debug: Check if data is loaded correctly
        document.addEventListener('DOMContentLoaded', function() {
            const el = document.getElementById('equipment-table');
            if (el) {
                console.log('Equipment table element found');
                console.log('Equipment data:', el.dataset.equipments);
                console.log('Categories data:', el.dataset.categories);
                console.log('Role:', el.dataset.role);
                
                try {
                    const equipments = JSON.parse(el.dataset.equipments || '[]');
                    console.log('Parsed equipments:', equipments);
                    console.log('Equipment count:', equipments.length);
                    
                    // Check if CA-ER8-002 is in the data
                    const targetEquipment = equipments.find(e => e.code === 'CA-ER8-002');
                    if (targetEquipment) {
                        console.log('✅ CA-ER8-002 found in data:', targetEquipment);
                    } else {
                        console.log('❌ CA-ER8-002 NOT found in data');
                        console.log('Available codes:', equipments.map(e => e.code));
                    }
                } catch (e) {
                    console.error('Error parsing equipment data:', e);
                }
            } else {
                console.error('Equipment table element not found');
            }
        });
    </script>
</x-admin-layout>
