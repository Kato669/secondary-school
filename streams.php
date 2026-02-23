<?php include("partials/header.php") ?>
<?php
    if(session_status() === PHP_SESSION_NONE) session_start();
    if(!empty($_SESSION['flash_success'])){
        echo '<div class="w-[95%] mx-auto my-4"><div class="px-4 py-2 bg-green-100 text-green-800 rounded">' . htmlspecialchars($_SESSION['flash_success']) . '</div></div>';
        unset($_SESSION['flash_success']);
    }
    if(!empty($_SESSION['flash_error'])){
        echo '<div class="w-[95%] mx-auto my-4"><div class="px-4 py-2 bg-red-100 text-red-800 rounded">' . htmlspecialchars($_SESSION['flash_error']) . '</div></div>';
        unset($_SESSION['flash_error']);
    }
?>

<div class="w-[95%] mx-auto my-6">

    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-700 uppercase">
            Welcome to Katojkalemba Secondary School
        </h1>

        <button id="openAddStream" type="button"
           class="flex items-center gap-2 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition">
            <i class="fa-solid fa-plus"></i>
            <span>Add New</span>
        </button>
    </div>

    <!-- Streams Card -->
    <div class="bg-white shadow rounded-lg overflow-hidden">

        <!-- Card Header -->
        <div class="bg-teal-500 text-white px-4 py-3 flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-sm"></i>
            <h2 class="font-semibold text-lg">Streams</h2>
        </div>

                <!-- Add Stream Modal -->
                <div id="addStreamModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
                    <div class="modal-panel bg-white rounded-lg w-1/3">
                        <div class="flex justify-between items-center px-4 py-2 border-b">
                            <h3 class="text-lg font-semibold">Add New Stream</h3>
                            <button id="closeAddStream" class="text-gray-600 hover:text-gray-800 text-2xl leading-none">&times;</button>
                        </div>
                        <form action="add-stream.php" method="post" class="p-4">
                            <div class="mb-4">
                                <label for="class_id" class="block text-sm font-medium text-gray-700">Class <span class="text-red-500">*</span></label>
                                <select id="class_id" name="class_id" required class="w-full rounded-lg border border-gray-300 px-3 py-2">
                                    <option value="">Select Class</option>
                                    <?php
                                        $classRes = mysqli_query($conn, "SELECT class_id, class_name FROM classes");
                                        if($classRes){
                                            while($crow = mysqli_fetch_assoc($classRes)){
                                                echo '<option value="' . $crow['class_id'] . '">' . htmlspecialchars($crow['class_name']) . '</option>';
                                            }
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label for="stream_name" class="block text-sm font-medium text-gray-700">Stream Name <span class="text-red-500">*</span></label>
                                <input id="stream_name" name="stream_name" required placeholder="E.G A" class="w-full rounded-lg border border-gray-300 px-3 py-2" />
                            </div>
                            <div class="flex justify-end gap-2">
                                <button type="button" id="modalCloseBtn" class="px-4 py-2 bg-white border rounded">Close</button>
                                <button type="submit" name="save_stream" class="px-4 py-2 bg-blue-500 text-white rounded">Save Stream</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Edit Stream Modal -->
                <div id="editStreamModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
                    <div class="modal-panel bg-white rounded-lg w-1/3">
                        <div class="flex justify-between items-center px-4 py-2 border-b">
                            <h3 class="text-lg font-semibold">Edit Stream</h3>
                            <button id="closeEditStream" class="text-gray-600 hover:text-gray-800 text-2xl leading-none">&times;</button>
                        </div>
                        <form action="edit-stream.php" method="post" class="p-4" id="editStreamForm">
                            <input type="hidden" name="stream_id" id="edit_stream_id" />
                            <div class="mb-4">
                                <label for="edit_class_id" class="block text-sm font-medium text-gray-700">Class <span class="text-red-500">*</span></label>
                                <select id="edit_class_id" name="class_id" required class="w-full rounded-lg border border-gray-300 px-3 py-2">
                                    <option value="">Select Class</option>
                                    <?php
                                        // populate classes for edit form
                                        $classRes2 = mysqli_query($conn, "SELECT class_id, class_name FROM classes");
                                        if($classRes2){
                                            while($crow2 = mysqli_fetch_assoc($classRes2)){
                                                echo '<option value="' . $crow2['class_id'] . '">' . htmlspecialchars($crow2['class_name']) . '</option>';
                                            }
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label for="edit_stream_name" class="block text-sm font-medium text-gray-700">Stream Name <span class="text-red-500">*</span></label>
                                <input id="edit_stream_name" name="stream_name" required placeholder="E.G A" class="w-full rounded-lg border border-gray-300 px-3 py-2" />
                            </div>
                            <div class="flex justify-end gap-2">
                                <button type="button" id="editModalCloseBtn" class="px-4 py-2 bg-white border rounded">Close</button>
                                <button type="submit" name="update_stream" class="px-4 py-2 bg-blue-500 text-white rounded">Update Stream</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200 text-sm">
                <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left border text-lg">S/N</th>
                        <th class="px-4 py-3 text-left border text-lg">Class</th>
                        <th class="px-4 py-3 text-left border text-lg">Stream</th>
                        <th class="px-4 py-3 text-left border text-lg">Action</th>
                    </tr>
                </thead>
                
                <tbody class="text-gray-700">
                    <?php
                        $query = "SELECT c.class_id, c.class_name, s.stream_id, s.stream_name
                                  FROM classes c
                                  JOIN streams s ON s.class_id = c.class_id
                                  ORDER BY c.class_name, s.stream_name";

                        $res = mysqli_query($conn, $query);

                        if($res && mysqli_num_rows($res) > 0){
                            $sn = 1;
                            $currentClass = null;
                            while($row = mysqli_fetch_assoc($res)){
                                $isFirstForClass = ($currentClass !== $row['class_id']);
                                if($isFirstForClass){
                                    $displaySn = $sn;
                                    $displayClass = htmlspecialchars($row['class_name']);
                                    $sn++;
                                    $currentClass = $row['class_id'];
                                } else {
                                    $displaySn = '';
                                    $displayClass = '';
                                }

                                echo '<tr class="border-b uppercase">';
                                echo '<td class="px-4 py-3 border align-top">' . ($displaySn !== '' ? $displaySn : '') . '</td>';
                                echo '<td class="px-4 py-3 border align-top">' . ($displayClass !== '' ? $displayClass : '') . '</td>';
                                  echo '<td class="px-4 py-3 border ">' . htmlspecialchars($row['stream_name'], ENT_QUOTES) . '</td>';
                                echo '<td class="px-4 py-3 border">'
                                     . '<div class="flex gap-2">'
                                     . '<a href="#" data-stream-id="' . $row['stream_id'] . '" data-class-id="' . $row['class_id'] . '" data-stream-name="' . htmlspecialchars($row['stream_name'], ENT_QUOTES) . '" class="openEditStream bg-teal-500 hover:bg-teal-600 text-white px-3 py-2 rounded">'
                                     . '<i class="fa-solid fa-pen-to-square"></i>'
                                     . '</a>'
                                     . '<a href="delete_stream.php?id=' . urlencode($row['stream_id']) . '" onclick="return confirm(\'Are you sure you want to delete this stream?\')" class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded">'
                                     . '<i class="fa-solid fa-trash"></i>'
                                     . '</a>'
                                     . '</div>'
                                     . '</td>';
                                echo '</tr>';
                            }
                        } else {
                            echo '<tr><td colspan="4" class="px-4 py-6 text-center text-gray-500">No streams found.</td></tr>';
                        }
                    ?>
                </tbody>
            </table>
        </div>

    </div>

</div>

<?php include("partials/footer.php") ?>

<script>
    (function(){
        const openBtn = document.getElementById('openAddStream');
        const modal = document.getElementById('addStreamModal');
        const closeBtn = document.getElementById('closeAddStream');
        const modalCloseBtn = document.getElementById('modalCloseBtn');
        const panel = modal?.querySelector('.modal-panel');

        // Edit modal elements
        const editModal = document.getElementById('editStreamModal');
        const closeEditBtn = document.getElementById('closeEditStream');
        const editModalCloseBtn = document.getElementById('editModalCloseBtn');
        const editForm = document.getElementById('editStreamForm');
        const editStreamId = document.getElementById('edit_stream_id');
        const editClassSelect = document.getElementById('edit_class_id');
        const editStreamName = document.getElementById('edit_stream_name');

        function openModal(){
            if(!modal) return;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            // allow layout then add open to trigger transition
            requestAnimationFrame(() => modal.classList.add('open'));
        }

        function closeModal(){
            if(!modal) return;
            modal.classList.remove('open');
            // after transition, hide
            const onEnd = (e) => {
                if(e.target === modal){
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    modal.removeEventListener('transitionend', onEnd);
                }
            };
            modal.addEventListener('transitionend', onEnd);
            // fallback
            setTimeout(() => {
                if(!modal.classList.contains('open')){
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
            }, 300);
        }

        function openEditModal(){
            if(!editModal) return;
            editModal.classList.remove('hidden');
            editModal.classList.add('flex');
            requestAnimationFrame(() => editModal.classList.add('open'));
        }

        function closeEditModal(){
            if(!editModal) return;
            editModal.classList.remove('open');
            const onEnd = (e) => {
                if(e.target === editModal){
                    editModal.classList.add('hidden');
                    editModal.classList.remove('flex');
                    editModal.removeEventListener('transitionend', onEnd);
                }
            };
            editModal.addEventListener('transitionend', onEnd);
            setTimeout(() => {
                if(!editModal.classList.contains('open')){
                    editModal.classList.add('hidden');
                    editModal.classList.remove('flex');
                }
            }, 300);
        }

        openBtn?.addEventListener('click', openModal);
        closeBtn?.addEventListener('click', closeModal);
        modalCloseBtn?.addEventListener('click', closeModal);
        modal?.addEventListener('click', function(e){ if(e.target === modal) closeModal(); });

        // edit handlers
        closeEditBtn?.addEventListener('click', closeEditModal);
        editModalCloseBtn?.addEventListener('click', closeEditModal);
        editModal?.addEventListener('click', function(e){ if(e.target === editModal) closeEditModal(); });

        // open and populate edit modal when edit buttons clicked
        document.querySelectorAll('.openEditStream').forEach(btn => {
            btn.addEventListener('click', function(e){
                e.preventDefault();
                const sid = this.getAttribute('data-stream-id');
                const cid = this.getAttribute('data-class-id');
                const sname = this.getAttribute('data-stream-name');
                if(editStreamId) editStreamId.value = sid;
                if(editClassSelect) editClassSelect.value = cid;
                if(editStreamName) editStreamName.value = sname;
                openEditModal();
            });
        });
    })();
</script>

<style>
    /* Modal backdrop fade */
    #addStreamModal,
    #editStreamModal {
        opacity: 0;
        transition: opacity 250ms ease;
    }
    #addStreamModal.open,
    #editStreamModal.open{
        opacity: 1;
    }

    /* Panel slide & scale */
    #addStreamModal .modal-panel,
    #editStreamModal .modal-panel{
        transform: translateY(-8px) scale(.98);
        opacity: 0;
        transition: transform 250ms ease, opacity 250ms ease;
    }
    #addStreamModal.open .modal-panel,
    #editStreamModal.open .modal-panel{
        transform: translateY(0) scale(1);
        opacity: 1;
    }
</style>