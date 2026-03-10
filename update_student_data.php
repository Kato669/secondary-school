<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

/* ──────────────────────────────────────────
   Helpers
────────────────────────────────────────── */
function sel($cur, $val): void { if ((string)$cur === (string)$val) echo ' selected'; }
function chk($cur, $val): void { if ((string)$cur === (string)$val) echo ' checked';  }

/* ──────────────────────────────────────────
   Handle SAVE (POST)  — must come BEFORE header
────────────────────────────────────────── */
$saveMessage = '';

include("partials/header.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_students'])) {
    $ids = $_POST['student_id'] ?? [];
    foreach ($ids as $sid) {
        $sid          = (int)$sid;
        $school_pay   = trim($_POST['school_pay'][$sid]   ?? '');
        $dob          = trim($_POST['dob'][$sid]          ?? '') ?: null;
        $lin          = trim($_POST['lin'][$sid]          ?? '');
        $nin          = trim($_POST['nin'][$sid]          ?? '');
        $gender       = trim($_POST['gender'][$sid]       ?? '');
        $residence    = trim($_POST['residence'][$sid]    ?? '');
        $entry_status = trim($_POST['entry_status'][$sid] ?? '');
        $contact      = trim($_POST['contact'][$sid]      ?? '');
        $parent_nin   = trim($_POST['parent_nin'][$sid]   ?? '');

        // students table
        $st = mysqli_prepare($conn, "UPDATE students SET lin=?, dob=?, gender=? WHERE student_id=?");
        if ($st) {
            mysqli_stmt_bind_param($st, 'sssi', $lin, $dob, $gender, $sid);
            mysqli_stmt_execute($st);
            mysqli_stmt_close($st);
        }

        // student_additional_info
        $st2 = mysqli_prepare($conn, "UPDATE student_additional_info SET school_pay=?, residence_status=?, entry_status=? WHERE student_id=?");
        if ($st2) {
            mysqli_stmt_bind_param($st2, 'sssi', $school_pay, $residence, $entry_status, $sid);
            mysqli_stmt_execute($st2);
            mysqli_stmt_close($st2);
        }

        // parent contact
        if ($contact !== '') {
            $st3 = mysqli_prepare($conn, "UPDATE student_parent SET phone_1=? WHERE student_id=?");
            if ($st3) {
                mysqli_stmt_bind_param($st3, 'si', $contact, $sid);
                mysqli_stmt_execute($st3);
                mysqli_stmt_close($st3);
            }
        }

        // parent NIN
        if ($parent_nin !== '') {
            $st4 = mysqli_prepare($conn, "UPDATE student_parent SET nin=? WHERE student_id=?");
            if ($st4) {
                mysqli_stmt_bind_param($st4, 'si', $parent_nin, $sid);
                mysqli_stmt_execute($st4);
                mysqli_stmt_close($st4);
            }
        }
    }
    $saveMessage = count($ids) . ' student record(s) updated successfully.';
}

/* ──────────────────────────────────────────
   Filter values
────────────────────────────────────────── */
$selYear   = trim($_GET['year_of_study'] ?? '');
$selYear   = ($selYear !== '') ? (int)$selYear : '';
$selTerm   = trim($_GET['term']      ?? '');
$selClass  = trim($_GET['class_id']  ?? '');
$selStream = trim($_GET['stream_id'] ?? '');

/* Classes */
$classRows = [];
$cq = mysqli_query($conn, "SELECT class_id, class_name, shortcode FROM classes ORDER BY class_id");
if ($cq) {
    while ($r = mysqli_fetch_assoc($cq)) $classRows[] = $r;
} else {
    error_log("Classes query failed: " . mysqli_error($conn));
}

/* Streams for selected class */
$streamRows = [];
if ($selClass !== '') {
    $st = mysqli_prepare($conn, "SELECT stream_id, stream_name FROM streams WHERE class_id=? ORDER BY stream_name");
    if ($st) {
        mysqli_stmt_bind_param($st, 'i', $selClass);
        mysqli_stmt_execute($st);
        $sr = mysqli_stmt_get_result($st);
        if ($sr) {
            while ($r = mysqli_fetch_assoc($sr)) $streamRows[] = $r;
        }
        mysqli_stmt_close($st);
    }
}

$terms     = ['Term 1', 'Term 2', 'Term 3'];
$yearStart = (int)date('Y');
$yearEnd   = 2015;
$clubs     = ['None','Red Cross','Scripture Union','Debate','Music','Sports','Drama','Science'];

/* ──────────────────────────────────────────
   Fetch students
────────────────────────────────────────── */
$students   = [];
$searched   = ($selYear !== '' || $selTerm !== '' || $selClass !== '' || $selStream !== '');
$tableTitle = '';
$queryError = '';

if ($searched) {
    /* Build title */
    $tp = [];
    foreach ($classRows  as $c) if ((string)$c['class_id'] === (string)$selClass)  { $tp[] = $c['shortcode'] ?? strtoupper($c['class_name']); break; }
    foreach ($streamRows as $s) if ((string)$s['stream_id'] === (string)$selStream) { $tp[] = strtoupper($s['stream_name']); break; }
    if ($selTerm) $tp[] = strtoupper($selTerm);
    if ($selYear) $tp[] = $selYear;
    $tableTitle = 'STUDENTS OF ' . implode(' ', $tp);

    $sql = "SELECT s.student_id, s.student_name, s.lin, s.dob, s.gender,
                   c.class_name, c.shortcode, st.stream_name,
                   sai.term, sai.year_of_study, sai.school_pay,
                   sai.residence_status, sai.entry_status,
                   sp.parent_name, sp.nin, sp.phone_1 AS parent_contact, sp.nin AS parent_nin
            FROM students s
            INNER JOIN student_additional_info sai ON sai.student_id = s.student_id
            INNER JOIN classes c ON c.class_id = sai.class_id
            LEFT  JOIN streams st ON st.stream_id = sai.stream_id
            LEFT  JOIN student_parent sp ON sp.student_id = s.student_id
            WHERE 1=1";

    $params = [];
    $types  = '';

    if ($selYear   !== '') { $sql .= " AND sai.year_of_study = ?"; $types .= 'i'; $params[] = (int)$selYear; }
    if ($selTerm   !== '') { $sql .= " AND sai.term = ?";          $types .= 's'; $params[] = $selTerm; }
    if ($selClass  !== '') { $sql .= " AND sai.class_id = ?";      $types .= 'i'; $params[] = (int)$selClass; }
    if ($selStream !== '') { $sql .= " AND sai.stream_id = ?";     $types .= 'i'; $params[] = (int)$selStream; }
    $sql .= " ORDER BY s.student_name ASC";

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        $queryError = "Prepare failed: " . mysqli_error($conn);
    } else {
        if ($types) mysqli_stmt_bind_param($stmt, $types, ...$params);

        if (!mysqli_stmt_execute($stmt)) {
            $queryError = "Execute failed: " . mysqli_stmt_error($stmt);
        } else {
            $res = mysqli_stmt_get_result($stmt);
            if (!$res) {
                // Fallback for servers without mysqlnd
                $queryError = "get_result unavailable, using bind_result fallback.";
                mysqli_stmt_bind_result($stmt,
                    $f_student_id, $f_student_name, $f_lin, $f_nin, $f_dob, $f_gender,
                    $f_class_name, $f_shortcode, $f_stream_name,
                    $f_term, $f_year_of_study, $f_school_pay,
                    $f_residence_status, $f_entry_status,
                    $f_parent_name, $f_parent_contact, $f_parent_nin
                );
                while (mysqli_stmt_fetch($stmt)) {
                    $students[] = [
                        'student_id'       => $f_student_id,
                        'student_name'     => $f_student_name,
                        'lin'              => $f_lin,
                        // 'nin'              => $f_nin,
                        'dob'              => $f_dob,
                        'gender'           => $f_gender,
                        'class_name'       => $f_class_name,
                        'shortcode'        => $f_shortcode,
                        'stream_name'      => $f_stream_name,
                        'term'             => $f_term,
                        'year_of_study'    => $f_year_of_study,
                        'school_pay'       => $f_school_pay,
                        'residence_status' => $f_residence_status,
                        'entry_status'     => $f_entry_status,
                        'parent_name'      => $f_parent_name,
                        'parent_contact'   => $f_parent_contact,
                        'parent_nin'       => $f_parent_nin,
                    ];
                }
                $queryError = ''; // cleared — fallback worked
            } else {
                while ($r = mysqli_fetch_assoc($res)) $students[] = $r;
            }
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<div class="container m-4 py-2 w-[95%]">

    <!-- Breadcrumb -->
    <div class="flex items-center justify-between py-1 w-full">
        <div class="flex items-center gap-1 text-sm text-gray-500">
            <a href="<?php echo SITEURL ?>" class="hover:text-[#042a54] transition capitalize">home</a>
            <i class="fa-solid fa-angle-right text-xs px-1"></i>
            <a href="students.php" class="hover:text-[#042a54] transition capitalize">students</a>
            <i class="fa-solid fa-angle-right text-xs px-1"></i>
            <span class="text-[#042a54] font-medium capitalize">update students</span>
        </div>
        <a href="<?php echo SITEURL ?>students.php"
           class="bg-[#042a54] py-1.5 px-3 text-white text-sm rounded hover:bg-[#042a54]/90 transition flex items-center gap-2">
            <i class="fa-solid fa-plus text-xs"></i> Add Student
        </a>
    </div>

    <hr class="border-gray-300 my-3">

    <?php if ($saveMessage): ?>
    <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded text-sm flex items-center gap-2">
        <i class="fa-solid fa-circle-check"></i> <?php echo htmlspecialchars($saveMessage); ?>
    </div>
    <?php endif; ?>

    <?php if ($queryError): ?>
    <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded text-sm">
        <strong>DB Error:</strong> <?php echo htmlspecialchars($queryError); ?>
    </div>
    <?php endif; ?>

    <!-- Filter Form -->
    <form method="get" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <div class="flex flex-wrap items-end gap-4">

            <div class="flex flex-col w-40">
                <label class="text-xs font-medium text-gray-600 mb-1">Year of Study</label>
                <select name="year_of_study" class="border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-[#042a54] outline-none bg-white">
                    <option value="">All years</option>
                    <?php for ($y = $yearStart; $y >= $yearEnd; $y--): ?>
                        <option value="<?php echo $y; ?>" <?php sel($selYear, (string)$y); ?>><?php echo $y; ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="flex flex-col w-36">
                <label class="text-xs font-medium text-gray-600 mb-1">Term</label>
                <select name="term" class="border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-[#042a54] outline-none bg-white">
                    <option value="">All terms</option>
                    <?php foreach ($terms as $t): ?>
                        <option value="<?php echo htmlspecialchars($t); ?>" <?php sel($selTerm, $t); ?>><?php echo htmlspecialchars($t); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex flex-col w-40">
                <label class="text-xs font-medium text-gray-600 mb-1">Class</label>
                <select id="classSelect" name="class_id" class="border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-[#042a54] outline-none bg-white">
                    <option value="">All classes</option>
                    <?php foreach ($classRows as $c): ?>
                        <option value="<?php echo $c['class_id']; ?>" <?php sel($selClass, (string)$c['class_id']); ?>>
                            <?php echo htmlspecialchars(ucwords($c['class_name'])); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex flex-col w-40">
                <label class="text-xs font-medium text-gray-600 mb-1">
                    Stream <span id="streamLoading" class="text-gray-400 hidden text-xs">…</span>
                </label>
                <select id="streamSelect" name="stream_id"
                        class="border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-[#042a54] outline-none bg-white disabled:bg-gray-100"
                        <?php echo $selClass === '' ? 'disabled' : ''; ?>>
                    <option value="">All streams</option>
                    <?php foreach ($streamRows as $s): ?>
                        <option value="<?php echo $s['stream_id']; ?>" <?php sel($selStream, (string)$s['stream_id']); ?>>
                            <?php echo htmlspecialchars($s['stream_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="bg-[#042a54] text-white px-4 py-2 rounded text-sm hover:bg-[#042a54]/90 transition flex items-center gap-2">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i> Search
                </button>
                <?php if ($searched): ?>
                <a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>"
                   class="border border-gray-300 text-gray-600 px-4 py-2 rounded text-sm hover:bg-gray-50 transition flex items-center gap-2">
                    <i class="fa-solid fa-xmark text-xs"></i> Clear
                </a>
                <?php endif; ?>
            </div>
        </div>
    </form>

    <!-- Results -->
    <?php if ($searched): ?>
    <div class="mt-6">
        <?php if (empty($students)): ?>
        <div class="flex flex-col items-center justify-center py-16 text-gray-400">
            <i class="fa-solid fa-user-slash text-4xl mb-3"></i>
            <p class="text-sm">No students match the selected filters.</p>
            <?php if (ini_get('display_errors')): ?>
            <p class="text-xs mt-2 text-red-400">
                Debug — year: <?php echo var_export($selYear,true); ?>,
                term: <?php echo var_export($selTerm,true); ?>,
                class: <?php echo var_export($selClass,true); ?>,
                stream: <?php echo var_export($selStream,true); ?>
            </p>
            <?php endif; ?>
        </div>
        <?php else: ?>

        <!-- Header bar -->
        <div class="flex items-center justify-between bg-[#042a54] text-white px-4 py-2 rounded-t-lg">
            <span class="font-semibold text-sm tracking-wide">
                <i class="fa-solid fa-graduation-cap mr-2"></i>
                <?php echo htmlspecialchars($tableTitle ?: 'STUDENTS'); ?>
            </span>
            <div class="flex gap-1 text-xs">
                <button onclick="window.print()"  class="bg-white/20 hover:bg-white/30 px-2 py-1 rounded transition">Print</button>
                <button onclick="copyTable()"      class="bg-white/20 hover:bg-white/30 px-2 py-1 rounded transition">Copy</button>
                <button onclick="exportCSV()"      class="bg-white/20 hover:bg-white/30 px-2 py-1 rounded transition">CSV</button>
            </div>
        </div>

        <!-- Entries + Search controls -->
        <div class="flex items-center justify-between bg-gray-50 border-x border-gray-200 px-4 py-2 text-sm text-gray-600">
            <div class="flex items-center gap-2">
                Show
                <select id="entriesSelect" class="border rounded px-2 py-1 text-sm">
                    <option>10</option><option>25</option><option>50</option><option value="9999">All</option>
                </select>
                entries
            </div>
            <div class="flex items-center gap-2">
                Search:
                <input type="text" id="tableSearch" placeholder="Search name…"
                       class="border border-gray-300 rounded px-3 py-1 text-sm focus:ring-2 focus:ring-[#042a54] outline-none w-48">
            </div>
        </div>

        <!-- Inline-edit form -->
        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?' . http_build_query($_GET)); ?>">
            <input type="hidden" name="save_students" value="1">
            <div class="overflow-x-auto border-x border-gray-200">
                <table class="min-w-full text-xs" id="studentsTable">
                    <thead class="bg-gray-100 text-gray-700 border-b-2 border-gray-300">
                        <tr>
                            <th class="px-3 py-2 text-left font-semibold whitespace-nowrap border-r border-gray-200">STUDENT NAME</th>
                            <th class="px-3 py-2 text-left font-semibold whitespace-nowrap border-r border-gray-200">SECTION</th>
                            <th class="px-3 py-2 text-left font-semibold whitespace-nowrap border-r border-gray-200">GENDER</th>
                            <th class="px-3 py-2 text-left font-semibold whitespace-nowrap border-r border-gray-200">ENTRY</th>
                            <th class="px-3 py-2 text-left font-semibold whitespace-nowrap border-r border-gray-200">SCHOOL PAY CODE</th>
                            <th class="px-3 py-2 text-left font-semibold whitespace-nowrap border-r border-gray-200">DATE OF BIRTH</th>
                            <th class="px-3 py-2 text-left font-semibold whitespace-nowrap border-r border-gray-200">STUDENT LIN</th>
                            <th class="px-3 py-2 text-left font-semibold whitespace-nowrap border-r border-gray-200">STUDENT NIN</th>
                            <th class="px-3 py-2 text-left font-semibold whitespace-nowrap border-r border-gray-200">PARENT / GUARDIAN</th>
                            <th class="px-3 py-2 text-left font-semibold whitespace-nowrap border-r border-gray-200">CLUB</th>
                            <th class="px-3 py-2 text-left font-semibold whitespace-nowrap border-r border-gray-200">CONTACT</th>
                            <th class="px-3 py-2 text-left font-semibold whitespace-nowrap">PARENT NIN</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white" id="tableBody">
                        <?php foreach ($students as $s):
                            $sid = $s['student_id'];
                        ?>
                        <tr class="hover:bg-blue-50/40 transition student-row">

                            <td class="px-3 py-2 font-medium text-gray-800 whitespace-nowrap border-r border-gray-100">
                                <input type="hidden" name="student_id[]" value="<?php echo $sid; ?>">
                                <?php echo htmlspecialchars($s['student_name']); ?>
                            </td>

                            <td class="px-3 py-2 border-r border-gray-100">
                                <label class="flex items-center gap-1 cursor-pointer mb-1 whitespace-nowrap">
                                    <input type="radio" name="residence[<?php echo $sid; ?>]" value="DAY"
                                           <?php chk($s['residence_status'], 'DAY'); ?> class="accent-[#042a54]"> Day
                                </label>
                                <label class="flex items-center gap-1 cursor-pointer whitespace-nowrap">
                                    <input type="radio" name="residence[<?php echo $sid; ?>]" value="BOARDING"
                                           <?php chk($s['residence_status'], 'BOARDING'); ?> class="accent-[#042a54]"> Boarding
                                </label>
                            </td>

                            <td class="px-3 py-2 border-r border-gray-100">
                                <label class="flex items-center gap-1 cursor-pointer mb-1 whitespace-nowrap">
                                    <input type="radio" name="gender[<?php echo $sid; ?>]" value="Male"
                                           <?php chk($s['gender'], 'Male'); ?> class="accent-[#042a54]"> Male
                                </label>
                                <label class="flex items-center gap-1 cursor-pointer whitespace-nowrap">
                                    <input type="radio" name="gender[<?php echo $sid; ?>]" value="Female"
                                           <?php chk($s['gender'], 'Female'); ?> class="accent-[#042a54]"> Female
                                </label>
                            </td>

                            <td class="px-3 py-2 border-r border-gray-100">
                                <label class="flex items-center gap-1 cursor-pointer mb-1 whitespace-nowrap">
                                    <input type="radio" name="entry_status[<?php echo $sid; ?>]" value="CONTINUING"
                                           <?php chk($s['entry_status'], 'CONTINUING'); ?> class="accent-[#042a54]"> Continuing
                                </label>
                                <label class="flex items-center gap-1 cursor-pointer whitespace-nowrap">
                                    <input type="radio" name="entry_status[<?php echo $sid; ?>]" value="NEW"
                                           <?php chk($s['entry_status'], 'NEW'); ?> class="accent-[#042a54]"> New
                                </label>
                            </td>

                            <td class="px-3 py-2 border-r border-gray-100">
                                <input type="text" name="school_pay[<?php echo $sid; ?>]"
                                       value="<?php echo htmlspecialchars($s['school_pay'] ?? ''); ?>"
                                       class="border border-gray-300 rounded px-2 py-1 w-28 focus:ring-2 focus:ring-[#042a54] outline-none">
                            </td>

                            <td class="px-3 py-2 border-r border-gray-100">
                                <input type="date" name="dob[<?php echo $sid; ?>]"
                                       value="<?php echo htmlspecialchars($s['dob'] ?? ''); ?>"
                                       class="border border-gray-300 rounded px-2 py-1 w-32 focus:ring-2 focus:ring-[#042a54] outline-none">
                            </td>

                            <td class="px-3 py-2 border-r border-gray-100">
                                <input type="text" name="lin[<?php echo $sid; ?>]"
                                       value="<?php echo htmlspecialchars($s['lin'] ?? ''); ?>"
                                       class="border border-gray-300 rounded px-2 py-1 w-24 focus:ring-2 focus:ring-[#042a54] outline-none">
                            </td>

                            <td class="px-3 py-2 border-r border-gray-100">
                                <input type="text" name="nin[<?php echo $sid; ?>]"
                                       value="<?php echo htmlspecialchars($s['nin'] ?? ''); ?>"
                                       class="border border-gray-300 rounded px-2 py-1 w-28 focus:ring-2 focus:ring-[#042a54] outline-none">
                            </td>

                            <td class="px-3 py-2 text-gray-600 whitespace-nowrap border-r border-gray-100">
                                <?php echo htmlspecialchars($s['parent_name'] ?? '—'); ?>
                            </td>

                            <td class="px-3 py-2 border-r border-gray-100">
                                <select name="club[<?php echo $sid; ?>]"
                                        class="border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-[#042a54] outline-none bg-white">
                                    <?php foreach ($clubs as $club): ?>
                                        <option value="<?php echo htmlspecialchars($club); ?>"
                                            <?php sel($s['club'] ?? '', $club); ?>>
                                            <?php echo htmlspecialchars($club); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>

                            <td class="px-3 py-2 border-r border-gray-100">
                                <input type="text" name="contact[<?php echo $sid; ?>]"
                                       value="<?php echo htmlspecialchars($s['parent_contact'] ?? ''); ?>"
                                       placeholder="+256…"
                                       class="border border-gray-300 rounded px-2 py-1 w-28 focus:ring-2 focus:ring-[#042a54] outline-none">
                            </td>

                            <td class="px-3 py-2">
                                <input type="text" name="parent_nin[<?php echo $sid; ?>]"
                                       value="<?php echo htmlspecialchars($s['parent_nin'] ?? ''); ?>"
                                       class="border border-gray-300 rounded px-2 py-1 w-28 focus:ring-2 focus:ring-[#042a54] outline-none">
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Footer: count + SAVE -->
            <div class="flex items-center justify-between bg-gray-50 border border-t-0 border-gray-200 rounded-b-lg px-4 py-3">
                <span class="text-xs text-gray-500" id="showingInfo">
                    Showing 1 to <?php echo count($students); ?> of <?php echo count($students); ?> entries
                </span>
                <button type="submit"
                        class="bg-[#042a54] text-white px-8 py-2 rounded text-sm font-semibold hover:bg-[#042a54]/90 transition tracking-wide">
                    SAVE
                </button>
            </div>
        </form>

        <?php endif; ?>
    </div>

    <?php else: ?>
    <div class="flex flex-col items-center justify-center py-20 text-gray-400">
        <i class="fa-solid fa-filter text-4xl mb-3"></i>
        <p class="text-sm">Use the filters above and click <strong class="text-gray-500">Search</strong> to load students.</p>
    </div>
    <?php endif; ?>
</div>

<!-- Stream loader -->
<script>
(function () {
    const cs = document.getElementById('classSelect');
    const ss = document.getElementById('streamSelect');
    const sl = document.getElementById('streamLoading');

    async function loadStreams(id) {
        ss.innerHTML = '<option value="">All streams</option>';
        if (!id) { ss.disabled = true; return; }
        ss.disabled = true;
        if (sl) sl.classList.remove('hidden');
        try {
            const r = await fetch('get-streams.php?class_id=' + encodeURIComponent(id));
            const h = await r.text();
            ss.innerHTML = '<option value="">All streams</option>' + h;
            ss.disabled = false;
        } catch (e) {
            console.error('Stream load error:', e);
        } finally {
            if (sl) sl.classList.add('hidden');
        }
    }

    cs.addEventListener('change', e => loadStreams(e.target.value));
    if (cs.value) loadStreams(cs.value);
})();
</script>

<!-- Table search + entries filter -->
<script>
(function () {
    const searchInput = document.getElementById('tableSearch');
    const entriesSel  = document.getElementById('entriesSelect');
    const showingInfo = document.getElementById('showingInfo');
    const allRows     = () => [...document.querySelectorAll('#tableBody .student-row')];

    function applyFilters() {
        const q     = (searchInput?.value || '').toLowerCase();
        const limit = parseInt(entriesSel?.value || '10');
        let visible = 0;
        allRows().forEach(row => {
            const name        = row.querySelector('td:first-child').textContent.toLowerCase();
            const matchSearch = name.includes(q);
            const show        = matchSearch && visible < limit;
            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });
        const total = allRows().length;
        if (showingInfo)
            showingInfo.textContent = 'Showing 1 to ' + visible + ' of ' + total + ' entries' + (q ? ' (filtered)' : '');
    }

    searchInput?.addEventListener('input', applyFilters);
    entriesSel?.addEventListener('change', applyFilters);
    applyFilters();

    window.copyTable = function () {
        const rows = allRows().filter(r => r.style.display !== 'none');
        const text = rows.map(r => [...r.querySelectorAll('td')].map(td => td.textContent.trim()).join('\t')).join('\n');
        navigator.clipboard.writeText(text).then(() => alert('Copied to clipboard'));
    };

    window.exportCSV = function () {
        const rows   = allRows().filter(r => r.style.display !== 'none');
        const header = [...document.querySelectorAll('#studentsTable thead th')].map(th => '"' + th.textContent.trim() + '"').join(',');
        const body   = rows.map(r => [...r.querySelectorAll('td')].map(td => '"' + td.textContent.trim() + '"').join(',')).join('\n');
        const blob   = new Blob([header + '\n' + body], { type: 'text/csv' });
        const a      = document.createElement('a');
        a.href       = URL.createObjectURL(blob);
        a.download   = 'students.csv';
        a.click();
    };
})();
</script>

<?php include("./partials/footer.php"); ?>