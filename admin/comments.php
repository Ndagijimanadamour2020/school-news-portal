<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
$page_title = "Manage Comments";
include '../includes/admin_header.php';
include '../includes/admin_sidebar.php';

// Fetch comments with news title
$query = "SELECT comments.*, news.title AS news_title 
          FROM comments 
          LEFT JOIN news ON comments.news_id = news.id 
          ORDER BY comments.created_at ASC";
$result = mysqli_query($conn, $query);
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Comments</h2>
</div>

<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>#</th>
            <th>News Article</th>
            <th>Who Commented?
            <th>Comment</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td>
                    <?php if ($row['news_title']): ?>
                        <a href="../news-detail.php?id=<?php echo $row['news_id']; ?>" target="_blank">
                            <?php echo htmlspecialchars($row['news_title']); ?>
                        </a>
                    <?php else: ?>
                        <em class="text-muted">Deleted News</em>
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                <td><?php echo htmlspecialchars($row['comment']); ?></td>
                <td><?php echo date('d M Y H:i', strtotime($row['created_at'])); ?></td>
                <td>
                    <button class="btn btn-sm btn-danger delete-comment" data-id="<?php echo $row['id']; ?>">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="6" class="text-center">No comments found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<script>
$(document).ready(function() {
    $('.delete-comment').on('click', function() {
        var id = $(this).data('id');
        if(confirm('Are you sure you want to delete this comment?')) {
            $.ajax({
                url: 'ajax_handler.php',
                type: 'POST',
                data: { action: 'delete_comment', id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                }
            });
        }
    });
});
</script>

<?php
include '../includes/admin_footer.php';
?>