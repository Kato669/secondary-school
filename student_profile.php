<?php include("partials/header.php");
if (session_status() === PHP_SESSION_NONE) session_start();

// validate student_id
$student_id = filter_input(INPUT_GET, 'student_id', FILTER_VALIDATE_INT);
if (!$student_id) {
    header("Location:".SITEURL."view_student.php");
    exit();
}

// fetch student and related info safely
$stmt = mysqli_prepare($conn, "SELECT s.*, p.parent_name, p.phone_1, p.email AS parent_email, p.address AS parent_address, p.gender AS parent_gender, p.relationship AS relationship, sai.class_id, sai.stream_id, sai.term, sai.year_of_study, sai.entry_status, sai.residence_status, sai.school_pay FROM students s LEFT JOIN student_parent p ON s.student_id = p.student_id LEFT JOIN student_additional_info sai ON s.student_id = sai.student_id WHERE s.student_id = ? LIMIT 1");
if (!$stmt) {
    header("Location:".SITEURL."view_student.php");
    exit();
}
mysqli_stmt_bind_param($stmt, 'i', $student_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// fetch class/stream names for display
$class_name = '';
$stream_name = '';
if ($student) {
    if (!empty($student['class_id'])) {
        $cr = mysqli_query($conn, 'SELECT class_name FROM classes WHERE class_id = ' . (int)$student['class_id'] . ' LIMIT 1');
        if ($cr && mysqli_num_rows($cr)) {
            $crow = mysqli_fetch_assoc($cr);
            $class_name = $crow['class_name'];
        }
    }
    if (!empty($student['stream_id'])) {
        $sr = mysqli_query($conn, 'SELECT stream_name FROM streams WHERE stream_id = ' . (int)$student['stream_id'] . ' LIMIT 1');
        if ($sr && mysqli_num_rows($sr)) {
            $srow = mysqli_fetch_assoc($sr);
            $stream_name = $srow['stream_name'];
        }
    }
}

if (!$student) {
    header("Location:".SITEURL."view_student.php");
    exit();
}
?>
<div class="container m-3 p-3">
    <!-- headers -->
    <div class="header-top flex flex-row align-items-center gap-2">
        <a href="<?php echo SITEURL; ?>" class="capitalize text-sm">home</a>
        <i class="fa-solid fa-angle-right text-sm text-gray-500"></i>
        <a href="<?php echo SITEURL ?>view_student.php" class="capitalize text-sm">students</a>
        <i class="fa-solid fa-angle-right text-sm text-gray-500"></i>
        <a href="" class="capitalize text-sm">student profile</a>
    </div>
    <!-- body section -->
    <div class="body-section border border-[#042a54] my-5 mr-3">
        <!-- top header begins -->
        <div class="top-bar bg-[#042a54] p-3 flex flex-row align-items-center gap-3">
            <i class="fa-solid fa-users text-white pt-2"></i>
            <h2 class="text-md uppercase font-bold text-white">Student Profile for <?php echo htmlspecialchars($student['student_name']); ?></h2>
        </div>
        <!-- top header ends -->
        <div class="header-nav flex flex-row align-items-center gap-3 p-2 justify-between">
            <div class=""></div>
            <div class="">
                <a href="" class="text-sm capitalize text-[#ffffff] bg-blue-900 px-3 py-2 my-3 mx-1 hover:bg-blue-800">
                    <i class="fa-solid fa-money-bill-wave"></i>
                    fees
                </a>
                <a href="" class="text-sm capitalize text-[#ffffff] bg-blue-900 px-3 py-2 my-3 mx-1 hover:bg-blue-800">
                    <i class="fa-solid fa-file-lines"></i>
                    report
                </a>
            </div>
        </div>
        <!-- top header ends -->
         <!-- image section -->
        <div class="container-image flex flex-row justify-between p-3 align-items-center">
            <div class="w-1/2">
                <img class="img-fluid border rounded " src="https://images.unsplash.com/photo-1659444003277-6cb0a5ffc8bd?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8YmxhY2slMjBnZW50bGVtYW4lMjBpbiUyMHN1aXR8ZW58MHx8MHx8fDA%3D" alt="" srcset="" style="height: 200px; width: 200px; border-radius: 10%; object-fit: cover;"> 
            </div>
            <div class="w-1/2">
               <hr class="border-white-700 my-3">
               <div class="flex justify-between align-items-center">
                    <div class="uppercase text-blue-900">
                        Student biodata
                    </div>
                    <div class="edit-btn">
                        <a href="" class="text-sm uppercase text-[#ffffff] bg-blue-900 px-3 py-2 my-3 mx-1 hover:bg-blue-800">
                            <i class="fa-solid fa-pen-to-square"></i>
                            edit info
                        </a>
                    </div>
                </div>
               <hr class="border-white-700 my-3">
                <div class="flex flex-col gap-2">
                        <div class="flex flex-row gap-2 justify-between">
                            <div class="font-bold text-black-500 capitalize">Full name:</div>
                            <div class="capitalize text-black-500"><?php echo htmlspecialchars($student['student_name'] ?? ''); ?></div>
                        </div>
                        <div class="flex flex-row gap-2 justify-between">
                            <div class="font-bold text-black-500 capitalize"> lin:</div>
                            <div class="uppercase text-black-500"><?php echo htmlspecialchars($student['lin'] ?? ''); ?></div>
                        </div>
                        <div class="flex flex-row gap-2 justify-between">
                            <div class="font-bold text-black-500 capitalize">Gender:</div>
                            <div class="capitalize text-black-500"><?php echo htmlspecialchars($student['gender'] ?? ''); ?></div>
                        </div>
                        <div class="flex flex-row gap-2 justify-between">
                            <div class="font-bold text-black-500 capitalize">   date of birth:</div>
                            <div class="capitalize text-black-500"><?php echo htmlspecialchars($student['dob'] ?? ''); ?></div>
                        </div>
                        <div class="flex flex-row gap-2 justify-between">
                            <div class="font-bold text-black-500 capitalize">address:</div>
                            <div class="capitalize text-black-500"><?php echo htmlspecialchars($student['parent_address'] ?? ''); ?></div>
                        </div>
                </div>
            </div>
            <!-- parent and other information -->
            
        </div>
         <h1 class="uppercase text-center py-5 text-2xl font-bold">more information</h1>
        <div class="flex flex-row justify-between p-3 gap-6">
            <div class="w-1/2">
                <hr class="border-gray-300 my-3">
                <div class="flex flex-row items-center justify-between mb-3">
                    <h3 class="uppercase text-blue-900 font-semibold">Parent/Guardian Information</h3>
                    <a href="" class="text-sm uppercase text-white bg-blue-900 px-3 py-2 hover:bg-blue-800 rounded transition">
                        <i class="fa-solid fa-pen-to-square"></i>
                        edit info
                    </a>
                </div>
                <hr class="border-gray-300 my-3">
                <div class="flex flex-col gap-2">
                    <div class="flex flex-row justify-between">
                        <span class="font-bold uppercase text-gray-700">Parent Name:</span>
                        <span class="text-gray-600 capitalize"><?php echo htmlspecialchars($student['parent_name'] ?? ''); ?></span>
                    </div>
                    <div class="flex flex-row justify-between">
                        <span class="font-bold uppercase text-gray-700">Contact:</span>
                        <span class="text-gray-600"><?php echo htmlspecialchars($student['phone_1'] ?? ''); ?></span>
                    </div>
                    <div class="flex flex-row justify-between">
                        <span class="font-bold uppercase text-gray-700">Email:</span>
                        <span class="text-gray-600 capitalize"><?php echo htmlspecialchars($student['parent_email'] ?? ''); ?></span>
                    </div>
                    <div class="flex flex-row justify-between">
                        <span class="font-bold uppercase text-gray-700">gender:</span>
                        <span class="text-gray-600 capitalize"><?php echo htmlspecialchars($student['parent_gender'] ?? ''); ?></span>
                    </div>
                    <div class="flex flex-row justify-between">
                        <span class="font-bold uppercase text-gray-700">relationship:</span>
                        <span class="text-gray-600 capitalize"><?php echo htmlspecialchars($student['relationship'] ?? ''); ?></span>
                    </div>
                    <div class="flex flex-row justify-between">
                        <span class="font-bold uppercase text-gray-700">address:</span>
                        <span class="text-gray-600 capitalize"><?php echo htmlspecialchars($student['parent_address'] ?? ''); ?></span>
                    </div>
                    
                </div>
            </div>
            <div class="w-1/2">
                <hr class="border-gray-300 my-3">
                <div class="flex flex-row items-center justify-between mb-3">
                    <h3 class="uppercase text-blue-900 font-semibold">Academic Information</h3>
                </div>
                <hr class="border-gray-300 my-3">
                <div class="flex flex-col gap-2">
                    <div class="flex flex-row justify-between">
                        <span class="font-bold uppercase text-gray-700">Class:</span>
                        <span class="text-gray-600 capitalize"><?php echo htmlspecialchars($class_name ?: ''); ?></span>
                    </div>
                    <div class="flex flex-row justify-between">
                        <span class="font-bold uppercase text-gray-700">Stream:</span>
                        <span class="text-gray-600 capitalize"><?php echo htmlspecialchars($stream_name ?: ''); ?></span>
                    </div>
                    <div class="flex flex-row justify-between">
                        <span class="font-bold uppercase text-gray-700">Enrollment Date:</span>
                        <span class="text-gray-600"><?php echo htmlspecialchars(!empty($student['entry_date']) ? $student['entry_date'] : ''); ?></span>
                    </div>
                    <div class="flex flex-row justify-between">
                        <span class="font-bold uppercase text-gray-700">schoolpay code:</span>
                        <span class="text-gray-600"><?php echo htmlspecialchars($student['school_pay'] ?? ''); ?></span>
                    </div>
                    <div class="flex flex-row justify-between">
                        <span class="font-bold uppercase text-gray-700">residence status:</span>
                        <span class="text-gray-600 capitalize"><?php echo htmlspecialchars($student['residence_status'] ?? ''); ?></span>
                    </div>
                    
                </div>
            </div>
        </div>
            <div class="w/2"></div>
         </div>
    </div>
    
</div>
<?php include("partials/footer.php") ?>