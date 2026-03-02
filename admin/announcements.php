<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
$page_title = "Manage Announcements";
include '../includes/admin_header.php';
include '../includes/admin_sidebar.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';

    // Verify CSRF token
    if (!hash_equals($_SESSION['csrf_token'], $csrf_token)) {
        die("Invalid CSRF token.");
    }

    if ($action == 'add' || $action == 'edit') {
        $title = mysqli_real_escape_string($conn, trim($_POST['title']));
        $description = mysqli_real_escape_string($conn, trim($_POST['description']));
        $is_premium = intval($_POST['is_premium'] ?? 0);
        $price = floatval($_POST['price'] ?? 0.00);
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

        // File upload handling
        $file_path = '';
        $upload_dir = '../uploads/announcements/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == UPLOAD_ERR_OK) {
            $allowed_types = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt'];
            $file_ext = strtolower(pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION));
            if (in_array($file_ext, $allowed_types)) {
                $new_filename = uniqid() . '_' . time() . '.' . $file_ext;
                $destination = $upload_dir . $new_filename;
                if (move_uploaded_file($_FILES['attachment']['tmp_name'], $destination)) {
                    $file_path = 'uploads/announcements/' . $new_filename;
                } else {
                    $error = "Failed to upload file.";
                }
            } else {
                $error = "Invalid file type. Allowed: PDF, Word, Excel, PowerPoint, Text.";
            }
        }

        if ($action == 'add') {
            $sql = "INSERT INTO announcements (title, description, file_path, is_premium, price) VALUES ('$title', '$description', '$file_path', $is_premium, $price)";
            if (mysqli_query($conn, $sql)) {
                $success = "Announcement added successfully.";
            } else {
                $error = "Error: " . mysqli_error($conn);
            }
        } elseif ($action == 'edit' && $id > 0) {
            // If new file uploaded, update; else keep old
            if (!empty($file_path)) {
                // Delete old file if exists
                $old = mysqli_fetch_assoc(mysqli_query($conn, "SELECT file_path FROM announcements WHERE id = $id"));
                if ($old && !empty($old['file_path']) && file_exists('../' . $old['file_path'])) {
                    unlink('../' . $old['file_path']);
                }
                $sql = "UPDATE announcements SET title='$title', description='$description', file_path='$file_path', is_premium=$is_premium, price=$price WHERE id=$id";
            } else {
                $sql = "UPDATE announcements SET title='$title', description='$description', is_premium=$is_premium, price=$price WHERE id=$id";
            }
            if (mysqli_query($conn, $sql)) {
                $success = "Announcement updated successfully.";
            } else {
                $error = "Error: " . mysqli_error($conn);
            }
        }
    } elseif ($action == 'delete') {
        $id = intval($_POST['id']);
        // Delete file first
        $old = mysqli_fetch_assoc(mysqli_query($conn, "SELECT file_path FROM announcements WHERE id = $id"));
        if ($old && !empty($old['file_path']) && file_exists('../' . $old['file_path'])) {
            unlink('../' . $old['file_path']);
        }
        mysqli_query($conn, "DELETE FROM announcements WHERE id = $id");
        $success = "Announcement deleted.";
    }
}

// Fetch all announcements
$result = mysqli_query($conn, "SELECT * FROM announcements ORDER BY created_at ASC");
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Announcements</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="fas fa-plus"></i> Add Announcement
    </button>
</div>

<?php if (isset($success)): ?>
    <div class="alert alert-success alert-dismissible fade show"><?php echo $success; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>
<?php if (isset($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show"><?php echo $error; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>#</th>
            <th>Title</th>
            <th>Description</th>
            <th>Premium</th>
            <th>Price</th>
            <th>Attachment</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo e($row['title']); ?></td>
            <td><?php echo nl2br(e($row['description'])); ?></td>
            <td>
                <?php if ($row['is_premium'] ?? 0): ?>
                    <span class="badge bg-warning text-dark">Premium</span>
                <?php else: ?>
                    <span class="badge bg-secondary">Free</span>
                <?php endif; ?>
            </td>
            <td><?php echo number_format($row['price'] ?? 0, 0); ?> UGX</td>
            <td>
                <?php if ($row['file_path']): ?>
                    <?php $filename = basename($row['file_path']); ?>
                    <a href="<?php echo BASE_URL; ?>/download.php?file=<?php echo urlencode($filename); ?>" class="btn btn-sm btn-outline-secondary" download>
                        <i class="fas fa-download"></i> Download
                    </a>
                <?php else: ?>
                    <span class="text-muted">None</span>
                <?php endif; ?>
            </td>
            <td><?php echo date('d M Y', strtotime($row['created_at'])); ?></td>
            <td>
                <button class="btn btn-sm btn-warning edit-btn" 
                        data-id="<?php echo $row['id']; ?>" 
                        data-title="<?php echo e($row['title']); ?>" 
                        data-description="<?php echo e($row['description']); ?>"
                        data-is_premium="<?php echo $row['is_premium'] ?? 0; ?>"
                        data-price="<?php echo $row['price'] ?? 0; ?>">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-danger delete-btn" data-id="<?php echo $row['id']; ?>">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="action" value="add">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addModalLabel">Add Announcement</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Premium?</label>
                                <select name="is_premium" class="form-control">
                                    <option value="0">No (Free)</option>
                                    <option value="1">Yes (Paid)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Price (UGX)</label>
                                <input type="number" name="price" class="form-control" value="0" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Attachment (PDF, Word, Excel, etc.)</label>
                        <input type="file" name="attachment" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="editModalLabel">Edit Announcement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" id="edit_title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Premium?</label>
                                <select name="is_premium" id="edit_is_premium" class="form-control">
                                    <option value="0">No (Free)</option>
                                    <option value="1">Yes (Paid)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Price (UGX)</label>
                                <input type="number" name="price" id="edit_price" class="form-control" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Attachment (leave empty to keep current)</label>
                        <input type="file" name="attachment" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" id="delete_id">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this announcement? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Edit button click: populate modal fields
    $('.edit-btn').on('click', function() {
        var id = $(this).data('id');
        var title = $(this).data('title');
        var description = $(this).data('description');
        var is_premium = $(this).data('is_premium');
        var price = $(this).data('price');

        $('#edit_id').val(id);
        $('#edit_title').val(title);
        $('#edit_description').val(description);
        $('#edit_is_premium').val(is_premium);
        $('#edit_price').val(price);
        $('#editModal').modal('show');
    });

    // Delete button click: set the ID in the delete modal
    $('.delete-btn').on('click', function() {
        var id = $(this).data('id');
        $('#delete_id').val(id);
        $('#deleteModal').modal('show');
    });

    // Optional: Reset form fields when add modal is hidden (to avoid stale data)
    $('#addModal').on('hidden.bs.modal', function () {
        $(this).find('form')[0].reset();
    });
});
</script>

<?php include '../includes/admin_footer.php'; ?>