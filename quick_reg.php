<?php
// ── Bootstrap ────────────────────────────────────────────────────
include_once __DIR__ . '/constants/constant.php';   // $conn lives here

if (session_status() === PHP_SESSION_NONE) session_start();

// ── CSRF token ───────────────────────────────────────────────────
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ════════════════════════════════════════════════════════════════
//  POST HANDLER  (must run BEFORE any output / header.php)
// ════════════════════════════════════════════════════════════════
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF check
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $_SESSION['flash_error'] = 'Invalid form submission. Please try again.';
        header('Location: quick_reg.php');
        exit;
    }

    // helper
    $clean = fn($v) => mysqli_real_escape_string($conn, trim($v ?? ''));

    // collect
    $student_name       = $clean($_POST['student_name']);
    $lin_number         = strtoupper($clean($_POST['lin_number']));
    $gender             = $clean($_POST['gender']);
    $nationality        = $clean($_POST['nationality']);
    $class_id           = (int)($_POST['class'] ?? 0);
    $stream_id          = (int)($_POST['stream'] ?? "");
    $term               = $clean($_POST['term']);
    $year_of_study      = $clean($_POST['year_of_study']);
    $residential_status = strtoupper($clean($_POST['residential_status']));
    $raw_entry          = $clean($_POST['entry_status']);

    $entry_status = match($raw_entry) {
        'New Student' => 'NEW',
        'Continuing'  => 'CONTINUING',
        default       => strtoupper($raw_entry),
    };

    // validate
    $errors = [];
    if (!$student_name)       $errors[] = 'Student name is required.';
    if (!$lin_number)         $errors[] = 'LIN number is required.';
    if (!$gender)             $errors[] = 'Gender is required.';
    if (!$class_id)           $errors[] = 'Class is required.';
    if (!$term)               $errors[] = 'Term is required.';
    if (!$year_of_study)      $errors[] = 'Year of study is required.';
    if (!$residential_status) $errors[] = 'Residential status is required.';
    if (!$entry_status)       $errors[] = 'Entry status is required.';
    if (!$nationality)        $errors[] = 'Nationality is required.';

    // duplicate LIN check
    if (empty($errors)) {
        $chk = mysqli_prepare($conn, "SELECT student_id FROM students WHERE lin = ? LIMIT 1");
        mysqli_stmt_bind_param($chk, 's', $lin_number);
        mysqli_stmt_execute($chk);
        mysqli_stmt_store_result($chk);
        if (mysqli_stmt_num_rows($chk) > 0) {
            $errors[] = "A student with LIN <strong>$lin_number</strong> already exists.";
        }
        mysqli_stmt_close($chk);
    }

    if (!empty($errors)) {
        $_SESSION['flash_error'] = implode('<br>', $errors);
        header('Location: quick_reg.php');
        exit;
    }

    // insert
    mysqli_begin_transaction($conn);
    try {

        $s1 = mysqli_prepare($conn,
            "INSERT INTO students (student_name, lin, gender, nationality, entry_date)
             VALUES (?, ?, ?, ?, NOW())"
        );
        if (!$s1) throw new Exception(mysqli_error($conn));
        mysqli_stmt_bind_param($s1, 'ssss', $student_name, $lin_number, $gender, $nationality);
        mysqli_stmt_execute($s1);
        $student_id = mysqli_insert_id($conn);
        mysqli_stmt_close($s1);

        $stream_val = $stream_id > 0 ? $stream_id : null;
        $s2 = mysqli_prepare($conn,
            "INSERT INTO student_additional_info
                (student_id, class_id, stream_id, term, year_of_study, entry_status, residence_status)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        if (!$s2) throw new Exception(mysqli_error($conn));
        mysqli_stmt_bind_param($s2, 'iiissss',
            $student_id, $class_id, $stream_val,
            $term, $year_of_study, $entry_status, $residential_status
        );
        mysqli_stmt_execute($s2);
        mysqli_stmt_close($s2);

        mysqli_commit($conn);

        // regenerate CSRF token after successful submit
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['flash_success'] = "Student <strong>$student_name</strong> registered successfully.";
        header('Location: quick_reg.php');
        exit;

    } catch (Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['flash_error'] = 'Database error: ' . htmlspecialchars($e->getMessage());
        header('Location: quick_reg.php');
        exit;
    }
}

// ════════════════════════════════════════════════════════════════
//  GET — render the page (safe to output now)
// ════════════════════════════════════════════════════════════════
include("partials/header.php");
?>

<!-- Flash messages -->
<?php if (!empty($_SESSION['flash_success'])): ?>
<div class="w-[95%] mx-auto mt-4">
    <div class="flex items-center gap-3 px-4 py-3 bg-green-50 border border-green-300 text-green-800 rounded-lg" id='success_msg'>
        <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        <span><?= $_SESSION['flash_success'] ?></span>
    </div>
</div>
<?php unset($_SESSION['flash_success']); endif; ?>

<?php if (!empty($_SESSION['flash_error'])): ?>
<div class="w-[95%] mx-auto mt-4">
    <div class="flex items-center gap-3 px-4 py-3 bg-red-50 border border-red-300 text-red-800 rounded-lg">
        <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-9v4a1 1 0 102 0V9a1 1 0 10-2 0zm0-4a1 1 0 112 0 1 1 0 01-2 0z" clip-rule="evenodd"/>
        </svg>
        <span><?= $_SESSION['flash_error'] ?></span>
    </div>
</div>
<?php unset($_SESSION['flash_error']); endif; ?>

<!-- Card -->
<div class="w-[95%] mx-auto mt-6 bg-white shadow rounded-lg">

    <div class="px-6 py-4 border-b bg-teal-500 text-white rounded-t-lg">
        <h2 class="text-lg font-semibold">Quick Student Registration</h2>
    </div>

    <div class="px-6 py-6">

        <!-- Progress bar -->
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-3">
                <div id="step1Circle" class="w-8 h-8 flex items-center justify-center rounded-full bg-yellow-400 text-white font-bold">1</div>
                <span class="font-medium text-gray-700">Student Details</span>
            </div>
            <div class="flex-1 h-1 mx-4 bg-gray-200">
                <div id="progressBar" class="h-1 bg-teal-500 w-0 transition-all duration-300"></div>
            </div>
            <div class="flex items-center gap-3">
                <div id="step2Circle" class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-300 text-white font-bold">2</div>
                <span class="font-medium text-gray-500">Confirm</span>
            </div>
        </div>

        <!-- Form posts to itself -->
        <form id="registrationForm" method="POST" action="quick_reg.php" novalidate>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

            <!-- ── STEP 1 ── -->
            <div id="step1">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div>
                        <label class="block text-sm font-medium mb-1">Student Name</label>
                        <input type="text" name="student_name" required
                               class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-400 outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">LIN Number</label>
                        <input type="text" name="lin_number" required
                               class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-400 outline-none uppercase">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Gender</label>
                        <select name="gender" required class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-400 outline-none">
                            <option value="" disabled selected>Select</option>
                            <option>Male</option>
                            <option>Female</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Class</label>
                        <select id="classSelect" name="class" required
                                class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-400 outline-none">
                            <option value="" disabled selected>Select Class</option>
                            <?php
                            $classes = mysqli_query($conn, "SELECT * FROM classes ORDER BY class_id");
                            while ($row = mysqli_fetch_assoc($classes)):
                            ?>
                            <option value="<?= $row['class_id'] ?>"><?= htmlspecialchars($row['class_name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div id="streamWrapper" class="hidden">
                        <label class="block text-sm font-medium mb-1">Stream</label>
                        <select id="streamSelect" name="stream" disabled
                                class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-400 outline-none">
                            <option value="">Select Stream (Optional)</option>
                        </select>
                        <p id="streamLoading" class="text-xs text-gray-500 mt-1 hidden">Loading streams…</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Term</label>
                        <select name="term" required class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-400 outline-none">
                            <option value="" disabled selected>Select term</option>
                            <option>Term 1</option>
                            <option>Term 2</option>
                            <option>Term 3</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Year of Study</label>
                        <input type="number" name="year_of_study" required min="2000" max="2100"
                               class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-400 outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Residential Status</label>
                        <select name="residential_status" required class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-400 outline-none">
                            <option value="" disabled selected>Select</option>
                            <option value="BOARDING">Boarding</option>
                            <option value="DAY">Day</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Entry Status</label>
                        <select name="entry_status" required class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-400 outline-none">
                            <option value="" disabled selected>Select</option>
                            <option value="New Student">New Student</option>
                            <option value="Continuing">Continuing</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Nationality</label>
                        <input type="text" name="nationality" required
                               class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-400 outline-none">
                    </div>

                </div>

                <div class="flex justify-end mt-8">
                    <button type="button" onclick="goToStep2()"
                            class="bg-teal-500 text-white px-6 py-2 rounded-lg hover:bg-teal-600 transition">
                        Next →
                    </button>
                </div>
            </div>

            <!-- ── STEP 2 ── -->
            <div id="step2" class="hidden">
                <h3 class="text-lg font-semibold mb-4">Confirm Student Details</h3>

                <div class="bg-gray-50 p-6 rounded-lg space-y-2 text-sm">
                    <div><strong>Name:</strong>        <span id="c_student_name"></span></div>
                    <div><strong>LIN:</strong>         <span id="c_lin_number"></span></div>
                    <div><strong>Gender:</strong>      <span id="c_gender"></span></div>
                    <div><strong>Class:</strong>       <span id="c_class"></span></div>
                    <div><strong>Stream:</strong>      <span id="c_stream"></span></div>
                    <div><strong>Term:</strong>        <span id="c_term"></span></div>
                    <div><strong>Year:</strong>        <span id="c_year_of_study"></span></div>
                    <div><strong>Residential:</strong> <span id="c_residential_status"></span></div>
                    <div><strong>Entry:</strong>       <span id="c_entry_status"></span></div>
                    <div><strong>Nationality:</strong> <span id="c_nationality"></span></div>
                </div>

                <div class="flex justify-between mt-8">
                    <button type="button" onclick="goBack()"
                            class="bg-gray-400 text-white px-6 py-2 rounded-lg hover:bg-gray-500 transition">
                        ← Back
                    </button>
                    <button type="submit"
                            class="bg-teal-500 text-white px-6 py-2 rounded-lg hover:bg-teal-600 transition">
                        Submit ✔
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>

<script>
// success message disappear
let success_msg = document.getElementById("success_msg");
setTimeout(() => {
    if (success_msg) {
        success_msg.style.transition = "opacity 0.5s";
        success_msg.style.opacity = "0";
    }
}, 3000);
// ── Step navigation ──────────────────────────────────────────────
function goToStep2() {
    const form     = document.getElementById('registrationForm');
    const inputs   = form.querySelectorAll('[name]');
    let   allValid = true;

    // basic client-side required check
    inputs.forEach(el => {
        if (el.name === 'csrf_token' || el.name === 'stream') return;
        if (el.hasAttribute('required') && !el.value.trim()) {
            el.classList.add('border-red-400');
            allValid = false;
        } else {
            el.classList.remove('border-red-400');
        }
    });

    if (!allValid) {
        alert('Please fill in all required fields before proceeding.');
        return;
    }

    const formData = new FormData(form);
    for (let [key, value] of formData.entries()) {
        if (key === 'csrf_token') continue;
        let display = value;
        if (key === 'class' || key === 'stream') {
            const sel = form.querySelector(`[name="${key}"]`);
            display   = sel?.options[sel.selectedIndex]?.text || '—';
        }
        const el = document.getElementById('c_' + key);
        if (el) el.innerText = display || '—';
    }

    document.getElementById('step1').classList.add('hidden');
    document.getElementById('step2').classList.remove('hidden');
    document.getElementById('progressBar').style.width  = '100%';
    document.getElementById('step1Circle').className    = 'w-8 h-8 flex items-center justify-center rounded-full bg-teal-500 text-white font-bold';
    document.getElementById('step2Circle').className    = 'w-8 h-8 flex items-center justify-center rounded-full bg-teal-500 text-white font-bold';
}

function goBack() {
    document.getElementById('step2').classList.add('hidden');
    document.getElementById('step1').classList.remove('hidden');
    document.getElementById('progressBar').style.width  = '0';
    document.getElementById('step1Circle').className    = 'w-8 h-8 flex items-center justify-center rounded-full bg-yellow-400 text-white font-bold';
    document.getElementById('step2Circle').className    = 'w-8 h-8 flex items-center justify-center rounded-full bg-gray-300 text-white font-bold';
}

// ── Dynamic stream loader ────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    const classSelect   = document.getElementById('classSelect');
    const streamSelect  = document.getElementById('streamSelect');
    const streamWrapper = document.getElementById('streamWrapper');
    const streamLoading = document.getElementById('streamLoading');

    classSelect.addEventListener('change', function () {
        const classId = this.value;

        streamSelect.innerHTML  = '<option value="">Select Stream (Optional)</option>';
        streamSelect.disabled   = true;
        streamWrapper.classList.add('hidden');

        if (!classId) return;

        streamLoading.classList.remove('hidden');

        fetch('get-streams.php?class_id=' + encodeURIComponent(classId))
            .then(res => {
                if (!res.ok) throw new Error('Network error');
                return res.text();
            })
            .then(html => {
                streamLoading.classList.add('hidden');
                if (html.trim()) {
                    streamSelect.innerHTML += html;
                    streamSelect.disabled   = false;
                    streamWrapper.classList.remove('hidden');
                }
            })
            .catch(err => {
                streamLoading.classList.add('hidden');
                console.error('Stream fetch failed:', err);
            });
    });
});
</script>

<?php include("partials/footer.php"); ?>