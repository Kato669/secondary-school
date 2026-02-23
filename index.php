<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>



<?php include('partials/header.php') ?>

        <!-- Main Body Placeholder -->
        <div class="flex-1 py-2 px-4">
          <!-- Your main content goes here -->
          <div class="flex items-center gap-2 text-sm text-black capitalize">
            <span>home</span>
            <i class="fa-solid fa-arrow-right text-gray-500 text-xs"></i>
            <span>admin</span>
          </div>
          <div class="flex gap-4 mt-4">
            <div class="w-full bg-white p-5 rounded shadow flex">
              <div class="flex flex-col gap-1 items-center">
                <span><i class="fa-solid fa-users text-green-900 text-xl"></i></span>
                <span class="text-sm capitalize">students</span>
              </div>
              <div class="border-l border-gray-300 mx-4"></div>
              <div class="flex flex-col gap-1 items-center justify-center ml-4">
                <span class="text-4xl font-bold">500</span>
              </div>
            </div>
            <div class="w-full bg-white p-5 rounded shadow flex">
              <div class="flex flex-col gap-1 items-center">
                <span><i class="fa-solid fa-chalkboard-user text-blue-900 text-xl"></i></span>
                <span class="text-sm capitalize">teachers</span>
              </div>
              <div class="border-l border-gray-300 mx-4"></div>
              <div class="flex flex-col gap-1 items-center justify-center ml-4">
                <span class="text-4xl font-bold">50</span>
              </div>
            </div>
            <div class="w-full bg-white p-5 rounded shadow flex">
              <div class="flex flex-col gap-1 items-center">
                <span><i class="fa-solid fa-book-open text-yellow-900 text-xl"></i></span>
                <span class="text-sm capitalize">subjects</span>
              </div>
              <div class="border-l border-gray-300 mx-4"></div>
              <div class="flex flex-col gap-1 items-center justify-center ml-4">
                <span class="text-4xl font-bold">30</span>
              </div>
            </div>
            <div class="w-full bg-white p-5 rounded shadow flex">
              <div class="flex flex-col gap-1 items-center">
                <span><i class="fa-solid fa-dollar text-red-900 text-xl"></i></span>
                <span class="text-sm capitalize">earnings</span>
              </div>
              <div class="border-l border-gray-300 mx-4"></div>
              <div class="flex flex-col gap-1 items-center justify-center ml-4">
                <span class="text-4xl font-bold">10</span>
              </div>
            </div>
          </div>
          <!-- charts go here -->
          <div class="flex gap-4 mt-4">
            <div class="w-full bg-white p-5 rounded shadow">
              <canvas id="myChart" width="400" height="300"></canvas>
            </div>
            <div class="w-full bg-white p-5 rounded shadow">
              <canvas id="myChart2" width="400" height="300"></canvas>
            </div>
          </div>
          <!-- notice goes here -->
          <div class="flex gap-4 mt-4">
            <div class="w-full bg-white p-5 rounded shadow">
              <canvas id="myChart" width="400" height="300"></canvas>
            </div>
            <div class="w-full bg-white p-5 rounded shadow scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100 h-[300px] overflow-y-scroll">
              <div class="notification flex flex-col gap-3">
                <h3 class="font-bold mb-3 capitalize text-lg">Notice board</h3>
                <div class="notification-item p-3 bg-gray-100 rounded">
                  <h3 class="bg-[#40dfcd] text-center rounded-full shadow-sm p-1 w-[23%]">23 Jan 2026</h3>
                  <p class="text-sm py-1">The system will undergo maintenance on Saturday at 2 AM.</p>
                  <span class="text-xs text-gray-500">2 hours ago</span>
                </div>
                <div class="notification-item p-3 bg-gray-100 rounded">
                  <h3 class="bg-[#40dfcd] text-center rounded-full shadow-sm p-1 w-[23%]">15 Feb 2026</h3>
                  <p class="text-sm py-1">New features have been added to enhance user experience.</p>
                  <span class="text-xs text-gray-500">1 day ago</span>  
                </div>
                <div class="notification-item p-3 bg-gray-100 rounded">
                  <h3 class="bg-[#40dfcd] text-center rounded-full shadow-sm p-1 w-[23%]">10 Mar 2026</h3>
                  <p class="text-sm py-1">Please update your profile information by the end of this week.</p>
                  <span class="text-xs text-gray-500">3 days ago</span> 
                </div>
                <div class="notification-item p-3 bg-gray-100 rounded">
                  <h3 class="bg-[#40dfcd] text-center rounded-full shadow-sm p-1 w-[23%]">10 Mar 2026</h3>
                  <p class="text-sm py-1">Please update your profile information by the end of this week.</p>
                  <span class="text-xs text-gray-500">3 days ago</span> 
                </div>
              </div>
          </div>
        
      </div>
    </div>

    <!-- Dropdown JS -->
<?php include('partials/footer.php') ?>   


 
