<?php
session_start();
include("./partials/header.php");


// Check database connection
if(!isset($conn) || !$conn){
    die("Database connection failed.");
}

// Initialize session data if not exists
if(!isset($_SESSION['student_form'])){
    $_SESSION['student_form'] = [];
}

$currentStep = isset($_POST['current_step']) ? intval($_POST['current_step']) : 1;
$errors = [];
$success_message = '';

// Handle form navigation and validation
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    // Validate and store Step 1 data
    if(isset($_POST['step_1_submit'])){
        $student_name = isset($_POST['student_name']) ? trim($_POST['student_name']) : '';
        $learner_id = isset($_POST['learner_id']) ? trim($_POST['learner_id']) : '';
        $dob = isset($_POST['dob']) ? trim($_POST['dob']) : '';
        $gender = isset($_POST['gender']) ? trim($_POST['gender']) : '';
        $nationality = isset($_POST['nationality']) ? trim($_POST['nationality']) : '';
        $district = isset($_POST['district']) ? trim($_POST['district']) : '';
        $entry_date = isset($_POST['entry_date']) ? trim($_POST['entry_date']) : '';
        
        // Validate
        if(empty($student_name) || empty($learner_id) || empty($dob) || empty($gender) || empty($nationality) || empty($district) || empty($entry_date)){
            $errors[] = "All fields in Step 1 are required.";
        } else {
            // Store in session
            $_SESSION['student_form']['student_name'] = $student_name;
            $_SESSION['student_form']['lin'] = $learner_id; // Store as 'lin' to match database
            $_SESSION['student_form']['dob'] = $dob;
            $_SESSION['student_form']['gender'] = $gender;
            $_SESSION['student_form']['nationality'] = $nationality;
            $_SESSION['student_form']['district'] = $district;
            $_SESSION['student_form']['entry_date'] = $entry_date;
            
            // Handle image upload
            if(isset($_FILES['student_image']) && $_FILES['student_image']['error'] === 0){
                $uploadDir = "./images/students/";
                if(!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                
                $fileName = time() . '_' . basename($_FILES['student_image']['name']);
                $uploadPath = $uploadDir . $fileName;
                
                if(move_uploaded_file($_FILES['student_image']['tmp_name'], $uploadPath)){
                    $_SESSION['student_form']['student_image'] = $fileName;
                }
            }
            
            $currentStep = 2;
        }
    }
    
    // Validate and store Step 2 data
    elseif(isset($_POST['step_2_submit'])){
        $parent_name = isset($_POST['parent_name']) ? trim($_POST['parent_name']) : '';
        $phone_1 = isset($_POST['phone_1']) ? trim($_POST['phone_1']) : '';
        $phone_2 = isset($_POST['phone_2']) ? trim($_POST['phone_2']) : '';
        $occupation = isset($_POST['occupation']) ? trim($_POST['occupation']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $parent_gender = isset($_POST['parent_gender']) ? strtoupper(trim($_POST['parent_gender'])) : '';
        $address = isset($_POST['address']) ? trim($_POST['address']) : '';
        
        // Validate
        if(empty($parent_name) || empty($phone_1) || empty($occupation) || empty($address)){
            $errors[] = "Please fill in all required fields in Step 2.";
        } elseif(!preg_match('/^[+]?[0-9\-\s]{7,}$/', $phone_1)){
            $errors[] = "Please enter a valid primary phone number.";
        } else {
            $_SESSION['student_form']['parent_name'] = $parent_name;
            $_SESSION['student_form']['phone_1'] = $phone_1;
            $_SESSION['student_form']['phone_2'] = $phone_2;
            $_SESSION['student_form']['occupation'] = $occupation;
            $_SESSION['student_form']['email'] = $email;
            $_SESSION['student_form']['parent_gender'] = $parent_gender;
            $_SESSION['student_form']['address'] = $address;
            
            $currentStep = 3;
        }
    }
    
    // Validate and store Step 3 data
    elseif(isset($_POST['step_3_submit'])){
        $class_id = isset($_POST['class_id']) ? intval($_POST['class_id']) : 0;
        $stream_id = isset($_POST['stream_id']) && !empty($_POST['stream_id']) ? intval($_POST['stream_id']) : 0;
        $term = isset($_POST['term']) ? trim($_POST['term']) : '';
        $year_of_study = isset($_POST['year_of_study']) ? intval($_POST['year_of_study']) : 0;
        $school_pay = isset($_POST['school_pay']) && !empty($_POST['school_pay']) ? floatval($_POST['school_pay']) : 0;
        $entry_status = isset($_POST['entry_status']) ? strtoupper(trim($_POST['entry_status'])) : '';
        $residence_status = isset($_POST['residence_status']) ? strtoupper(trim($_POST['residence_status'])) : '';
        
        // Validate required fields
        if($class_id <= 0 || empty($term) || $year_of_study <= 0 || empty($entry_status) || empty($residence_status)){
            $errors[] = "Please fill in all required fields in Step 3.";
        } else {
            // Validate enum values
            if(!in_array($entry_status, ['NEW', 'CONTINUING'])){
                $errors[] = "Invalid enrollment status selected.";
            } elseif(!in_array($residence_status, ['DAY', 'BOARDING'])){
                $errors[] = "Invalid residence status selected.";
            } else {
                // Validate class exists in database
                $classCheck = mysqli_query($conn, "SELECT class_id FROM classes WHERE class_id = $class_id");
                if(!$classCheck || mysqli_num_rows($classCheck) === 0){
                    $errors[] = "Invalid class selected.";
                } elseif($stream_id > 0) {
                    // Validate stream exists if provided
                    $streamCheck = mysqli_query($conn, "SELECT stream_id FROM streams WHERE stream_id = $stream_id");
                    if(!$streamCheck || mysqli_num_rows($streamCheck) === 0){
                        $errors[] = "Invalid stream selected.";
                    }
                }
                
                // If validations pass, store in session
                if(empty($errors)){
                    $_SESSION['student_form']['class_id'] = $class_id;
                    $_SESSION['student_form']['stream_id'] = $stream_id;
                    $_SESSION['student_form']['term'] = $term;
                    $_SESSION['student_form']['year_of_study'] = $year_of_study;
                    $_SESSION['student_form']['school_pay'] = $school_pay;
                    $_SESSION['student_form']['entry_status'] = $entry_status;
                    $_SESSION['student_form']['residence_status'] = $residence_status;
                    
                    $currentStep = 4;
                }
            }
        }
    }
   
    // Final submission
    elseif(isset($_POST['submit_final'])){
        $formData = $_SESSION['student_form'];
        
        // Start transaction for data integrity
        mysqli_begin_transaction($conn);
        // var_dump($formData);
        // exit();
        try {
            // Insert into students table (core student info)
            $stmt = mysqli_prepare($conn, "INSERT INTO students (student_name, lin, dob, gender, nationality, district, entry_date, student_image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            if(!$stmt){
                throw new Exception("Prepare failed: " . mysqli_error($conn));
            }
            
            $student_image = isset($formData['student_image']) ? $formData['student_image'] : NULL;
            
            mysqli_stmt_bind_param($stmt, 'ssssssss',
                $formData['student_name'],
                $formData['lin'],
                $formData['dob'],
                $formData['gender'],
                $formData['nationality'],
                $formData['district'],
                $formData['entry_date'],
                $student_image
            );

            if(!mysqli_stmt_execute($stmt)){
                throw new Exception("Student insert failed: " . mysqli_stmt_error($stmt));
            }
            
            $student_id = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);

            // Insert into student_parent table (parent/guardian info)
            $pStmt = mysqli_prepare($conn, "INSERT INTO student_parent (student_id, parent_name, phone_1, phone_2, occupation, email, gender, address) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            if(!$pStmt){
                throw new Exception("Prepare failed: " . mysqli_error($conn));
            }
            
            $phone_2 = !empty($formData['phone_2']) ? $formData['phone_2'] : NULL;
            $email = !empty($formData['email']) ? $formData['email'] : NULL;
            
            mysqli_stmt_bind_param($pStmt, 'isssssss',
                $student_id,
                $formData['parent_name'],
                $formData['phone_1'],
                $phone_2,
                $formData['occupation'],
                $email,
                $formData['parent_gender'],
                $formData['address']
            );

            if(!mysqli_stmt_execute($pStmt)){
                throw new Exception("Parent info insert failed: " . mysqli_stmt_error($pStmt));
            }
            mysqli_stmt_close($pStmt);

            // Insert into student_additional_info table (academic info)
            $aStmt = mysqli_prepare($conn, "INSERT INTO student_additional_info (student_id, class_id, stream_id, term, year_of_study, school_pay, entry_status, residence_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            if(!$aStmt){
                throw new Exception("Prepare failed: " . mysqli_error($conn));
            }
            
            $stream_id = $formData['stream_id'] > 0 ? $formData['stream_id'] : NULL;
            $school_pay = $formData['school_pay'] > 0 ? $formData['school_pay'] : NULL;
            
            mysqli_stmt_bind_param($aStmt, 'iissiiss',
                $student_id,
                $formData['class_id'],
                $stream_id,
                $formData['term'],
                $formData['year_of_study'],
                $school_pay,
                $formData['entry_status'],
                $formData['residence_status']
            );

            if(!mysqli_stmt_execute($aStmt)){
                throw new Exception("Additional info insert failed: " . mysqli_stmt_error($aStmt));
            }
            mysqli_stmt_close($aStmt);
            
            // Commit transaction
            mysqli_commit($conn);
            
            $success_message = "Student registered successfully! Student ID: " . $student_id;
            unset($_SESSION['student_form']);
            $currentStep = 1;
            
        } catch (Exception $e) {
            // Rollback transaction on error
            mysqli_rollback($conn);
            $errors[] = "Registration failed: " . $e->getMessage();
            error_log('Student registration error: ' . $e->getMessage());
        }
    }
    
    // Go back to previous step
    elseif(isset($_POST['go_back'])){
        $currentStep = intval($_POST['current_step']) - 1;
        if($currentStep < 1) $currentStep = 1;
    }
}

$formData = $_SESSION['student_form'] ?? [];
?>

<div class="flex-1 p-6">
  <div class="max-w-4xl mx-auto bg-white shadow-lg rounded-lg p-8">
    
    <!-- Success Message -->
    <?php if($success_message): ?>
      <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-6">
        <?php echo e($success_message); ?>
      </div>
    <?php endif; ?>
    
    <!-- Error Messages -->
    <?php if(!empty($errors)): ?>
      <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-6">
        <ul>
          <?php foreach($errors as $error): ?>
            <li>• <?php echo e($error); ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>
    
    <!-- Progress Bar -->
    <div class="mb-8">
      <div class="flex justify-between items-center mb-2">
        <h2 class="text-2xl font-bold text-gray-800">Student Registration</h2>
        <span class="text-sm font-semibold text-gray-600">Step <?php echo $currentStep; ?>/4</span>
      </div>
      <div class="w-full bg-gray-200 rounded-full h-2">
        <div class="bg-teal-500 h-2 rounded-full transition-all duration-300" style="width: <?php echo ($currentStep / 4) * 100; ?>%"></div>
      </div>
    </div>
    
    <form method="POST" enctype="multipart/form-data" id="registrationForm">
      <input type="hidden" name="current_step" value="<?php echo $currentStep; ?>">
      
      <!-- STEP 1: Student Info -->
      <?php if($currentStep === 1): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Student Name <span class="text-red-500">*</span></label>
            <input type="text" autocomplete="off" name="student_name" required value="<?php echo htmlspecialchars($formData['student_name'] ?? ''); ?>" 
              class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400"/>
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Learner ID (LIN) <span class="text-red-500">*</span></label>
            <input type="text" autocomplete="off" name="learner_id" required value="<?php echo htmlspecialchars($formData['lin'] ?? ''); ?>" 
              placeholder="e.g. LID001"
              class="w-full  rounded-lg border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400"/>
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Date of Birth <span class="text-red-500">*</span></label>
            <input type="date" autocomplete="off" name="dob" required value="<?php echo htmlspecialchars($formData['dob'] ?? ''); ?>" 
              class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400"/>
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Gender <span class="text-red-500">*</span></label>
            <select name="gender" required class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
              <option value="">Select gender</option>
              <option value="Male" <?php echo ($formData['gender'] ?? '') === 'Male' ? 'selected' : ''; ?>>Male</option>
              <option value="Female" <?php echo ($formData['gender'] ?? '') === 'Female' ? 'selected' : ''; ?>>Female</option>
              <option value="Other" <?php echo ($formData['gender'] ?? '') === 'Other' ? 'selected' : ''; ?>>Other</option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Nationality <span class="text-red-500">*</span></label>
            <input type="text" autocomplete="off" name="nationality" required value="<?php echo htmlspecialchars($formData['nationality'] ?? ''); ?>" 
              placeholder="e.g. Ugandan"
              class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400"/>
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">District <span class="text-red-500">*</span></label>
            <input autocomplete="off" type="text" name="district" required value="<?php echo htmlspecialchars($formData['district'] ?? ''); ?>" 
              class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400"/>
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Entry Date <span class="text-red-500">*</span></label>
            <input autocomplete="off" type="date" name="entry_date" required value="<?php echo htmlspecialchars($formData['entry_date'] ?? ''); ?>" 
              class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400"/>
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Student Photo</label>
            <input type="file" name="student_image" accept="image/*" 
              class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400"/>
          </div>
        </div>
      <?php endif; ?>
      
      <!-- STEP 2: Parent/Guardian Info -->
      <?php if($currentStep === 2): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="md:col-span-2">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Parent/Guardian Information</h3>
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Parent Name <span class="text-red-500">*</span></label>
            <input type="text" autocomplete="off" name="parent_name" required value="<?php echo htmlspecialchars($formData['parent_name'] ?? ''); ?>" 
              class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400"/>
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Gender</label>
            <select name="parent_gender" class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
              <option value="">Select gender</option>
              <option value="MALE" <?php echo ($formData['parent_gender'] ?? '') === 'MALE' ? 'selected' : ''; ?>>Male</option>
              <option value="FEMALE" <?php echo ($formData['parent_gender'] ?? '') === 'FEMALE' ? 'selected' : ''; ?>>Female</option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Phone 1 <span class="text-red-500">*</span></label>
            <input type="text" autocomplete="off" name="phone_1" required value="<?php echo htmlspecialchars($formData['phone_1'] ?? ''); ?>" 
              placeholder="+256..."
              class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400"/>
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Phone 2 (Optional)</label>
            <input type="text" autocomplete="off" name="phone_2" value="<?php echo htmlspecialchars($formData['phone_2'] ?? ''); ?>" 
              placeholder="+256..."
              class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400"/>
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Occupation <span class="text-red-500">*</span></label>
            <input type="text" autocomplete="off" name="occupation" required value="<?php echo htmlspecialchars($formData['occupation'] ?? ''); ?>" 
              class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400"/>
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
            <input type="email" autocomplete="off" name="email" value="<?php echo htmlspecialchars($formData['email'] ?? ''); ?>" 
              class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400"/>
          </div>
          
          <div class="md:col-span-2">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Address <span class="text-red-500">*</span></label>
            <textarea name="address" required rows="3" 
              class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400"><?php echo htmlspecialchars($formData['address'] ?? ''); ?></textarea>
          </div>
        </div>
      <?php endif; ?>
      
      <!-- STEP 3: Additional Information -->
      <?php if($currentStep === 3): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="md:col-span-2">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Academic Information</h3>
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Class <span class="text-red-500">*</span></label>
            <select name="class_id" required class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
              <option value="">Select class</option>
              <?php
                $classRes = mysqli_query($conn, "SELECT class_id, class_name FROM classes ORDER BY class_name");
                if($classRes){
                  while($row = mysqli_fetch_assoc($classRes)){
                    $selected = ($formData['class_id'] ?? '') == $row['class_id'] ? 'selected' : '';
                    echo "<option value='" . $row['class_id'] . "' $selected>" . $row['class_name'] . "</option>";
                  }
                }
              ?>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Stream</label>
            <select name="stream_id" class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
              <option value="">Select stream (optional)</option>
              <?php
                $streamRes = mysqli_query($conn, "SELECT stream_id, stream_name FROM streams ORDER BY stream_name");
                if($streamRes){
                  while($row = mysqli_fetch_assoc($streamRes)){
                    $selected = ($formData['stream_id'] ?? '') == $row['stream_id'] ? 'selected' : '';
                    echo "<option value='" . $row['stream_id'] . "' $selected>" . $row['stream_name'] . "</option>";
                  }
                }
              ?>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Term <span class="text-red-500">*</span></label>
            <select name="term" required class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
              <option value="">Select term</option>
              <option value="Term 1" <?php echo ($formData['term'] ?? '') === 'Term 1' ? 'selected' : ''; ?>>Term 1</option>
              <option value="Term 2" <?php echo ($formData['term'] ?? '') === 'Term 2' ? 'selected' : ''; ?>>Term 2</option>
              <option value="Term 3" <?php echo ($formData['term'] ?? '') === 'Term 3' ? 'selected' : ''; ?>>Term 3</option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Year of Study <span class="text-red-500">*</span></label>
            <input autocomplete="off" type="number" name="year_of_study" required min="1" value="<?php echo htmlspecialchars($formData['year_of_study'] ?? ''); ?>" 
              class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400"/>
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">School Fee Amount</label>
            <input autocomplete="off" type="number" name="school_pay" step="0.01" value="<?php echo htmlspecialchars($formData['school_pay'] ?? ''); ?>" 
              placeholder="0.00"
              class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400"/>
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Enrollment Status <span class="text-red-500">*</span></label>
            <select name="entry_status" required class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
              <option value="">Select status</option>
              <option value="NEW" <?php echo ($formData['entry_status'] ?? '') === 'NEW' ? 'selected' : ''; ?>>New</option>
              <option value="CONTINUING" <?php echo ($formData['entry_status'] ?? '') === 'CONTINUING' ? 'selected' : ''; ?>>Continuing</option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Residence Status <span class="text-red-500">*</span></label>
            <select name="residence_status" required class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
              <option value="">Select status</option>
              <option value="DAY" <?php echo ($formData['residence_status'] ?? '') === 'DAY' ? 'selected' : ''; ?>>Day</option>
              <option value="BOARDING" <?php echo ($formData['residence_status'] ?? '') === 'BOARDING' ? 'selected' : ''; ?>>Boarding</option>
            </select>
          </div>
        </div>
      <?php endif; ?>
      
      <!-- STEP 4: Review & Confirm -->
      <?php if($currentStep === 4): ?>
        <div class="space-y-6">
          <h3 class="text-lg font-semibold text-gray-700 mb-4">Review Your Information</h3>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 p-6 rounded-lg">
            <div>
              <h4 class="text-sm font-semibold text-gray-600 uppercase mb-3">Student Information</h4>
            <div class="space-y-2 text-sm text-gray-700">
                <p><strong>Name:</strong> <?php echo htmlspecialchars($formData['student_name'] ?? ''); ?></p>
                <p><strong>Learner ID (LIN):</strong> <?php echo htmlspecialchars($formData['lin'] ?? ''); ?></p>
                <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($formData['dob'] ?? ''); ?></p>
                <p><strong>Gender:</strong> <?php echo htmlspecialchars($formData['gender'] ?? ''); ?></p>
                <p><strong>Nationality:</strong> <?php echo htmlspecialchars($formData['nationality'] ?? ''); ?></p>
                <p><strong>District:</strong> <?php echo htmlspecialchars($formData['district'] ?? ''); ?></p>
                <p><strong>Entry Date:</strong> <?php echo htmlspecialchars($formData['entry_date'] ?? ''); ?></p>
              </div>
            </div>
            
            <div>
              <h4 class="text-sm font-semibold text-gray-600 uppercase mb-3">Parent/Guardian Information</h4>
              <div class="space-y-2 text-sm text-gray-700">
                <p><strong>Name:</strong> <?php echo htmlspecialchars($formData['parent_name'] ?? ''); ?></p>
                <p><strong>Phone 1:</strong> <?php echo htmlspecialchars($formData['phone_1'] ?? ''); ?></p>
                <p><strong>Phone 2:</strong> <?php echo htmlspecialchars($formData['phone_2'] ?? ''); ?></p>
                <p><strong>Occupation:</strong> <?php echo htmlspecialchars($formData['occupation'] ?? ''); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($formData['email'] ?? ''); ?></p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($formData['address'] ?? ''); ?></p>
              </div>
            </div>
            
            <div class="md:col-span-2">
              <h4 class="text-sm font-semibold text-gray-600 uppercase mb-3">Academic Information</h4>
              <div class="grid grid-cols-2 gap-4 text-sm text-gray-700">
                <p><strong>Class:</strong> <?php echo htmlspecialchars($formData['class_id'] ?? ''); ?></p>
                <p><strong>Stream:</strong> <?php echo htmlspecialchars($formData['stream_id'] ?? ''); ?></p>
                <p><strong>Term:</strong> <?php echo htmlspecialchars($formData['term'] ?? ''); ?></p>
                <p><strong>Year of Study:</strong> <?php echo htmlspecialchars($formData['year_of_study'] ?? ''); ?></p>
                <p><strong>School Fee:</strong> <?php echo htmlspecialchars($formData['school_pay'] ?? ''); ?></p>
                <p><strong>Enrollment Status:</strong> <?php echo htmlspecialchars($formData['entry_status'] ?? ''); ?></p>
                <p><strong>Residence Status:</strong> <?php echo htmlspecialchars($formData['residence_status'] ?? ''); ?></p>
              </div>
            </div>
          </div>
          
          <div class="bg-blue-50 border-l-4 border-blue-500 p-4">
            <p class="text-sm text-blue-700"><strong>Note:</strong> Please review all information carefully. Click Submit to complete the registration.</p>
          </div>
        </div>
      <?php endif; ?>
      
      <!-- Navigation Buttons -->
      <div class="flex items-center justify-between pt-6 border-t border-gray-200">
        <?php if($currentStep > 1): ?>
          <button type="submit" name="go_back" class="px-6 py-2 rounded-lg text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 transition">
            ← Previous
          </button>
        <?php else: ?>
          <div></div>
        <?php endif; ?>
        
        <?php if($currentStep < 4): ?>
          <button type="submit" name="step_<?php echo $currentStep; ?>_submit" class="px-6 py-2 rounded-lg text-sm font-medium text-white bg-teal-500 hover:bg-teal-600 transition">
            Next →
          </button>
        <?php elseif($currentStep === 4): ?>
          <button type="submit" name="submit_final" class="px-6 py-2 rounded-lg text-sm font-medium text-white bg-green-500 hover:bg-green-600 transition">
            Submit Registration
          </button>
        <?php endif; ?>
      </div>
    </form>
  </div>
</div>

<?php 
// Helper function for escaping output
function e($str){
  return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
include("./partials/footer.php");
?>