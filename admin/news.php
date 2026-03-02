<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
$page_title = "Manage News";
include '../includes/admin_header.php';
include '../includes/admin_sidebar.php';

// Fetch news with category names
$query = "SELECT news.*, categories.name AS category_name 
          FROM news 
          LEFT JOIN categories ON news.category_id = categories.id 
          ORDER BY news.created_at ASC";
$result = mysqli_query($conn, $query);
?>

<!-- Page header with Add button -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>News Articles</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNewsModal">
        <i class="fas fa-plus"></i> Add News
    </button>
</div>

<!-- News Table -->
<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>#</th>
            <th>Image</th>
            <th>Title</th>
            <th>Category</th>
            <th>Premium</th>
            <th>Price</th>
            <th>Create On</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td>
                <?php if ($row['image']): ?>
                    <img src="../assets/uploads/<?php echo $row['image']; ?>" width="50" height="50" style="object-fit: cover;">
                <?php else: ?>
                    <span class="text-muted">No image</span>
                <?php endif; ?>
            </td>
            <td><?php echo htmlspecialchars($row['title']); ?></td>
            <td><?php echo htmlspecialchars($row['category_name'] ?: 'Uncategorized'); ?></td>
            <td>
                <?php if ($row['is_premium'] ?? 0): ?>
                    <span class="badge bg-warning text-dark">Premium</span>
                <?php else: ?>
                    <span class="badge bg-secondary">Free</span>
                <?php endif; ?>
            </td>
            <td><?php echo number_format($row['price'] ?? 0, 0); ?> Rwf</td>
            <td><?php echo date('d M Y', strtotime($row['created_at'])); ?></td>
            <td>
                <button class="btn btn-sm btn-warning edit-news" 
                        data-id="<?php echo $row['id']; ?>"
                        data-title="<?php echo htmlspecialchars($row['title']); ?>"
                        data-content="<?php echo htmlspecialchars($row['content']); ?>"
                        data-category="<?php echo $row['category_id']; ?>"
                        data-is_premium="<?php echo $row['is_premium'] ?? 0; ?>"
                        data-price="<?php echo $row['price'] ?? 0; ?>"
                        data-image="<?php echo $row['image']; ?>">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-danger delete-news" data-id="<?php echo $row['id']; ?>">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<!-- ========== MODALS (3D animated) ========== -->

<!-- Add News Modal -->
<div class="modal fade" id="addNewsModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content news-modal-3d">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Add New News</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addNewsForm" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">Content</label>
                        <textarea class="form-control" id="content" name="content" rows="5" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Category</label>
                                <select class="form-control" id="category_id" name="category_id">
                                    <option value="">-- Select Category --</option>
                                    <?php
                                    $cats = mysqli_query($conn, "SELECT * FROM categories");
                                    while ($cat = mysqli_fetch_assoc($cats)) {
                                        echo "<option value='{$cat['id']}'>{$cat['name']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="is_premium" class="form-label">Premium?</label>
                                <select class="form-control" id="is_premium" name="is_premium">
                                    <option value="0">No (Free)</option>
                                    <option value="1">Yes (Paid)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="price" class="form-label">Price (UGX)</label>
                                <input type="number" class="form-control" id="price" name="price" value="0" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Image (optional)</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save News</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit News Modal -->
<div class="modal fade" id="editNewsModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content news-modal-3d">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">Edit News</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editNewsForm" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="edit_title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_content" class="form-label">Content</label>
                        <textarea class="form-control" id="edit_content" name="content" rows="5" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_category_id" class="form-label">Category</label>
                                <select class="form-control" id="edit_category_id" name="category_id">
                                    <option value="">-- Select Category --</option>
                                    <?php
                                    mysqli_data_seek($cats, 0); // reset pointer
                                    while ($cat = mysqli_fetch_assoc($cats)) {
                                        echo "<option value='{$cat['id']}'>{$cat['name']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="edit_is_premium" class="form-label">Premium?</label>
                                <select class="form-control" id="edit_is_premium" name="is_premium">
                                    <option value="0">No (Free)</option>
                                    <option value="1">Yes (Paid)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="edit_price" class="form-label">Price (UGX)</label>
                                <input type="number" class="form-control" id="edit_price" name="price" value="0" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_image" class="form-label">Change Image (leave empty to keep current)</label>
                        <input type="file" class="form-control" id="edit_image" name="image" accept="image/*">
                        <div id="current_image_wrapper" class="mt-2"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Update News</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteNewsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content news-modal-3d">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this news article?</p>
                <input type="hidden" id="delete_id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteNews">Delete</button>
            </div>
        </div>
    </div>
</div>

<?php
include '../includes/admin_footer.php';
?>