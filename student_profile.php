<?php include("partials/header.php");
if (session_status() === PHP_SESSION_NONE) session_start();

// validate student_id
$student_id = filter_input(INPUT_GET, 'student_id', FILTER_VALIDATE_INT);
if (!$student_id) {
    header("Location:".SITEURL."view_student.php");
    exit();
}

$updateErrors = [];
$updateSuccess = '';

$parentUpdateErrors = [];
$parentUpdateSuccess = '';

// Handle bio updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_bio'])) {
    $postedId = filter_input(INPUT_POST, 'student_id', FILTER_VALIDATE_INT);
    if ($postedId === $student_id) {
        $student_name = trim($_POST['student_name'] ?? '');
        $nationality = trim($_POST['nationality'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $class_id = filter_input(INPUT_POST, 'class_id', FILTER_VALIDATE_INT);
        $stream_id = filter_input(INPUT_POST, 'stream_id', FILTER_VALIDATE_INT);

        if ($student_name === '') {
            $updateErrors[] = 'Student name is required.';
        }
        if ($nationality === '') {
            $updateErrors[] = 'Nationality is required.';
        }
        if ($address === '') {
            $updateErrors[] = 'Address is required.';
        }
        if (!$class_id || $class_id <= 0) {
            $updateErrors[] = 'Please select a class.';
        }

        if (empty($updateErrors)) {
            // Update students table
            $stmt = mysqli_prepare($conn, "UPDATE students SET student_name = ?, nationality = ? WHERE student_id = ?");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'ssi', $student_name, $nationality, $student_id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }

            // Update parent address
            $pStmt = mysqli_prepare($conn, "UPDATE student_parent SET address = ? WHERE student_id = ?");
            if ($pStmt) {
                mysqli_stmt_bind_param($pStmt, 'si', $address, $student_id);
                mysqli_stmt_execute($pStmt);
                mysqli_stmt_close($pStmt);
            }

            // Update academic info
            $aStmt = mysqli_prepare($conn, "UPDATE student_additional_info SET class_id = ?, stream_id = ? WHERE student_id = ?");
            if ($aStmt) {
                $streamVal = ($stream_id && $stream_id > 0) ? $stream_id : null;
                mysqli_stmt_bind_param($aStmt, 'iii', $class_id, $streamVal, $student_id);
                mysqli_stmt_execute($aStmt);
                mysqli_stmt_close($aStmt);
            }

            header('Location: ' . SITEURL . 'student_profile.php?student_id=' . $student_id . '&updated=1');
            exit();
        }
    }
}

// Handle parent updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_parent'])) {
    $postedId = filter_input(INPUT_POST, 'student_id', FILTER_VALIDATE_INT);
    if ($postedId === $student_id) {
        $parent_name = trim($_POST['parent_name'] ?? '');
        $parent_gender = trim($_POST['parent_gender'] ?? '');
        $phone_1 = trim($_POST['phone_1'] ?? '');
        $phone_2 = trim($_POST['phone_2'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $relationship = trim($_POST['relationship'] ?? '');

        if ($parent_name === '') {
            $parentUpdateErrors[] = 'Parent name is required.';
        }
        if ($phone_1 === '') {
            $parentUpdateErrors[] = 'Primary contact is required.';
        }

        if (empty($parentUpdateErrors)) {
            $pStmt = mysqli_prepare($conn, "UPDATE student_parent SET parent_name = ?, gender = ?, phone_1 = ?, phone_2 = ?, email = ?, relationship = ? WHERE student_id = ?");
            if ($pStmt) {
                mysqli_stmt_bind_param($pStmt, 'ssssssi', $parent_name, $parent_gender, $phone_1, $phone_2, $email, $relationship, $student_id);
                mysqli_stmt_execute($pStmt);
                mysqli_stmt_close($pStmt);
            }

            header('Location: ' . SITEURL . 'student_profile.php?student_id=' . $student_id . '&updated_parent=1');
            // exit();
        }
    }
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

// Determine profile image (use default if missing)
$profileImage = 'images/students/profil.png';
if (!empty($student['student_image'])) {
    $candidate = $student['student_image'];
    $candidatePath = $candidate;
    if (!str_starts_with($candidatePath, '/')) {
        $candidatePath = __DIR__ . '/' . ltrim($candidatePath, '/');
    }
    if (file_exists($candidatePath)) {
        $profileImage = $student['student_image'];
    }
}

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

$updateSuccess = isset($_GET['updated']) ? 'Student info updated successfully.' : '';
$parentUpdateSuccess = isset($_GET['updated_parent']) ? 'Parent info updated successfully.' : '';

// Fetch list of classes and streams for the edit form
$classes = [];
$streamsByClass = [];
$classRes = mysqli_query($conn, "SELECT class_id, class_name FROM classes ORDER BY class_name");
if ($classRes) {
    while ($row = mysqli_fetch_assoc($classRes)) {
        $classes[] = $row;
    }
}
$streamRes = mysqli_query($conn, "SELECT stream_id, stream_name, class_id FROM streams ORDER BY stream_name");
if ($streamRes) {
    while ($row = mysqli_fetch_assoc($streamRes)) {
        $streamsByClass[$row['class_id']][] = $row;
    }
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
                <img class="img-fluid border rounded" src="<?php echo htmlspecialchars($profileImage); ?>" alt="Student photo" style="height: 200px; width: 200px; border-radius: 10%; object-fit: cover;">
            </div>
            <div class="w-1/2">
               <?php if($updateSuccess): ?>
                   <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                       <?php echo htmlspecialchars($updateSuccess); ?>
                   </div>
               <?php endif; ?>
               <?php if($parentUpdateSuccess): ?>
                   <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                       <?php echo htmlspecialchars($parentUpdateSuccess); ?>
                   </div>
               <?php endif; ?>
               <hr class="border-white-700 my-1">
               <div class="flex justify-between align-items-center">
                    <div class="uppercase text-blue-900 text-sm">
                        Student biodata
                    </div>
                    <div class="edit-btn">
                        <button type="button" id="editBioBtn" class="text-sm uppercase text-[#ffffff] bg-blue-900 px-3 py-1 mx-1 hover:bg-blue-800">
                            <i class="fa-solid fa-pen-to-square"></i>
                            edit info
                        </button>
                    </div>
                </div>
               <hr class="border-white-700 my-1">

               <!-- Edit modal -->
               <style>
                 /* Modal animation */
                 #editBioModal,
                 #editParentModal {
                   opacity: 0;
                   transform: translateY(-15px);
                   transition: opacity 220ms ease, transform 220ms ease;
                 }
                 #editBioModal.modal-open,
                 #editParentModal.modal-open {
                   opacity: 1;
                   transform: translateY(0);
                 }
                 #editBioModal.modal-closing,
                 #editParentModal.modal-closing {
                   opacity: 0;
                   transform: translateY(-15px);
                 }
               </style>

               <div id="editBioModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
                 <div class="bg-white rounded-lg w-full max-w-lg p-6 relative">
                   <button id="closeEditBio" type="button" class="absolute top-3 right-3 text-gray-600 hover:text-gray-900">&times;</button>
                   <h3 class="text-lg font-semibold mb-4">Update Student Info</h3>
                   <?php if(!empty($updateErrors)): ?>
                     <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                       <ul class="list-disc list-inside">
                         <?php foreach($updateErrors as $updateError): ?>
                           <li><?php echo htmlspecialchars($updateError); ?></li>
                         <?php endforeach; ?>
                       </ul>
                     </div>
                   <?php endif; ?>
                   <form method="POST" id="editBioForm">
                     <input type="hidden" name="update_bio" value="1">
                     <input type="hidden" name="student_id" value="<?php echo (int) $student_id; ?>">

                     <div class="grid grid-cols-1 gap-4">
                       <div>
                         <label class="block text-sm font-semibold">Full name</label>
                         <input name="student_name" required value="<?php echo htmlspecialchars($student['student_name'] ?? ''); ?>" class="w-full rounded border-gray-300 px-3 py-2" />
                       </div>
                       <div>
                         <label class="block text-sm font-semibold">Nationality</label>
                         <input name="nationality" required value="<?php echo htmlspecialchars($student['nationality'] ?? ''); ?>" class="w-full rounded border-gray-300 px-3 py-2" />
                       </div>
                       <div>
                         <label class="block text-sm font-semibold">Address</label>
                         <textarea name="address" required class="w-full rounded border-gray-300 px-3 py-2"><?php echo htmlspecialchars($student['parent_address'] ?? ''); ?></textarea>
                       </div>
                       <div>
                         <label class="block text-sm font-semibold">Class</label>
                         <select name="class_id" id="editClassSelect" required class="w-full rounded border-gray-300 px-3 py-2">
                           <option value="">Select class</option>
                           <?php foreach($classes as $class): ?>
                             <option value="<?php echo (int)$class['class_id']; ?>" <?php echo ((int)$student['class_id'] === (int)$class['class_id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($class['class_name']); ?></option>
                           <?php endforeach; ?>
                         </select>
                       </div>
                       <div>
                         <label class="block text-sm font-semibold">Stream</label>
                         <select name="stream_id" id="editStreamSelect" class="w-full rounded border-gray-300 px-3 py-2">
                           <option value="">Select stream (optional)</option>
                         </select>
                       </div>
                     </div>

                     <div class="mt-5 flex justify-end gap-2">
                       <button type="button" id="cancelEditBio" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Cancel</button>
                       <button type="submit" class="px-4 py-2 bg-blue-900 text-white rounded hover:bg-blue-800">Save</button>
                     </div>
                   </form>
                 </div>
               </div>

               <!-- Parent edit modal -->
               <div id="editParentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
                 <div class="bg-white rounded-lg w-full max-w-lg p-6 relative">
                   <button id="closeEditParent" type="button" class="absolute top-3 right-3 text-gray-600 hover:text-gray-900">&times;</button>
                   <h3 class="text-lg font-semibold mb-4">Update Parent/Guardian Info</h3>
                   <?php if(!empty($parentUpdateErrors)): ?>
                     <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                       <ul class="list-disc list-inside">
                         <?php foreach($parentUpdateErrors as $parentError): ?>
                           <li><?php echo htmlspecialchars($parentError); ?></li>
                         <?php endforeach; ?>
                       </ul>
                     </div>
                   <?php endif; ?>
                   <form method="POST" id="editParentForm">
                     <input type="hidden" name="update_parent" value="1">
                     <input type="hidden" name="student_id" value="<?php echo (int) $student_id; ?>">

                     <div class="grid grid-cols-1 gap-4">
                       <div>
                         <label class="block text-sm font-semibold">Parent name</label>
                         <input name="parent_name" required value="<?php echo htmlspecialchars($student['parent_name'] ?? ''); ?>" class="w-full rounded border-gray-300 px-3 py-2" />
                       </div>
                       <div>
                         <label class="block text-sm font-semibold">Gender</label>
                         <select name="parent_gender" class="w-full rounded border-gray-300 px-3 py-2">
                           <option value="">Select gender</option>
                           <option value="MALE" <?php echo (strtoupper($student['parent_gender'] ?? '') === 'MALE') ? 'selected' : ''; ?>>Male</option>
                           <option value="FEMALE" <?php echo (strtoupper($student['parent_gender'] ?? '') === 'FEMALE') ? 'selected' : ''; ?>>Female</option>
                         </select>
                       </div>
                       <div>
                         <label class="block text-sm font-semibold">Contact 1</label>
                         <input name="phone_1" required value="<?php echo htmlspecialchars($student['phone_1'] ?? ''); ?>" class="w-full rounded border-gray-300 px-3 py-2" />
                       </div>
                       <div>
                         <label class="block text-sm font-semibold">Contact 2</label>
                         <input name="phone_2" value="<?php echo htmlspecialchars($student['phone_2'] ?? ''); ?>" class="w-full rounded border-gray-300 px-3 py-2" />
                       </div>
                       <div>
                         <label class="block text-sm font-semibold">Email</label>
                         <input type="email" name="email" value="<?php echo htmlspecialchars($student['parent_email'] ?? ''); ?>" class="w-full rounded border-gray-300 px-3 py-2" />
                       </div>
                       <div>
                         <label class="block text-sm font-semibold">Relationship</label>
                         <input name="relationship" value="<?php echo htmlspecialchars($student['relationship'] ?? ''); ?>" class="w-full rounded border-gray-300 px-3 py-2" />
                       </div>
                     </div>

                     <div class="mt-5 flex justify-end gap-2">
                       <button type="button" id="cancelEditParent" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Cancel</button>
                       <button type="submit" class="px-4 py-2 bg-blue-900 text-white rounded hover:bg-blue-800">Save</button>
                     </div>
                   </form>
                 </div>
               </div>

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
                <hr class="border-gray-300 my-1">
                <div class="flex flex-row items-center justify-between mb-1">
                    <h3 class="uppercase text-blue-900 font-semibold">Parent/Guardian Information</h3>
                    <button type="button" id="editParentBtn" class="text-sm uppercase text-white bg-blue-900 px-3 py-1 hover:bg-blue-800 rounded transition">
                        <i class="fa-solid fa-pen-to-square"></i>
                        edit info
                    </button>
                </div>
                <hr class="border-gray-300 my-1">
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
                        <span class="font-bold uppercase text-gray-700">Contact 2:</span>
                        <span class="text-gray-600"><?php echo htmlspecialchars($student['phone_2'] ?? ''); ?></span>
                    </div>
                    <div class="flex flex-row justify-between">
                        <span class="font-bold uppercase text-gray-700">Email:</span>
                        <span class="text-gray-600"><?php echo htmlspecialchars($student['parent_email'] ?? ''); ?></span>
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
                <hr class="border-gray-300 my-1">
                <div class="flex flex-row items-center justify-between mb-1">
                    <h3 class="uppercase text-blue-900 font-semibold">Academic Information</h3>
                </div>
                <hr class="border-gray-300 my-1">
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
<script>
(function(){
  const modal = document.getElementById('editBioModal');
  const editBtn = document.getElementById('editBioBtn');
  const closeBtn = document.getElementById('closeEditBio');
  const cancelBtn = document.getElementById('cancelEditBio');
  const classSelect = document.getElementById('editClassSelect');
  const streamSelect = document.getElementById('editStreamSelect');

  const parentModal = document.getElementById('editParentModal');
  const editParentBtn = document.getElementById('editParentBtn');
  const closeParentBtn = document.getElementById('closeEditParent');
  const cancelParentBtn = document.getElementById('cancelEditParent');

  const streamsByClass = <?php echo json_encode($streamsByClass); ?>;

  function populateStreams(classId, selectedStream) {
    streamSelect.innerHTML = '<option value="">Select stream (optional)</option>';
    if (!classId) return;
    const streams = streamsByClass[classId] || [];
    streams.forEach(stream => {
      const opt = document.createElement('option');
      opt.value = stream.stream_id;
      opt.textContent = stream.stream_name;
      if (selectedStream && parseInt(selectedStream, 10) === parseInt(stream.stream_id, 10)) {
        opt.selected = true;
      }
      streamSelect.appendChild(opt);
    });
  }

  function openModal() {
    modal.classList.remove('hidden');
    // Force reflow for transition to work
    void modal.offsetWidth;
    modal.classList.add('modal-open');

    const selectedClass = classSelect.value;
    const selectedStream = <?php echo json_encode($student['stream_id'] ?? ''); ?>;
    populateStreams(selectedClass, selectedStream);
  }

  function closeModal() {
    modal.classList.remove('modal-open');
    modal.classList.add('modal-closing');

    modal.addEventListener('transitionend', function handler() {
      modal.classList.add('hidden');
      modal.classList.remove('modal-closing');
      modal.removeEventListener('transitionend', handler);
    });
  }

  editBtn?.addEventListener('click', openModal);
  closeBtn?.addEventListener('click', closeModal);
  cancelBtn?.addEventListener('click', closeModal);

  editParentBtn?.addEventListener('click', openParentModal);
  closeParentBtn?.addEventListener('click', closeParentModal);
  cancelParentBtn?.addEventListener('click', closeParentModal);

  classSelect?.addEventListener('change', function() {
    populateStreams(this.value, '');
  });

  function openParentModal() {
    parentModal.classList.remove('hidden');
    void parentModal.offsetWidth;
    parentModal.classList.add('modal-open');
  }

  function closeParentModal() {
    parentModal.classList.remove('modal-open');
    parentModal.classList.add('modal-closing');

    parentModal.addEventListener('transitionend', function handler() {
      parentModal.classList.add('hidden');
      parentModal.classList.remove('modal-closing');
      parentModal.removeEventListener('transitionend', handler);
    });
  }
})();
</script>

<?php include("partials/footer.php") ?>