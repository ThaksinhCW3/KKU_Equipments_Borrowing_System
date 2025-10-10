<footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
  <div class="max-w-screen-2xl mx-auto px-6 py-6">
    <div class="flex flex-col sm:flex-row justify-between items-center sm:items-start">
      <div class="text-center sm:text-left">
        <!-- Logo -->
        <div class="flex items-center">
          <a href="{{ route('home') }}" class="flex items-center space-x-2">
            <img src="{{ asset('logo/KKU_Engineering.png') }}" alt="KKU Borrow Logo" class="h-16 w-auto object-contain">
          </a>
        </div>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
          Borrow equipment easily and efficiently.
        </p>
      </div>
    </div>

    <div
      class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-4 text-center text-sm text-gray-500 dark:text-gray-400">
      <p>© {{ date('Y') }} KKU Borrow. All rights reserved.</p>
    </div>
  </div>
</footer>