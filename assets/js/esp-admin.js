(function($){
    $(function(){
        // Init color pickers
        $('.esp-color-field').wpColorPicker();

        /* ------------------------- Media uploader ------------------------- */
        var file_frame;
        $(document).on('click', '.esp-upload-image', function(e){
            e.preventDefault();
            var $button = $(this);
            var targetInput = $button.data('target');

            // For repeater table cells we don't have data-target; use sibling input
            if(!targetInput){
                var $input = $button.closest('td').find('input.esp-image-url');
            }

            // Create the media frame.
            file_frame = wp.media({
                title: esp_admin.title || 'Select or Upload Image',
                button: {
                    text: esp_admin.choose || 'Use this image'
                },
                multiple: false
            });

            file_frame.on('select', function(){
                var attachment = file_frame.state().get('selection').first().toJSON();
                var url = attachment.url;
                if(targetInput){
                    $('#' + targetInput).val(url);
                    $('#' + targetInput).closest('tr').find('.esp-image-wrap').html('<img src="'+url+'" style="max-width:100px;height:auto;display:block;margin-bottom:10px;" />');
                } else if($input) {
                    $input.val(url);
                    $input.closest('td').find('.esp-icon-preview').html('<img src="'+url+'" style="width:32px;height:32px;border-radius:4px;" />');
                }
            });

            file_frame.open();
        });

        // Remove image
        $(document).on('click', '.esp-remove-image', function(e){
            e.preventDefault();
            var target = $(this).data('target');
            $('#' + target).val('');
            $('#' + target).closest('tr').find('.esp-image-wrap').html('');
        });

        /* ------------------------- Social Links repeater ------------------- */
        $('#esp-add-social-row').on('click', function(e){
            e.preventDefault();
            var $table = $('#esp-social-links-table tbody');
            var index = $table.find('tr').length;
            var row = '<tr>' +
                      '<td class="esp-icon-cell"><span class="esp-drag-handle dashicons dashicons-move" title="Drag to reorder"></span><div class="esp-icon-preview"></div><input type="hidden" class="esp-image-url" name="esp_settings[social_links]['+index+'][icon]" value="" /> <button type="button" class="button button-small esp-upload-image" title="Edit icon"><span class="dashicons dashicons-edit"></span></button></td>' +
                      '<td><input type="url" class="regular-text" name="esp_settings[social_links]['+index+'][url]" value="" placeholder="https://" /></td>' +
                      '<td><button type="button" class="button button-small esp-remove-row" title="Remove"><span class="dashicons dashicons-trash"></span></button></td>' +
                      '</tr>';
            $table.append(row);

            reindexRows();
        });

        $(document).on('click', '.esp-remove-row', function(e){
            e.preventDefault();
            $(this).closest('tr').remove();
            // Reindex names
            reindexRows();
        });

        /* ------------------------- Sortable ------------------------------ */
        function reindexRows(){
            $('#esp-social-links-table tbody tr').each(function(i){
                $(this).find('input').each(function(){
                    var name = $(this).attr('name');
                    name = name.replace(/social_links\]\[\d+\]/, 'social_links]['+i+']');
                    $(this).attr('name', name);
                });
            });
        }

        var fixHelper = function(e, tr){
            var $originals = tr.children();
            var $helper = tr.clone();
            $helper.children().each(function(index){
                $(this).width($originals.eq(index).outerWidth());
            });
            return $helper;
        };

        $('#esp-social-links-table tbody').sortable({
            items: '> tr',
            handle: '.esp-drag-handle',
            helper: fixHelper,
            placeholder: 'esp-sort-placeholder',
            stop: function(){
                reindexRows();
            }
        });
    });
})(jQuery); 