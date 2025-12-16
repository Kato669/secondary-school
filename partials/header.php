<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>School Management System - SMS</title>

    <!-- Tailwind CDN (deferred) -->
    <script defer src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome (deferred) -->
    <script defer src="https://kit.fontawesome.com/78e0d6a352.js" crossorigin="anonymous"></script>

    <!-- Use root‑relative paths to avoid include-location issues -->
    <link rel="stylesheet" href="/sms/css/styles.css" />
  </head>
  <body class="bg-[#e5f6e5]">
    <div class="flex min-h-screen">

      <!-- Sidebar -->
      <nav aria-label="Main Sidebar" class="w-[15%] bg-[#042a54] flex flex-col">
        <!-- Top bar / Hamburger -->
        <div class="h-12 bg-[#fbc400] flex items-center justify-end px-4 shadow">
          <button id="sidebarToggle" class="text-white text-xl cursor-pointer px-2" aria-label="Toggle sidebar" aria-controls="sidebarMenu" aria-expanded="true" type="button">
            <i class="fas fa-bars" aria-hidden="true"></i>
          </button>
        </div>

        <!-- Sidebar Menu -->
        <div id="sidebarMenu" class="flex-1 flex flex-col" role="menu">
          <!-- Dashboard (collapsible) -->
          <div class="flex flex-col">
            <button
              class="sidebar-toggle flex items-center justify-between gap-3 w-full p-2 hover:bg-[#130f40] transition duration-200 text-white focus:outline-none"
              data-target="dashboardMenu"
              aria-expanded="false"
              type="button">
              <div class="flex items-center gap-3">
                <i class="fas fa-tachometer-alt text-white text-lg" aria-hidden="true"></i>
                <span class="font-semibold text-base tracking-wide capitalize">dashboard</span>
              </div>
              <i class="fas fa-chevron-down text-white text-sm transform transition-transform duration-200" aria-hidden="true"></i>
            </button>

            <div id="dashboardMenu" class="hidden flex-col ml-6 mt-1" role="menu" aria-hidden="true">
              <a href="#" class="block p-2 text-white text-sm hover:bg-[#0f1b33] rounded" role="menuitem">Admin</a>
              <a href="#" class="block p-2 text-white text-sm hover:bg-[#0f1b33] rounded" role="menuitem">Students</a>
              <a href="#" class="block p-2 text-white text-sm hover:bg-[#0f1b33] rounded" role="menuitem">Parents</a>
            </div>
          </div>

          <!-- Students (collapsible) -->
          <div class="flex flex-col mt-1">
            <button
              class="sidebar-toggle flex items-center justify-between gap-3 w-full p-2 hover:bg-[#130f40] transition duration-200 text-white focus:outline-none"
              data-target="studentsMenu"
              aria-expanded="false"
              type="button"
            >
              <div class="flex items-center gap-3">
                <i class="fas fa-users text-white text-lg" aria-hidden="true"></i>
                <span class="font-semibold text-base tracking-wide capitalize">students</span>
              </div>
              <i class="fas fa-chevron-down text-white text-sm transform transition-transform duration-200" aria-hidden="true"></i>
            </button>

            <div id="studentsMenu" class="hidden flex-col ml-6 mt-1" role="menu" aria-hidden="true">
              <a href="#" class="block p-2 text-white w-full text-sm hover:bg-[#0f1b33] rounded" role="menuitem">
                <i class="fa-solid fa-plus" aria-hidden="true"></i>
                Add Student
              </a>
              <a href="#" class="block p-2 text-white text-sm hover:bg-[#0f1b33] rounded" role="menuitem">
                <i class="fa-solid fa-pen" aria-hidden="true"></i>
                Quick Registration
              </a>
              <a href="#" class="block p-2 text-white text-sm hover:bg-[#0f1b33] rounded" role="menuitem">
                <i class="fa-solid fa-eye" aria-hidden="true"></i>
                View Students
              </a>
              <a href="#" class="block p-2 text-white text-sm hover:bg-[#0f1b33] rounded" role="menuitem">
                <i class="fa-solid fa-pen-to-square" aria-hidden="true"></i>
                Update Student Data
              </a>
              <a href="#" class="block p-2 text-white text-sm hover:bg-[#0f1b33] rounded" role="menuitem">
                <i class="fa-solid fa-file" aria-hidden="true"></i>
                Student Photos
              </a>
              <a href="#" class="block p-2 text-white text-sm hover:bg-[#0f1b33] rounded" role="menuitem">
                <i class="fa-solid fa-list" aria-hidden="true"></i>
                Students Summary
              </a>
            </div>
          </div>
        </div>
      </nav>

      <!-- Main Content -->
      <div class="w-[85%] flex flex-col">
        <!-- Top Bar -->
        <header class="bg-white h-12 flex items-center justify-between px-5 shadow" role="banner">
          <!-- Left: Logo / Welcome -->
          <div class="flex items-center gap-3">
            <i class="fa-solid fa-user-graduate text-gray-600 text-lg" aria-hidden="true"></i>
            <span class="font-bold capitalize">Welcome To</span>
            <span class="text-gray-600">School Management System</span>
          </div>

          <!------------------------------------ Center: Search ------------------------->
          <div class="flex justify-center">
            <div class="flex-1 flex items-center justify-center font-bold text-sm">
              <button id="olevelToggle" class="capitalize text-black-900 cursor-pointer flex items-center gap-2 focus:outline-none">
                <span>O' level</span>
                <i id="olevelCaret" class="fa-solid fa-chevron-down text-xs transition-transform duration-200"></i>
              </button>
            </div>
          </div>

          <!-- Right: Icons & Profile -->
          <div class="flex items-center gap-4">
            <!-- Messages -->
            <button class="relative text-gray-600" aria-label="Messages">
              <i class="fa-regular fa-envelope" aria-hidden="true"></i>
              <span class="absolute -top-1 -right-2 bg-blue-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">5</span>
            </button>

            <!-- Notifications -->
            <button class="relative text-gray-600" aria-label="Notifications">
              <i class="fa-regular fa-bell" aria-hidden="true"></i>
              <span class="absolute -top-1 -right-2 bg-orange-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">8</span>
            </button>

            <!-- Language Selector -->
            <label class="sr-only" for="langSelect">Language</label>
            <select id="langSelect" class="border rounded px-2 py-1 text-sm" aria-label="Select language">
              <option>English</option>
              <option>French</option>
            </select>

            <!-- Profile -->
            <div class="flex items-center gap-2">
              <img src="/sms/images/profil.jpg" alt="Admin profile photo" class="w-8 h-8 rounded-full" />
              <div class="text-sm">
                <p class="font-semibold text-sm">katojkalemba</p>
                <p class="text-gray-500 text-xs">Admin</p>
              </div>
            </div>
          </div>
        </header>



        <!-- dropdowns starts-->
         <div id="olevelDropdown" class="hidden absolute w-[100%] bg-[#ffffff] flex px-10 py-4 gap-6 text-sm text-gray-800 shadow" style="top: 48px; left: 15%; width: 85%;">
          <div class="w-[1/5]">
            <div class="flex flex-col gap-2">
              <span class="uppercase text-blue-900 font-bold tracking-wide">
                <i class="fa-solid fa-book"></i>
                Subjects
              </span>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">add subject</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">assign subject to class</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span class="capitalize">subject teacher</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-eye"></i>
                <span class="capitalize">view subject teacher</span>
              </a>
            </div>
            <hr>
            <div class="flex flex-col gap-2">
              <span class="uppercase text-blue-900 font-bold tracking-wide">
                <i class="fa-solid fa-book"></i>
                continous assessment
              </span>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">Enter Continuous Assessment</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">practical assessment</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">weekly/monthly assessment</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">assessment rubrics</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span class="capitalize">teacher score sheets</span>
              </a>
              
            </div>
          </div>
          <div class="w-[1/5]">
            <div class="flex flex-col gap-2">
              <span class="uppercase text-blue-900 font-bold tracking-wide">
                <i class="fa-solid fa-book"></i>
                competency tracking
              </span>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">define competency</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">assign competency to subjetcs</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span class="capitalize">subject teacher</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-eye"></i>
                <span class="capitalize">view subject teacher</span>
              </a>
            </div>
            <hr>
            <div class="flex flex-col gap-2">
              <span class="uppercase text-blue-900 font-bold tracking-wide">
                <i class="fa-solid fa-book"></i>
                continous assessment
              </span>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">practical assessment</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">weekly/monthly assessment</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span class="capitalize">teacher score</span>
              </a>
              
            </div>
          </div>
          <div class="w-[1/5]">
            <div class="flex flex-col gap-2">
              <span class="uppercase text-blue-900 font-bold tracking-wide">
                <i class="fa-solid fa-book"></i>
                Subjects
              </span>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">add subject</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">assign subject to class</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span class="capitalize">subject teacher</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-eye"></i>
                <span class="capitalize">view subject teacher</span>
              </a>
            </div>
            <hr>
            <div class="flex flex-col gap-2">
              <span class="uppercase text-blue-900 font-bold tracking-wide">
                <i class="fa-solid fa-book"></i>
                continous assessment
              </span>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">practical assessment</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">weekly/monthly assessment</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span class="capitalize">teacher score</span>
              </a>
              
            </div>
          </div>
          <div class="w-[1/5]">
            <div class="flex flex-col gap-2">
              <span class="uppercase text-blue-900 font-bold tracking-wide">
                <i class="fa-solid fa-book"></i>
                Subjects
              </span>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">add subject</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">assign subject to class</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span class="capitalize">subject teacher</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-eye"></i>
                <span class="capitalize">view subject teacher</span>
              </a>
            </div>
            <hr>
            <div class="flex flex-col gap-2">
              <span class="uppercase text-blue-900 font-bold tracking-wide">
                <i class="fa-solid fa-book"></i>
                continous assessment
              </span>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">practical assessment</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">weekly/monthly assessment</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span class="capitalize">teacher score</span>
              </a>
              
            </div>
          </div>
          <div class="w-[1/5]">
            <div class="flex flex-col gap-2">
              <span class="uppercase text-blue-900 font-bold tracking-wide">
                <i class="fa-solid fa-book"></i>
                Subjects
              </span>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">add subject</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">assign subject to class</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span class="capitalize">subject teacher</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-eye"></i>
                <span class="capitalize">view subject teacher</span>
              </a>
            </div>
            <hr>
            <div class="flex flex-col gap-2">
              <span class="uppercase text-blue-900 font-bold tracking-wide">
                <i class="fa-solid fa-book"></i>
                continous assessment
              </span>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">practical assessment</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">weekly/monthly assessment</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span class="capitalize">teacher score</span>
              </a>
              
            </div>
          </div>
         </div>
        <!-- dropdowns ends-->

        <!-- Inline JS: consolidated, accessible, and shorter -->
<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Generic sidebar toggles: use data-target to find menu and caret (last child)
    document.querySelectorAll('.sidebar-toggle').forEach(function (btn) {
      var targetId = btn.getAttribute('data-target');
      var menu = document.getElementById(targetId);
      var caret = btn.querySelector('.fa-chevron-down');

      btn.addEventListener('click', function (e) {
        e.preventDefault();
        var isHidden = menu.classList.contains('hidden');
        menu.classList.toggle('hidden', !isHidden ? true : false);
        menu.setAttribute('aria-hidden', isHidden ? 'false' : 'true');
        btn.setAttribute('aria-expanded', isHidden ? 'true' : 'false');

        if (caret) {
          caret.classList.toggle('rotate-180', isHidden);
        }
      });

      // keyboard accessibility (Enter/Space)
      btn.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          btn.click();
        }
      });
    });

    // Sidebar global toggle (optional behavior)
    var sidebarToggle = document.getElementById('sidebarToggle');
    var sidebarMenu = document.getElementById('sidebarMenu');
    if (sidebarToggle && sidebarMenu) {
      sidebarToggle.addEventListener('click', function () {
        var collapsed = sidebarMenu.classList.toggle('hidden');
        sidebarToggle.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
        sidebarMenu.setAttribute('aria-hidden', collapsed ? 'true' : 'false');
      });
    }

    // O' level dropdown toggle (hover behavior)
    var oBtn = document.getElementById('olevelToggle');
    var oMenu = document.getElementById('olevelDropdown');
    var oCaret = document.getElementById('olevelCaret');
    var hideTimeout;
    
    if (oBtn && oMenu) {
      // Show on mouse enter
      oBtn.addEventListener('mouseenter', function () {
        clearTimeout(hideTimeout);
        oMenu.classList.remove('hidden');
        oMenu.setAttribute('aria-hidden', 'false');
        oBtn.setAttribute('aria-expanded', 'true');
        if (oCaret) {
          oCaret.classList.add('rotate-180');
        }
      });

      // Hide on mouse leave (from button) with delay
      oBtn.addEventListener('mouseleave', function () {
        hideTimeout = setTimeout(function () {
          oMenu.classList.add('hidden');
          oMenu.setAttribute('aria-hidden', 'true');
          oBtn.setAttribute('aria-expanded', 'false');
          if (oCaret) {
            oCaret.classList.remove('rotate-180');
          }
        }, 200);
      });

      // Keep menu visible when hovering over it
      oMenu.addEventListener('mouseenter', function () {
        clearTimeout(hideTimeout);
        oMenu.classList.remove('hidden');
        oMenu.setAttribute('aria-hidden', 'false');
      });

      oMenu.addEventListener('mouseleave', function () {
        oMenu.classList.add('hidden');
        oMenu.setAttribute('aria-hidden', 'true');
        oBtn.setAttribute('aria-expanded', 'false');
        if (oCaret) {
          oCaret.classList.remove('rotate-180');
        }
      });
    }
  });
</script>