<?php include("partials/header.php"); ?>
<?php include_once __DIR__ . '/constants/constant.php'; ?>

<div class="w-[95%] mx-auto mt-6">

    <!-- Top Bar -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

        <!-- Search Dropdown -->
        <div>
            <select class="border rounded px-4 py-2 w-64 focus:ring-2 focus:ring-teal-400 outline-none">
                <option>Search Student</option>
                <option>By Name</option>
                <option>By LIN</option>
                <option>By Admission No</option>
            </select>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-2">
            <a href="quick_reg.php"
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
                + Add New
            </a>

            <a href="new_students.php"
               class="bg-teal-500 hover:bg-teal-600 text-white px-4 py-2 rounded shadow">
                👁 New Students
            </a>

            <a href="trashed_students.php"
               class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded shadow">
                🗑 Trashed Students
            </a>
        </div>
    </div>

    <!-- Filter Section -->
    <form method="GET" class="mt-6 bg-white p-6 shadow rounded">

        <div class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">

            <!-- Year -->
            <div>
                <label class="block text-sm font-medium mb-1">Year Of Study</label>
                <select name="year" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-teal-400 outline-none">
                    <?php
                    $currentYear = date('Y');
                    for($i = $currentYear; $i >= $currentYear-5; $i--){
                        $selected = ($_GET['year'] ?? '') == $i ? 'selected' : '';
                        echo "<option value='$i' $selected>$i</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Term -->
            <div>
                <label class="block text-sm font-medium mb-1">Term</label>
                <select name="term" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-teal-400 outline-none">
                    <option value="" class="selected disabled">Select</option>
                    <?php
                    $terms = ["Term 1","Term 2","Term 3"];
                    foreach($terms as $term){
                        $selected = ($_GET['term'] ?? '') == $term ? 'selected' : '';
                        echo "<option value='$term' $selected>$term</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Class -->
            <div>
                <label class="block text-sm font-medium mb-1">Class</label>
                <select name="class" id="classSelect"
                        class="w-full border rounded capitalize px-3 py-2 focus:ring-2 focus:ring-teal-400 outline-none">
                    <option value="" class="selected disabled">Select Class</option>
                    <?php
                    $classes = mysqli_query($conn, "SELECT * FROM classes ORDER BY class_id");
                    while($row = mysqli_fetch_assoc($classes)){
                        $selected = ($_GET['class'] ?? '') == $row['class_id'] ? 'selected' : '';
                        echo "<option class='capitalize' value='{$row['class_id']}' $selected>{$row['class_name']}</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Stream -->
            <div>
                <label class="block text-sm font-medium mb-1">Stream</label>
                <select name="stream" id="streamSelect"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-teal-400 outline-none">
                    <option value="" class="selected disabled">Select Stream</option>
                </select>
            </div>

            <!-- District -->
            <div>
                <label class="block text-sm font-medium mb-1">District</label>
                <select name="district"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-teal-400 outline-none">
                    <option value="" class="disabled selected">Select District</option>
                    <?php
                    $districts = mysqli_query($conn, "SELECT DISTINCT district FROM students ORDER BY district");
                    while($row = mysqli_fetch_assoc($districts)){
                        $selected = ($_GET['district'] ?? '') == $row['district'] ? 'selected' : '';
                        echo "<option value='{$row['district']}' $selected>{$row['district']}</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Search Button -->
            <div>
                <button type="submit"
                        class="bg-teal-500 hover:bg-teal-600 text-white px-6 py-2 rounded shadow w-full">
                    🔍 Search
                </button>
            </div>

        </div>
    </form>

    <!-- Students Table Header -->
    <?php
    // build filter query if any filter provided
    $results = [];
    $hasFilter = false;
    $conds = [];

    if(isset($_GET['year']) && $_GET['year'] !== ''){
        $hasFilter = true;
        $conds[] = 'ai.year_of_study = ' . (int)$_GET['year'];
    }
    if(isset($_GET['term']) && $_GET['term'] !== ''){
        $hasFilter = true;
        $term = mysqli_real_escape_string($conn, $_GET['term']);
        $conds[] = "ai.term = '$term'";
    }
    if(isset($_GET['class']) && $_GET['class'] !== ''){
        $hasFilter = true;
        $conds[] = 'ai.class_id = ' . (int)$_GET['class'];
    }
    if(isset($_GET['stream']) && $_GET['stream'] !== ''){
        $hasFilter = true;
        $conds[] = 'ai.stream_id = ' . (int)$_GET['stream'];
    }
    if(isset($_GET['district']) && $_GET['district'] !== ''){
        $hasFilter = true;
        $dist = mysqli_real_escape_string($conn, $_GET['district']);
        $conds[] = "s.district = '$dist'";
    }

    if($hasFilter){
        $where = '';
        if(!empty($conds)){
            $where = 'WHERE ' . implode(' AND ', $conds);
        }

        $sql = "SELECT s.student_id, s.lin, s.student_name,
                        c.class_name, st.stream_name,
                        ai.residence_status, s.gender, s.dob, s.district,
                        p.parent_name
                 FROM students s
                 LEFT JOIN student_additional_info ai ON s.student_id = ai.student_id
                 LEFT JOIN classes c ON ai.class_id = c.class_id
                 LEFT JOIN streams st ON ai.stream_id = st.stream_id
                 LEFT JOIN student_parent p ON s.student_id = p.student_id
                 $where
                 ORDER BY s.student_name";
        $res = mysqli_query($conn, $sql);
        if($res){
            while($row = mysqli_fetch_assoc($res)){
                $results[] = $row;
            }
        }
    }
    ?>

    <div class="mt-6 bg-teal-500 text-white px-6 py-3 rounded-t">
        <h3 class="font-semibold text-lg">
            <?php
                if(!$hasFilter){
                    echo 'Students Of Term';
                } else {
                    $parts = [];
                    if(!empty($_GET['class'])){
                        $cname = mysqli_fetch_assoc(mysqli_query($conn, "SELECT class_name FROM classes WHERE class_id=".(int)$_GET['class']))['class_name'];
                        $parts[] = $cname;
                    }
                    if(!empty($_GET['stream'])){
                        $sname = mysqli_fetch_assoc(mysqli_query($conn, "SELECT stream_name FROM streams WHERE stream_id=".(int)$_GET['stream']))['stream_name'];
                        $parts[] = $sname;
                    }
                    if(!empty($_GET['term'])){
                        $parts[] = $_GET['term'];
                    }
                    if(!empty($_GET['year'])){
                        $parts[] = $_GET['year'];
                    }
                    $title = !empty($parts) ? implode(' ', $parts) : 'Filtered Students';
                    echo htmlspecialchars($title);
                }
            ?>
        </h3>
    </div>

    <div class="bg-white shadow rounded-b p-6">
        <?php if($hasFilter): ?>
            <table id="studentsTable" class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-100 text-gray-700 uppercase">
                    <tr>
                        <th class="px-4 py-2">S/N</th>
                        <th class="px-4 py-2">RegNo</th>
                        <th class="px-4 py-2">Student Name</th>
                        <th class="px-4 py-2">Class</th>
                        <th class="px-4 py-2">Section</th>
                        <th class="px-4 py-2">Gender</th>
                        <th class="px-4 py-2">Age</th>
                        <th class="px-4 py-2">District</th>
                        <th class="px-4 py-2">Parent</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    <?php foreach($results as $i => $row):
                        $age = '';
                        if(!empty($row['dob'])){
                            $dob = new DateTime($row['dob']);
                            $age = $dob->diff(new DateTime())->y;
                        }
                    ?>
                    <tr>
                        <td class="px-4 py-2"><?php echo $i+1; ?></td>
                        <td class="px-4 py-2 uppercase"><?php echo htmlspecialchars($row['lin']); ?></td>
                        <td class="px-4 py-2 capitalize"><?php echo htmlspecialchars($row['student_name']); ?></td>
                        <td class="px-4 py-2 capitalize"><?php echo htmlspecialchars($row['class_name']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($row['residence_status']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($row['gender']); ?></td>
                        <td class="px-4 py-2"><?php echo $age; ?></td>
                        <td class="px-4 py-2 capitalize"><?php echo htmlspecialchars($row['district']); ?></td>
                        <td class="px-4 py-2 capitalize"><?php echo htmlspecialchars($row['parent_name']); ?></td>
                        <td>
                            <a href="student_profile.php?student_id=<?php echo $row['student_id']; ?>"
                               class="text-blue-600m">
                                <i class="fa-solid fa-eye bg-[#1d468f] p-2 rounded-full text-white"></i>
                                <!-- <i class="fa-solid fa-book bg-[#1d468f] p-2 rounded-full text-white"></i> -->
                            </a>
                             <a href="student_profile.php?student_id=<?php echo $row['student_id']; ?>"
                               class="text-blue-600m">
                                <!-- <i class="fa-solid fa-eye bg-[#1d468f] p-2 rounded-full text-white"></i> -->
                                <i class="fa-solid fa-book bg-[#1d468f] p-2 rounded-full text-white"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-gray-500 text-sm">Filtered results will appear here.</p>
        <?php endif; ?>
    </div>

</div>

<!-- DataTables resources (CDN) -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css" />

<script>
// Dynamic stream loader
(function(){
    const classSelectElem = document.getElementById('classSelect');
    const streamSelectElem = document.getElementById('streamSelect');

    function loadStreams(classId, selectedStream){
        if(!classId) return;
        streamSelectElem.innerHTML = '<option>Loading...</option>';
        fetch('get-streams.php?class_id=' + encodeURIComponent(classId))
            .then(res => res.text())
            .then(data => {
                streamSelectElem.innerHTML = '<option value="">Select Stream</option>' + data;
                if(selectedStream){
                    streamSelectElem.value = selectedStream;
                }
            });
    }

    classSelectElem.addEventListener('change', function() {
        loadStreams(this.value);
    });

    // on page load, if class pre-selected from GET, fetch streams
    document.addEventListener('DOMContentLoaded', function(){
        const initialClass = classSelectElem.value;
        const initialStream = '<?php echo isset($_GET["stream"]) ? addslashes($_GET["stream"]) : ""; ?>';
        if(initialClass){
            loadStreams(initialClass, initialStream);
        }
    });
})();
</script>

<!-- DataTables scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
$(document).ready(function(){
    if($.fn.DataTable && $('#studentsTable').length){
        $('#studentsTable').DataTable({
            dom: 'Bfrtip',
            buttons: ['print','copy','pdf','excel','csv'],
            pageLength: 25,
            order: []
        });
    }
});
</script>

<?php include("partials/footer.php"); ?>