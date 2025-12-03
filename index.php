<!doctype html>
<html>
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- tailwind cdn -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <!-- fontawesome cdn -->
    <script src="https://kit.fontawesome.com/78e0d6a352.js" crossorigin="anonymous"></script>
    <title>School Management System - SMS</title>
    <link rel="stylesheet" href="css/styles.css" />
  </head>
  <body class="bg-[#e5f6e5] p-2">
    <div class="flex">
      <!-- Sidebar placeholder -->
      <div class="w-[15%]"></div>

      <!-- Navbar -->
      <div class="w-[85%] bg-white h-12 flex items-center justify-evenly px-4 shadow">
        <!-- Left: Menu + Logo -->
        <div class="flex items-center gap-3">
          <!-- Hamburger menu -->
          <button class="text-gray-700 text-xl" style="padding:0 10px">
            <i class="fa fa-bars"></i>
          </button>

          <!-- Logo + Welcome text -->
          <div class="flex items-center gap-2">
            <i class="fa-solid fa-user-graduate text-gray-600 text-lg"></i>
            <span class="font-bold capitalize">Welcome To</span>
            <span class="text-gray-600">School Management System</span>
          </div>
        </div>

        <!-- Center: Search bar -->
        <div class="flex-1 flex justify-center">
          <input type="text" placeholder="Search Here ..." class="w-[70%] h-6 px-4 border border-gray-300 rounded-full bg-[#e5f6e5] outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent shadow-sm transition duration-200" />
        </div>

        <!-- Right: Icons + Profile -->
        <div class="flex items-center gap-4">
          <!-- Messages -->
          <button class="relative text-gray-600">
            <i class="fa-regular fa-envelope"></i>
            <span class="absolute -top-1 -right-2 bg-blue-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">5</span>
          </button>

          <!-- Notifications -->
          <button class="relative text-gray-600">
            <i class="fa-regular fa-bell"></i>
            <span class="absolute -top-1 -right-2 bg-orange-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">8</span>
          </button>

          <!-- Language selector -->
          <select class="border rounded px-2 py-1 text-sm">
            <option>English</option>
            <option>French</option>
          </select>

          <!-- User profile -->
          <div class="flex items-center gap-2">
            <img src="images/profil.jpg" alt="User" class="w-8 h-8 rounded-full" />
            <div class="text-sm">
              <p class="font-semibold text-sm">katojkalemba</p>
              <p class="text-gray-500 text-xs">Admin</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
