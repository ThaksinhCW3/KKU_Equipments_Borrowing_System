<x-admin-layout>
    @section('title', 'รายการผู้ใช้')
    <div id="user-table" data-users='@json($users)' data-role="admin"></div>
</x-admin-layout>