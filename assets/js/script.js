$(document).ready(function() {

    // ========== NEWS CRUD ==========

    // Add News
    $('#addNewsForm').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('action', 'add_news');
        $.ajax({
            url: 'ajax_handler.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#addNewsModal').modal('hide');
                    location.reload(); // simple refresh to show new entry
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('AJAX error!');
            }
        });
    });

    // Edit News - populate modal
    $('.edit-news').on('click', function() {
        var id = $(this).data('id');
        var title = $(this).data('title');
        var content = $(this).data('content');
        var category = $(this).data('category');
        var is_premium = $(this).data('is_premium');
        var price = $(this).data('price');
        var image = $(this).data('image');

        $('#edit_id').val(id);
        $('#edit_title').val(title);
        $('#edit_content').val(content);
        $('#edit_category_id').val(category);
        $('#edit_is_premium').val(is_premium);
        $('#edit_price').val(price);

        if (image) {
            $('#current_image_wrapper').html('<img src="../assets/uploads/'+image+'" width="100" class="mt-2"><p class="text-muted">Current image</p>');
        } else {
            $('#current_image_wrapper').html('');
        }
        $('#editNewsModal').modal('show');
    });

    // Update News
    $('#editNewsForm').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('action', 'edit_news');
        $.ajax({
            url: 'ajax_handler.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#editNewsModal').modal('hide');
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            }
        });
    });

    // Delete News - show confirmation
    $('.delete-news').on('click', function() {
        var id = $(this).data('id');
        $('#delete_id').val(id);
        $('#deleteNewsModal').modal('show');
    });

    $('#confirmDeleteNews').on('click', function() {
        var id = $('#delete_id').val();
        $.ajax({
            url: 'ajax_handler.php',
            type: 'POST',
            data: { action: 'delete_news', id: id },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#deleteNewsModal').modal('hide');
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            }
        });
    });

    // ========== CATEGORIES CRUD (similar pattern) ==========
    // Add Category
    $('#addCategoryForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: 'ajax_handler.php',
            type: 'POST',
            data: $(this).serialize() + '&action=add_category',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#addCategoryModal').modal('hide');
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            }
        });
    });

    // Edit Category - populate
    $('.edit-cat').on('click', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var desc = $(this).data('description');
        $('#edit_cat_id').val(id);
        $('#edit_cat_name').val(name);
        $('#edit_cat_description').val(desc);
        $('#editCategoryModal').modal('show');
    });

    // Update Category
    $('#editCategoryForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: 'ajax_handler.php',
            type: 'POST',
            data: $(this).serialize() + '&action=edit_category',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#editCategoryModal').modal('hide');
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            }
        });
    });

    // Delete Category - show modal
    $('.delete-cat').on('click', function() {
        var id = $(this).data('id');
        $('#delete_cat_id').val(id);
        $('#deleteCategoryModal').modal('show');
    });

    $('#confirmDeleteCategory').on('click', function() {
        var id = $('#delete_cat_id').val();
        $.ajax({
            url: 'ajax_handler.php',
            type: 'POST',
            data: { action: 'delete_category', id: id },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#deleteCategoryModal').modal('hide');
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            }
        });
    });

});