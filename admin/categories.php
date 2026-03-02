<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
$page_title = "Manage Categories";
include '../includes/admin_header.php';
include '../includes/admin_sidebar.php';

$result = mysqli_query($conn, "SELECT * FROM categories ORDER BY created_at ASC");
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Categories</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
        <i class="fas fa-plus"></i> Add Category
    </button>
</div>

<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Description</th>
            <th>Created On</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo htmlspecialchars($row['description']); ?></td>
            <td><?php echo date('d M Y', strtotime($row['created_at'])); ?></td>
            <td>
                <button class="btn btn-sm btn-warning edit-cat" 
                        data-id="<?php echo $row['id']; ?>"
                        data-name="<?php echo htmlspecialchars($row['name']); ?>"
                        data-description="<?php echo htmlspecialchars($row['description']); ?>">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-danger delete-cat" data-id="<?php echo $row['id']; ?>">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content news-modal-3d">
            <form id="addCategoryForm">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Add Category</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="cat_name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="cat_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="cat_description" class="form-label">Description</label>
                        <textarea class="form-control" id="cat_description" name="description" rows="3"></textarea>
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

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content news-modal-3d">
            <form id="editCategoryForm">
                <input type="hidden" name="id" id="edit_cat_id">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title">Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_cat_name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="edit_cat_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_cat_description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_cat_description" name="description" rows="3"></textarea>
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

<!-- Delete Category Modal -->
<div class="modal fade" id="deleteCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content news-modal-3d">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this category?</p>
                <p class="text-danger small">Note: All news articles in this category will be set to Uncategorized.</p>
                <input type="hidden" id="delete_cat_id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteCategory">Delete</button>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/admin_footer.php'; ?>