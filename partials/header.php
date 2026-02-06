<?php include("./constants/constant.php") ?>
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
      <nav id="mainSidebar" aria-label="Main Sidebar" class="w-[15%] bg-[#042a54] flex flex-col">
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

            <div id="dashboardMenu" class="hidden flex-col mt-1" role="menu" aria-hidden="true">
              <a href="<?php echo SITEURL ?>" class="block px-2 py-2 pl-8 text-white text-sm hover:bg-[#0f1b33] w-full" role="menuitem">Admin</a>
              <a href="#" class="block px-2 py-2 pl-8 text-white text-sm hover:bg-[#0f1b33] w-full" role="menuitem">Students</a>
              <a href="#" class="block px-2 py-2 pl-8 text-white text-sm hover:bg-[#0f1b33] w-full" role="menuitem">Parents</a>
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

            <div id="studentsMenu" class="hidden flex-col mt-1" role="menu" aria-hidden="true">
              <a href="<?php echo SITEURL ?>students.php" class="block px-2 py-2 pl-8 text-white w-full text-sm hover:bg-[#0f1b33] " role="menuitem">
                <i class="fa-solid fa-plus" aria-hidden="true"></i>
                Add Student
              </a>
              <a href="#" class="block px-2 py-2 pl-8 text-white w-full text-sm hover:bg-[#0f1b33] " role="menuitem">
                <i class="fa-solid fa-pen" aria-hidden="true"></i>
                Quick Registration
              </a>
              <a href="#" class="block px-2 py-2 pl-8 text-white w-full text-sm hover:bg-[#0f1b33] " role="menuitem">
                <i class="fa-solid fa-eye" aria-hidden="true"></i>
                View Students
              </a>
              <a href="#" class="block px-2 py-2 pl-8 text-white w-full text-sm hover:bg-[#0f1b33] " role="menuitem">
                <i class="fa-solid fa-pen-to-square" aria-hidden="true"></i>
                Update Student Data
              </a>
              <a href="#" class="block px-2 py-2 pl-8 text-white w-full text-sm hover:bg-[#0f1b33] " role="menuitem">
                <i class="fa-solid fa-file" aria-hidden="true"></i>
                Student Photos
              </a>
              <a href="#" class="block px-2 py-2 pl-8 text-white w-full text-sm hover:bg-[#0f1b33] " role="menuitem">
                <i class="fa-solid fa-list" aria-hidden="true"></i>
                Students Summary
              </a>
            </div>
          </div>

          <!-- Staff (collapsible) -->
          <div class="flex flex-col mt-1">
            <button
              class="sidebar-toggle flex items-center justify-between gap-3 w-full p-2 hover:bg-[#130f40] transition duration-200 text-white focus:outline-none"
              data-target="staffMenu"
              aria-expanded="false"
              type="button"
            >
              <div class="flex items-center gap-3">
                <i class="fas fa-user-tie text-white text-lg" aria-hidden="true"></i>
                <span class="font-semibold text-base tracking-wide capitalize">staff</span>
              </div>
              <i class="fas fa-chevron-down text-white text-sm transform transition-transform duration-200" aria-hidden="true"></i>
            </button>

            <div id="staffMenu" class="hidden flex-col mt-1" role="menu" aria-hidden="true">
              <a href="#" class="block px-2 py-2 pl-8 text-white w-full text-sm hover:bg-[#0f1b33] rounded" role="menuitem">
                <i class="fa-solid fa-plus" aria-hidden="true"></i>
                Add Staff
              </a>
              <a href="#" class="block px-2 py-2 pl-8 text-white w-full text-sm hover:bg-[#0f1b33] rounded" role="menuitem">
                <i class="fa-solid fa-eye" aria-hidden="true"></i>
                View Staff
              </a>
              <a href="#" class="block px-2 py-2 pl-8 text-white w-full text-sm hover:bg-[#0f1b33] rounded" role="menuitem">
                <i class="fa-solid fa-file" aria-hidden="true"></i>
                Staff Images
              </a>
              <a href="#" class="block px-2 py-2 pl-8 text-white w-full text-sm hover:bg-[#0f1b33] rounded" role="menuitem">
                <i class="fa-solid fa-user-plus" aria-hidden="true"></i>
                Enroll Staff Users
              </a>
              <a href="#" class="block px-2 py-2 pl-8 text-white w-full text-sm hover:bg-[#0f1b33] rounded" role="menuitem">
                <i class="fa-solid fa-users" aria-hidden="true"></i>
                View Staff Users
              </a>
              <a href="#" class="block px-2 py-2 pl-8 text-white w-full text-sm hover:bg-[#0f1b33] rounded" role="menuitem">
                <i class="fa-solid fa-briefcase" aria-hidden="true"></i>
                Staff Positions
              </a>
              <a href="#" class="block px-2 py-2 pl-8 text-white w-full text-sm hover:bg-[#0f1b33] rounded" role="menuitem">
                <i class="fa-solid fa-upload" aria-hidden="true"></i>
                Load
              </a>
            </div>
          </div>
        </div>
      </nav>

      <!-- Main Content -->
      <div id="mainContent" class="w-[85%] flex flex-col">
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
              <!-- O level dropdown starts-->
              <button id="olevelToggle" class="capitalize text-black-900 cursor-pointer flex items-center gap-2 focus:outline-none">
                <span>O' level</span>
                <i id="olevelCaret" class="fa-solid fa-chevron-down text-xs transition-transform duration-200"></i>
              </button>
              <!-- A level dropdown -->
              <button id="alevelToggle" class="capitalize text-black-900 px-3 cursor-pointer flex items-center gap-2 focus:outline-none">
                <span>A' level</span>
                <i id="alevelCaret" class="fa-solid fa-chevron-down text-xs transition-transform duration-200"></i>
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
          <div class="w-[1/4]">
            <div class="flex flex-col gap-2">
              <span class="uppercase text-blue-900 font-bold tracking-wide">
                <i class="fa-solid fa-book"></i>
                Subjects
              </span>
              <a href="<?php echo SITEURL; ?>add_subject.php" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
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
          <div class="w-[1/4]">
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
                <span class="capitalize">Rate Learner Competencies</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-eye"></i>
                <span class="capitalize">Competency Achievement Levels</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-eye"></i>
                <span class="capitalize">Competency Summary per Learner</span>
              </a>
            </div>
            <hr>
            <div class="flex flex-col gap-2">
              <span class="uppercase text-blue-900 font-bold tracking-wide">
                <i class="fa-solid fa-book"></i>
                Learner Projects
              </span>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">Add Project</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">Individual Projects</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span class="capitalize">Group Projects</span>
              </a>
              <!-- <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span class="capitalize">Group Projects</span>
              </a> -->
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span class="capitalize">project rubric</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span class="capitalize">projects score</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span class="capitalize">Projects report</span>
              </a>
              
            </div>
          </div>
          <div class="w-[1/4]">
            <div class="flex flex-col gap-2">
              <span class="uppercase text-blue-900 font-bold tracking-wide">
                <i class="fa-solid fa-book"></i>
               learning profile
              </span>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">academic profile</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">skills and talent profile</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span class="capitalize">behaviour and values</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-eye"></i>
                <span class="capitalize">career interest</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-eye"></i>
                <span class="capitalize">teacher observation</span>
              </a>
            </div>
            <hr>
            <div class="flex flex-col gap-2">
              <span class="uppercase text-blue-900 font-bold tracking-wide">
                <i class="fa-solid fa-book"></i>
                co-curricular activities
              </span>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">clubs and society</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">games and sports</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span class="capitalize">community service learning</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span class="capitalize">participation records</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span class="capitalize">activity perfomance</span>
              <!-- </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span class="capitalize">community service learning</span>
              </a> -->
              
            </div>
          </div>
          <div class="w-[1/4]">
            <div class="flex flex-col gap-2">
              <span class="uppercase text-blue-900 font-bold tracking-wide">
              <i class="fa-solid fa-file"></i>
                attendance and participation
                
              </span>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">daily attendance</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">lesson attendance</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span class="capitalize">activity attendance</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-file"></i>
                <span class="capitalize">participation logs</span>
              </a>
            </div>
            <hr>
            <div class="flex flex-col gap-2">
              <span class="uppercase text-blue-900 font-bold tracking-wide">
                <i class="fa-solid fa-book"></i>
                Assessment Reports
              </span>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">Learner Progress Report</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">Competency Achievement Report</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span class="capitalize">Parent Summary Report</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span class="capitalize">Class Performance Summary</span>
              </a>
              
            </div>
          </div>
         </div>
        <!-- dropdowns ends-->
         <!-- dropdowns starts-->
         <div id="alevelDropdown" class="hidden absolute w-[100%] bg-[#ffffff] flex px-10 py-4 gap-6 text-sm text-gray-800 shadow" style="top: 48px; left: 15%; width: 85%;">
          <div class="w-[1/5]">
            <div class="flex flex-col gap-2">
              <span class="uppercase text-blue-900 font-bold tracking-wide">
                <i class="fa-solid fa-book"></i>
                Subject & Combination Management
              </span>
              <a href="<?php echo SITEURL; ?>add_subject.php" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">add subject</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">Create Subject Combinations</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span class="capitalize">Define Core Subjects in Combination</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span class="capitalize">Define Allowed Subsidiary Subjects</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-eye"></i>
                <span class="capitalize">view all Combination</span>
              </a>
            </div>
            <hr>
            <div class="flex flex-col gap-2">
              <span class="uppercase text-blue-900 font-bold tracking-wide">
                <i class="fa-solid fa-book"></i>
                Student Combination Assignment
              </span>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">Assign Combination to Student</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">Edit Student Combination</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">Add Subsidiary Subjects</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">Auto-Attach General Paper</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span class="capitalize">View Student Subject Load (all 5 subjects)</span>
              </a>
              
            </div>
          </div>
          <div class="w-[1/5]">
            <div class="flex flex-col gap-2">
              <span class="uppercase text-blue-900 font-bold tracking-wide">
                <i class="fa-solid fa-book"></i>
                Practicals & Coursework
              </span>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">Record Practical Scores</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">Coursework Marks</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span class="capitalize">Research / Fieldwork Marks</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-eye"></i>
                <span class="capitalize">Practical Attendance</span>
              </a>
              
            </div>
            <hr>
            <div class="flex flex-col gap-2">
              <span class="uppercase text-blue-900 font-bold tracking-wide">
                <i class="fa-solid fa-book"></i>
                Continuous Assessment
              </span>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">Monthly Tests</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">Mid-term Assessment</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span class="capitalize">Class Activities Scores</span>
              </a>
      
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span class="capitalize">Teacher Comments</span>
              </a>
              <!-- <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span class="capitalize">projects score</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span class="capitalize">Projects report</span>
              </a> -->
              
            </div>
          </div>
          <div class="w-[1/5]">
            <div class="flex flex-col gap-2">
              <span class="uppercase text-blue-900 font-bold tracking-wide">
                <i class="fa-solid fa-book"></i>
               Examinations
              </span>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">Set Exams</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">Enter Marks</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span class="capitalize">Grade Boundaries Setup</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-eye"></i>
                <span class="capitalize">Results Analysis</span>
              </a>
              <!-- <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-eye"></i>
                <span class="capitalize">teacher observation</span>
              </a> -->
            </div>
            <hr>
            <div class="flex flex-col gap-2">
              <span class="uppercase text-blue-900 font-bold tracking-wide">
                <i class="fa-solid fa-book"></i>
                Academic & Career Profile
              </span>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">Student Academic Profile</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">Subject Strengths & Weaknesses</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span class="capitalize">Career Guidance Notes</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span class="capitalize">University Pathway Suggestions</span>
              </a>
              <!-- <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span class="capitalize">activity perfomance</span> -->
              <!-- </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span class="capitalize">community service learning</span>
              </a> -->
              
            </div>
          </div>
          <div class="w-[1/5]">
            <div class="flex flex-col gap-2">
              <span class="uppercase text-blue-900 font-bold tracking-wide">
              <i class="fa-solid fa-file"></i>
                Research & Projects
                
              </span>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">Individual Research Projects</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">Group Projects</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span class="capitalize">Assign Supervisor</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-file"></i>
                <span class="capitalize">Project Rubrics</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-file"></i>
                <span class="capitalize">Project Results</span>
              </a>
            </div>
            <hr>
            <div class="flex flex-col gap-2">
              <span class="uppercase text-blue-900 font-bold tracking-wide">
                <i class="fa-solid fa-book"></i>
                Attendance
              </span>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">Class Attendance</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">Subject-based Attendance</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span class="capitalize">Practical Attendance</span>
              </a>
              
            </div>
          </div>
          <div class="w-[1/5]">
            <div class="flex flex-col gap-2">
              <span class="uppercase text-blue-900 font-bold tracking-wide">
              <i class="fa-solid fa-file"></i>
                Co-Curricular
                
              </span>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">Clubs & Leadership Roles</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">Sports Participation</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span class="capitalize">Community Service</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-file"></i>
                <span class="capitalize">Activity Records</span>
              </a>
              <!-- <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-file"></i>
                <span class="capitalize">Project Results</span>
              </a> -->
            </div>
            <hr>
            <div class="flex flex-col gap-2">
              <span class="uppercase text-blue-900 font-bold tracking-wide">
                <i class="fa-solid fa-book"></i>
                Reports & Transcripts
              </span>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">Term Report</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-plus-circle"></i>
                <span class="capitalize">Mock Report</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span class="capitalize">Combination Performance Report</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span class="capitalize">Full A-Level Transcript</span>
              </a>
              <a href="#" class="flex items-center gap-1 text-gray-700 hover:text-blue-900 transition-colors">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span class="capitalize">University Application Summary</span>
              </a>
              
            </div>
          </div>
         </div>
        <!-- dropdowns ends-->

        <!-- Inline JS: consolidated, accessible, and shorter -->
        <script>
          document.addEventListener('DOMContentLoaded', function () {
            var TRANS_DUR = 320; // must match CSS transition timing (ms)

            function ensureAnim(menu) {
              if (!menu) return;
              menu.classList.add('anim-dropdown');
            }

            function openAnimated(menu, caret) {
              if (!menu) return;
              ensureAnim(menu);
              menu.classList.remove('hidden');
              // trigger a frame then add open to animate
              requestAnimationFrame(function () { menu.classList.add('open'); });
              menu.setAttribute('aria-hidden', 'false');
              if (caret) caret.classList.add('rotate-180');
            }

            function closeAnimated(menu, caret) {
              if (!menu) return;
              ensureAnim(menu);
              menu.classList.remove('open');
              menu.setAttribute('aria-hidden', 'true');
              if (caret) caret.classList.remove('rotate-180');
              // after animation finishes, hide from layout
              setTimeout(function () { menu.classList.add('hidden'); }, TRANS_DUR + 30);
            }

            var currentOpenMenu = null;

            // Sidebar toggles
            document.querySelectorAll('.sidebar-toggle').forEach(function (btn) {
              var targetId = btn.getAttribute('data-target');
              var menu = document.getElementById(targetId);
              var caret = btn.querySelector('.fa-chevron-down');

              btn.addEventListener('click', function (e) {
                e.preventDefault();
                var isClosed = menu.classList.contains('hidden');

                // close previously opened menu
                if (currentOpenMenu && currentOpenMenu !== menu && !currentOpenMenu.classList.contains('hidden')) {
                  closeAnimated(currentOpenMenu);
                  // find previous button and update attributes
                  var previousBtn = document.querySelector('[data-target="' + currentOpenMenu.id + '"]');
                  if (previousBtn) previousBtn.setAttribute('aria-expanded', 'false');
                }

                if (isClosed) {
                  openAnimated(menu, caret);
                  btn.setAttribute('aria-expanded', 'true');
                  currentOpenMenu = menu;
                } else {
                  closeAnimated(menu, caret);
                  btn.setAttribute('aria-expanded', 'false');
                  currentOpenMenu = null;
                }
              });

              btn.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                  e.preventDefault();
                  btn.click();
                }
              });
            });

            // Sidebar global toggle: collapse to icons-only
            var sidebarToggle = document.getElementById('sidebarToggle');
            var sidebar = document.getElementById('mainSidebar');
            var mainContent = document.getElementById('mainContent');
            if (sidebarToggle && sidebar && mainContent) {
              sidebarToggle.addEventListener('click', function () {
                var collapsed = sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded', collapsed);
                sidebarToggle.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
                sidebar.setAttribute('aria-hidden', collapsed ? 'true' : 'false');

                // Close any open sidebar submenu when collapsing
                if (collapsed && currentOpenMenu) {
                  closeAnimated(currentOpenMenu);
                  currentOpenMenu = null;
                  document.querySelectorAll('.sidebar-toggle').forEach(function (b) {
                    b.setAttribute('aria-expanded', 'false');
                    var cr = b.querySelector('.fa-chevron-down');
                    if (cr) cr.classList.remove('rotate-180');
                  });
                }
              });
            }

            // Helper to wire hover menus (O' level & A' level)
            function wireHoverMenu(btn, menu, caret) {
              if (!btn || !menu) return;
              var hideTimeout;
              btn.addEventListener('mouseenter', function () {
                clearTimeout(hideTimeout);
                openAnimated(menu, caret);
                btn.setAttribute('aria-expanded', 'true');
              });
              btn.addEventListener('mouseleave', function () {
                hideTimeout = setTimeout(function () {
                  closeAnimated(menu, caret);
                  btn.setAttribute('aria-expanded', 'false');
                }, 180);
              });
              menu.addEventListener('mouseenter', function () { clearTimeout(hideTimeout); openAnimated(menu, caret); });
              menu.addEventListener('mouseleave', function () { closeAnimated(menu, caret); btn.setAttribute('aria-expanded', 'false'); });
            }

            wireHoverMenu(document.getElementById('olevelToggle'), document.getElementById('olevelDropdown'), document.getElementById('olevelCaret'));
            wireHoverMenu(document.getElementById('alevelToggle'), document.getElementById('alevelDropdown'), document.getElementById('alevelCaret'));
          });
        </script>