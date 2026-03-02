<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
$page_title = "Manage Admin Users";
include '../includes/admin_header.php';
include '../includes/admin_sidebar.php';

// Fetch all admins
$query = "SELECT id, username, email, created_at FROM users ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>

<div class="container-fluid pb-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Admin Management</h2>
            <p class="text-muted">Monitor and manage system administrators.</p>
        </div>
        <button class="btn btn-primary px-4 py-2 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="fas fa-plus-circle me-2"></i>Add New Admin
        </button>
    </div>

    <!-- Admin Users Table -->
    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 border-0 small text-uppercase text-muted">#</th>
                            <th class="py-3 border-0 small text-uppercase text-muted">Username</th>
                            <th class="py-3 border-0 small text-uppercase text-muted">Email</th>
                            <th class="py-3 border-0 small text-uppercase text-muted">Joined Date</th>
                            <th class="py-3 border-0 small text-uppercase text-muted text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while ($user = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td class="ps-4 border-0">
                                        <span class="text-muted small">#<?php echo $user['id']; ?></span>
                                    </td>
                                    <td class="border-0">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 p-2 rounded-circle me-3">
                                                <i class="fas fa-user-shield text-primary"></i>
                                            </div>
                                            <span class="text-dark fw-bold"><?php echo htmlspecialchars($user['username']); ?></span>
                                        </div>
                                    </td>
                                    <td class="border-0 text-muted">
                                        <?php echo htmlspecialchars($user['email']); ?>
                                    </td>
                                    <td class="border-0 text-muted small">
                                        <?php echo date('M d, Y', strtotime($user['created_at'])); ?>
                                    </td>
                                    <td class="border-0 text-center">
                                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                            <button class="btn btn-sm btn-outline-danger border-0 rounded-circle delete-user" data-id="<?php echo $user['id']; ?>" title="Remove Access">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        <?php else: ?>
                                            <span class="badge bg-light text-primary">Logged In</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center py-5 text-muted">No admin users found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 bg-primary text-white py-3">
                <h5 class="modal-title fw-bold"><i class="fas fa-user-plus me-2"></i>Add Admin User</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addUserForm">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Username</label>
                        <input type="text" name="username" class="form-control" required placeholder="Choose a unique username">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Email Address</label>
                        <input type="email" name="email" class="form-control" required placeholder="admin@example.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Password</label>
                        <input type="password" name="password" class="form-control" required placeholder="Secure password">
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Create Account</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Add User via AJAX
    $('#addUserForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: 'ajax_handler.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#addUserModal').modal('hide');
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('AJAX Error: ' + error + '\nResponse: ' + xhr.responseText);
            }
        });
    });

    // Delete User
    $('.delete-user').on('click', function() {
        var id = $(this).data('id');
        if (confirm('Are you sure you want to revoke admin access for this user?')) {
            $.ajax({
                url: 'ajax_handler.php',
                type: 'POST',
                data: { 
                    action: 'delete_user', 
                    id: id,
                    csrf_token: '<?php echo $_SESSION['csrf_token']; ?>' 
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert('AJAX Error: ' + error + '\nResponse: ' + xhr.responseText);
                }
            });
        }
    });
});
</script>

<?php include '../includes/admin_footer.php'; ?>