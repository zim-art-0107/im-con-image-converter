jQuery(document).ready(function($) {
    var dropzone = $('#im-con-dropzone');
    var fileInput = $('#im-con-file-input');
    var selectBtn = $('#im-con-select-btn');
    var previewContainer = $('#im-con-preview');

    // Open file selector when button clicked.
    selectBtn.on('click', function(e) {
        e.preventDefault();
        fileInput.trigger('click');
    });

    // Handle file selection via input.
    fileInput.on('change', function(e) {
        var files = e.target.files;
        handleFiles(files);
    });

    // Drag and drop events.
    dropzone.on('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        dropzone.addClass('dragover');
    });
    dropzone.on('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        dropzone.removeClass('dragover');
    });
    dropzone.on('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        dropzone.removeClass('dragover');
        var dt = e.originalEvent.dataTransfer;
        if (dt && dt.files && dt.files.length) {
            handleFiles(dt.files);
        }
    });

    function handleFiles(files) {
        // Iterate through selected files.
        $.each(files, function(i, file) {
            // Only accept images.
            if (!file.type.match('image.*')) {
                alert('Only image files are allowed.');
                return true; // continue
            }
            var formData = new FormData();
            formData.append('action', 'im_con_convert_image');
            formData.append('nonce', imCon.nonce);
            formData.append('image', file);

            $.ajax({
                url: imCon.ajaxUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    // Show a simple loader.
                    previewContainer.append('<p>Processing ' + file.name + '...</p>');
                },
                success: function(response) {
                    if (response.success) {
                        // Append preview image.
                        previewContainer.append(response.data.preview);
                        // Append shortcode box.
                        previewContainer.append('<input type="text" readonly class="im-con-shortcode-box" value="' + response.data.shortcode + '" onclick="this.select();" />');
                    } else {
                        previewContainer.append('<p style="color:red;">Error: ' + response.data.message + '</p>');
                    }
                },
                error: function(xhr, status, error) {
                    previewContainer.append('<p style="color:red;">Ajax error: ' + error + '</p>');
                }
            });
        });
    }
});