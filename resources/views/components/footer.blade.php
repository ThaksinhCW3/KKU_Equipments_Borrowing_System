<footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
  <div class="max-w-screen-2xl mx-auto px-6 py-6">
    <div class="flex flex-col sm:flex-row justify-between items-start gap-8">
      <!-- Left Side: Logo and Description -->
      <div class="text-center sm:text-left flex-1">
        <!-- Logo -->
        <div class="flex items-center">
          <a href="{{ route('home') }}" class="flex items-center space-x-2">
            <img src="{{ asset('logo/KKU_Engineering.png') }}" alt="KKU Borrow Logo" class="h-16 w-auto object-contain">
          </a>
        </div>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
          เว็บไซต์ KKU Borrow เป็นระบบยืม‑คืนอุปกรณ์สำหรับคณะวิศวกรรมศาสตร์ มหาวิทยาลัยขอนแก่น ช่วยให้การจอง
          ตรวจสอบสถานะ และจัดการการยืมอุปกรณ์เป็นเรื่องง่าย ปลอดภัย และรวดเร็ว หากมีคำถามหรือต้องการความช่วยเหลือ
          กรุณาติดต่อผู้ดูแลระบบของคณะ
        </p>
      </div>

      <!-- Right Side: Contact Details -->
      <div class="text-center sm:text-right">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">ติดต่อเรา</h3>
        <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
          <div class="flex items-center justify-center sm:justify-end gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span>คณะวิศวกรรมศาสตร์ มหาวิทยาลัยขอนแก่น</span>
          </div>
          <div class="flex items-center justify-center sm:justify-end gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
            </svg>
            <span>043-202-845</span>
          </div>
          <div class="flex items-center justify-center sm:justify-end gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            <span>engineering@kku.ac.th</span>
          </div>
        </div>
      </div>
    </div>

    <div
      class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-4 text-center text-sm text-gray-500 dark:text-gray-400">
      <p>© {{ date('Y') }} Intelligent Solution Software via Khon Kaen University, Thailand. All rights reserved.</p>
    </div>
  </div>
</footer>