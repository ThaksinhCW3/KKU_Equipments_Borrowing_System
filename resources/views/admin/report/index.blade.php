<x-admin-layout>
    @section('title', 'รายงาน')
    @vite('resources/js/report-app.js')
    <div id="report-app" data-report-type="{{ $type }}"></div>
</x-admin-layout>
