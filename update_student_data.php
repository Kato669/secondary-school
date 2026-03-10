<?php include("partials/header.php") ?>

<div class="container m-4 py-2 w-[95%]">

    <!-- Breadcrumb + Action -->
    <div class="flex items-center justify-between py-1 w-full">
        <div class="flex items-center gap-1 text-sm text-gray-500">
            <a href="<?php echo SITEURL ?>" class="hover:text-[#042a54] capitalize transition">home</a>
            <i class="fa-solid fa-angle-right text-xs px-1"></i>
            <a href="students.php" class="hover:text-[#042a54] capitalize transition">students</a>
            <i class="fa-solid fa-angle-right text-xs px-1"></i>
            <span class="text-[#042a54] font-medium capitalize">update students</span>
        </div>
        <a href="<?php echo SITEURL ?>students.php"
           class="bg-[#042a54] py-1.5 px-3 text-white text-sm rounded hover:bg-[#042a54]/90 transition flex items-center gap-2">
            <i class="fa-solid fa-plus text-xs"></i>
            Add Student
        </a>
    </div>

    <hr class="border-gray-300 my-3">

    <?php
    /* ──────────────────────────────────────────
       Preserve filter values across requests
    ────────────────────────────────────────── */
    $selectedYear   = trim($_GET['year_of_study'] ?? '');
    $selectedTerm   = trim($_GET['term']          ?? '');
    $selectedClass  = trim($_GET['class_id']      ?? '');
    $selectedStream = trim($_GET['stream_id']     ?? '');

    /* ──────────────────────────────────────────
       Load classes
    ────────────────────────────────────────── */
    $classRows = [];
    $classQuery = mysqli_query($conn, "SELECT class_id, class_name FROM classes ORDER BY class_id");
    if ($classQuery) {
        while ($r = mysqli_fetch_assoc($classQuery)) {
            $classRows[] = $r;
        }
    }

    /* ──────────────────────────────────────────
       Load streams for the selected class
    ────────────────────────────────────────── */
    $streamRows = [];
    if ($selectedClass !== '') {
        $stmt = mysqli_prepare($conn, "SELECT stream_id, stream_name FROM streams WHERE class_id = ? ORDER BY stream_name");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'i', $selectedClass);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            while ($r = mysqli_fetch_assoc($res)) {
                $streamRows[] = $r;
            }
            mysqli_stmt_close($stmt);
        }
    }

    /* ──────────────────────────────────────────
       Static term options  (stored as "Term 1" etc.)
    ────────────────────────────────────────── */
    $terms = ['Term 1', 'Term 2', 'Term 3'];

    /* ──────────────────────────────────────────
       Year range
    ────────────────────────────────────────── */
    $yearStart = (int)date('Y');
    $yearEnd   = 2015;

    /* ──────────────────────────────────────────
       Build & run the student query when filters present
    ────────────────────────────────────────── */
    $students    = [];
    $searched    = ($selectedYear !== '' || $selectedTerm !== '' || $selectedClass !== '' || $selectedStream !== '');
    $filterError = '';

    if ($searched) {
        $sql    = "SELECT s.student_id, s.student_name, s.lin, s.gender,
                          c.class_name, st.stream_name,
                          sai.term, sai.year_of_study, sai.residence_status, sai.entry_status
                   FROM students s
                   INNER JOIN student_additional_info sai ON sai.student_id = s.student_id
                   INNER JOIN classes c ON c.class_id = sai.class_id
                   LEFT  JOIN streams st ON st.stream_id = sai.stream_id
                   WHERE 1=1";
        $params = [];
        $types  = '';

        if ($selectedYear !== '') {
            $sql    .= " AND sai.year_of_study = ?";
            $types  .= 's';
            $params[] = $selectedYear;
        }
        if ($selectedTerm !== '') {
            $sql    .= " AND sai.term = ?";
            $types  .= 's';
            $params[] = $selectedTerm;
        }
        if ($selectedClass !== '') {
            $sql    .= " AND sai.class_id = ?";
            $types  .= 'i';
            $params[] = (int)$selectedClass;
        }
        if ($selectedStream !== '') {
            $sql    .= " AND sai.stream_id = ?";
            $types  .= 'i';
            $params[] = (int)$selectedStream;
        }

        $sql .= " ORDER BY s.student_name ASC";

        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            if ($types) {
                mysqli_stmt_bind_param($stmt, $types, ...$params);
            }
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            while ($row = mysqli_fetch_assoc($result)) {
                $students[] = $row;
            }
            mysqli_stmt_close($stmt);
        } else {
            $filterError = 'Query error: ' . mysqli_error($conn);
        }
    }
    ?>

    <!-- ── Filter Form ── -->
    <form method="get" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="mt-4">
        <div class="flex flex-wrap items-end gap-4">

            <!-- Year of Study -->
            <div class="flex flex-col w-44">
                <label for="year_of_study" class="text-xs font-medium text-gray-600 mb-1 capitalize">Year of Study</label>
                <select id="year_of_study" name="year_of_study"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#042a54] focus:border-[#042a54] outline-none bg-white">
                    <option value="">All years</option>
                    <?php for ($y = $yearStart; $y >= $yearEnd; $y--): ?>
                        <option value="<?php echo $y; ?>" <?php selected($selectedYear, (string)$y); ?>>
                            <?php echo $y; ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>

            <!-- Term -->
            <div class="flex flex-col w-44">
                <label for="term" class="text-xs font-medium text-gray-600 mb-1 capitalize">Term</label>
                <select id="term" name="term"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#042a54] focus:border-[#042a54] outline-none bg-white">
                    <option value="">All terms</option>
                    <?php foreach ($terms as $t): ?>
                        <option value="<?php echo htmlspecialchars($t); ?>" <?php selected($selectedTerm, $t); ?>>
                            <?php echo htmlspecialchars($t); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Class -->
            <div class="flex flex-col w-44">
                <label for="classSelect" class="text-xs font-medium text-gray-600 mb-1 capitalize">Class</label>
                <select id="classSelect" name="class_id"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#042a54] focus:border-[#042a54] outline-none bg-white">
                    <option value="">All classes</option>
                    <?php foreach ($classRows as $class): ?>
                        <option value="<?php echo $class['class_id']; ?>"
                            <?php selected($selectedClass, (string)$class['class_id']); ?>>
                            <?php echo htmlspecialchars(ucwords($class['class_name'])); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Stream (dynamic) -->
            <div class="flex flex-col w-44">
                <label for="streamSelect" class="text-xs font-medium text-gray-600 mb-1 capitalize">
                    Stream
                    <span id="streamLoading" class="text-gray-400 hidden">…</span>
                </label>
                <select id="streamSelect" name="stream_id"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#042a54] focus:border-[#042a54] outline-none bg-white disabled:bg-gray-100 disabled:cursor-not-allowed"
                        <?php echo $selectedClass === '' ? 'disabled' : ''; ?>>
                    <option value="">All streams</option>
                    <?php foreach ($streamRows as $stream): ?>
                        <option value="<?php echo $stream['stream_id']; ?>"
                            <?php selected($selectedStream, (string)$stream['stream_id']); ?>>
                            <?php echo htmlspecialchars($stream['stream_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex items-end gap-2">
                <button type="submit"
                        class="bg-[#042a54] text-white px-4 py-2 rounded-lg text-sm hover:bg-[#042a54]/90 transition flex items-center gap-2">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    Search
                </button>
                <?php if ($searched): ?>
                <a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>"
                   class="border border-gray-300 text-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-50 transition flex items-center gap-2">
                    <i class="fa-solid fa-xmark text-xs"></i>
                    Clear
                </a>
                <?php endif; ?>
            </div>

        </div>
    </form>

    <!-- ── Results ── -->
    <?php if ($filterError): ?>
        <div class="mt-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
            <i class="fa-solid fa-circle-exclamation mr-2"></i>
            <?php echo htmlspecialchars($filterError); ?>
        </div>

    <?php elseif ($searched): ?>
        <div class="mt-6">
            <p class="text-sm text-gray-500 mb-3">
                <?php echo count($students); ?> student<?php echo count($students) !== 1 ? 's' : ''; ?> found
            </p>

            <?php if (empty($students)): ?>
                <div class="flex flex-col items-center justify-center py-16 text-gray-400">
                    <i class="fa-solid fa-user-slash text-4xl mb-3"></i>
                    <p class="text-sm">No students match the selected filters.</p>
                </div>

            <?php else: ?>
                <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                    <table class="min-w-full text-sm">
                        <thead class="bg-[#042a54] text-white">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium">#</th>
                                <th class="px-4 py-3 text-left font-medium">Name</th>
                                <th class="px-4 py-3 text-left font-medium">LIN</th>
                                <th class="px-4 py-3 text-left font-medium">Gender</th>
                                <th class="px-4 py-3 text-left font-medium">Class</th>
                                <th class="px-4 py-3 text-left font-medium">Stream</th>
                                <th class="px-4 py-3 text-left font-medium">Term</th>
                                <th class="px-4 py-3 text-left font-medium">Year</th>
                                <th class="px-4 py-3 text-left font-medium">Residence</th>
                                <th class="px-4 py-3 text-left font-medium">Status</th>
                                <th class="px-4 py-3 text-center font-medium">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach ($students as $i => $s): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-gray-400"><?php echo $i + 1; ?></td>
                                <td class="px-4 py-3 font-medium text-gray-800">
                                    <?php echo htmlspecialchars($s['student_name']); ?>
                                </td>
                                <td class="px-4 py-3 text-gray-500 font-mono text-xs">
                                    <?php echo htmlspecialchars($s['lin'] ?? '—'); ?>
                                </td>
                                <td class="px-4 py-3 text-gray-600">
                                    <?php echo htmlspecialchars($s['gender'] ?? '—'); ?>
                                </td>
                                <td class="px-4 py-3 text-gray-600">
                                    <?php echo htmlspecialchars(ucwords($s['class_name'])); ?>
                                </td>
                                <td class="px-4 py-3 text-gray-600">
                                    <?php echo htmlspecialchars($s['stream_name'] ?? '—'); ?>
                                </td>
                                <td class="px-4 py-3 text-gray-600">
                                    <?php echo htmlspecialchars($s['term'] ?? '—'); ?>
                                </td>
                                <td class="px-4 py-3 text-gray-600">
                                    <?php echo htmlspecialchars($s['year_of_study'] ?? '—'); ?>
                                </td>
                                <td class="px-4 py-3">
                                    <?php
                                    $res = $s['residence_status'] ?? '';
                                    $resClass = $res === 'BOARDING'
                                        ? 'bg-blue-100 text-blue-700'
                                        : 'bg-green-100 text-green-700';
                                    ?>
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium <?php echo $resClass; ?>">
                                        <?php echo htmlspecialchars($res ?: '—'); ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <?php
                                    $ent = $s['entry_status'] ?? '';
                                    $entClass = $ent === 'NEW'
                                        ? 'bg-yellow-100 text-yellow-700'
                                        : 'bg-purple-100 text-purple-700';
                                    ?>
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium <?php echo $entClass; ?>">
                                        <?php echo htmlspecialchars($ent ?: '—'); ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <a href="update-student.php?id=<?php echo $s['student_id']; ?>"
                                       class="inline-flex items-center gap-1 bg-[#042a54] text-white text-xs px-3 py-1.5 rounded hover:bg-[#042a54]/90 transition">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                        Edit
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

    <?php else: ?>
        <!-- No search yet — prompt the user -->
        <div class="flex flex-col items-center justify-center py-20 text-gray-400">
            <i class="fa-solid fa-filter text-4xl mb-3"></i>
            <p class="text-sm">Use the filters above and click <strong class="text-gray-500">Search</strong> to find students.</p>
        </div>
    <?php endif; ?>

</div>

<?php
/* ── Helper: selected() ── */
function selected($current, $value): void {
    if ((string)$current === (string)$value) echo ' selected';
}
?>

<!-- Dynamic stream loader -->
<script>
(function () {
    const classSelect  = document.getElementById('classSelect');
    const streamSelect = document.getElementById('streamSelect');
    const loadingSpan  = document.getElementById('streamLoading');

    async function loadStreams(classId) {
        streamSelect.innerHTML = '<option value="">All streams</option>';

        if (!classId) {
            streamSelect.disabled = true;
            return;
        }

        streamSelect.disabled = true;
        loadingSpan.classList.remove('hidden');

        try {
            const res  = await fetch('get-streams.php?class_id=' + encodeURIComponent(classId));
            const html = await res.text();
            streamSelect.innerHTML = '<option value="">All streams</option>' + html;
            streamSelect.disabled  = false;
        } catch (err) {
            console.error('Stream load error:', err);
        } finally {
            loadingSpan.classList.add('hidden');
        }
    }

    classSelect.addEventListener('change', e => loadStreams(e.target.value));

    // Re-populate streams on page load if a class is already selected
    if (classSelect.value) {
        loadStreams(classSelect.value);
    }
})();
</script>

<?php include("partials/footer.php") ?>