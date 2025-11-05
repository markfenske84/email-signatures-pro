(function(){
    document.addEventListener('DOMContentLoaded', function(){
        // Track unsaved changes
        let hasUnsavedChanges = false;

        // Auto-dismiss success message after 3 seconds
        setTimeout(function(){
            const notice = document.querySelector('.notice.notice-success.is-dismissible');
            if(notice){
                notice.style.transition = 'opacity 0.4s';
                notice.style.opacity = '0';
                setTimeout(function(){
                    notice.remove();
                }, 400);
            }
        }, 3000);

        // Init color pickers (WordPress color picker requires jQuery)
        if(typeof jQuery !== 'undefined' && jQuery.fn.wpColorPicker){
            jQuery('.esp-color-field').wpColorPicker({
                change: function(){
                    hasUnsavedChanges = true;
                },
                clear: function(){
                    hasUnsavedChanges = true;
                }
            });
        }

        /* ------------------------- Media uploader ------------------------- */
        let file_frame;
        document.addEventListener('click', function(e){
            if(e.target.closest('.esp-upload-image')){
                e.preventDefault();
                const button = e.target.closest('.esp-upload-image');
                const targetInput = button.dataset.target;
                let inputEl = null;

                // For repeater table cells we don't have data-target; use sibling input
                if(!targetInput){
                    const td = button.closest('td');
                    if(td){
                        inputEl = td.querySelector('input.esp-image-url');
                    }
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
                    const attachment = file_frame.state().get('selection').first().toJSON();
                    const url = attachment.url;
                    
                    if(targetInput){
                        const input = document.getElementById(targetInput);
                        if(input){
                            input.value = url;
                            const wrap = input.closest('tr').querySelector('.esp-image-wrap');
                            if(wrap){
                                wrap.innerHTML = '<img src="'+url+'" style="max-width:100px;height:auto;display:block;margin-bottom:10px;" />';
                            }
                        }
                    } else if(inputEl) {
                        inputEl.value = url;
                        const preview = inputEl.closest('td').querySelector('.esp-icon-preview');
                        if(preview){
                            preview.innerHTML = '<img src="'+url+'" style="width:32px;height:32px;border-radius:4px;" />';
                        }
                    }
                    
                    // Mark as having unsaved changes
                    hasUnsavedChanges = true;
                });

                file_frame.open();
            }

            // Remove image
            if(e.target.closest('.esp-remove-image')){
                e.preventDefault();
                const button = e.target.closest('.esp-remove-image');
                const target = button.dataset.target;
                const input = document.getElementById(target);
                if(input){
                    input.value = '';
                    const wrap = input.closest('tr').querySelector('.esp-image-wrap');
                    if(wrap){
                        wrap.innerHTML = '';
                    }
                    // Mark as having unsaved changes
                    hasUnsavedChanges = true;
                }
            }

            // Remove row
            if(e.target.closest('.esp-remove-row')){
                e.preventDefault();
                const row = e.target.closest('tr');
                if(row){
                    row.remove();
                    reindexRows();
                    // Mark as having unsaved changes
                    hasUnsavedChanges = true;
                }
            }
        });

        /* ------------------------- Social Links repeater ------------------- */
        const addSocialBtn = document.getElementById('esp-add-social-row');
        if(addSocialBtn){
            addSocialBtn.addEventListener('click', function(e){
                e.preventDefault();
                const tbody = document.querySelector('#esp-social-links-table tbody');
                if(!tbody) return;
                
                const index = tbody.querySelectorAll('tr').length;
                const row = document.createElement('tr');
                row.innerHTML = 
                    '<td class="esp-icon-cell"><span class="esp-drag-handle dashicons dashicons-move" title="Drag to reorder"></span><div class="esp-icon-preview"></div><input type="hidden" class="esp-image-url" name="esp_settings[social_links]['+index+'][icon]" value="" /> <button type="button" class="button button-small esp-upload-image" title="Edit icon"><span class="dashicons dashicons-edit"></span></button></td>' +
                    '<td><input type="url" class="regular-text" name="esp_settings[social_links]['+index+'][url]" value="" placeholder="https://" /></td>' +
                    '<td><button type="button" class="button button-small esp-remove-row" title="Remove"><span class="dashicons dashicons-trash"></span></button></td>';
                tbody.appendChild(row);
                reindexRows();
                // Mark as having unsaved changes
                hasUnsavedChanges = true;
            });
        }

        /* ------------------------- Sortable ------------------------------ */
        function reindexRows(){
            const rows = document.querySelectorAll('#esp-social-links-table tbody tr');
            rows.forEach(function(row, i){
                const inputs = row.querySelectorAll('input');
                inputs.forEach(function(input){
                    let name = input.getAttribute('name');
                    if(name){
                        name = name.replace(/social_links\]\[\d+\]/, 'social_links]['+i+']');
                        input.setAttribute('name', name);
                    }
                });
            });
        }

        // HTML5 drag and drop for sortable table
        const tbody = document.querySelector('#esp-social-links-table tbody');
        if(tbody){
            let draggedRow = null;

            tbody.addEventListener('dragstart', function(e){
                if(e.target.closest('.esp-drag-handle')){
                    draggedRow = e.target.closest('tr');
                    draggedRow.style.opacity = '0.4';
                    e.dataTransfer.effectAllowed = 'move';
                }
            });

            tbody.addEventListener('dragend', function(e){
                if(draggedRow){
                    draggedRow.style.opacity = '1';
                    draggedRow = null;
                    reindexRows();
                    hasUnsavedChanges = true;
                }
            });

            tbody.addEventListener('dragover', function(e){
                if(draggedRow && e.target.closest('tr')){
                    e.preventDefault();
                    e.dataTransfer.dropEffect = 'move';
                    const targetRow = e.target.closest('tr');
                    if(targetRow && targetRow !== draggedRow){
                        const rect = targetRow.getBoundingClientRect();
                        const offset = e.clientY - rect.top;
                        if(offset > rect.height / 2){
                            targetRow.parentNode.insertBefore(draggedRow, targetRow.nextSibling);
                        } else {
                            targetRow.parentNode.insertBefore(draggedRow, targetRow);
                        }
                    }
                }
            });

            // Make drag handles draggable
            tbody.querySelectorAll('.esp-drag-handle').forEach(function(handle){
                handle.closest('tr').setAttribute('draggable', 'true');
            });

            // Re-enable draggable for new rows
            const observer = new MutationObserver(function(mutations){
                mutations.forEach(function(mutation){
                    mutation.addedNodes.forEach(function(node){
                        if(node.nodeType === 1 && node.tagName === 'TR'){
                            node.setAttribute('draggable', 'true');
                        }
                    });
                });
            });
            observer.observe(tbody, {childList: true});
        }

        /* ------------------------- Unsaved Changes Warning ----------------- */
        // Monitor form changes
        const forms = document.querySelectorAll('form');
        forms.forEach(function(form){
            form.addEventListener('change', function(e){
                if(e.target.matches('input, select, textarea')){
                    hasUnsavedChanges = true;
                }
            });

            // Clear flag when form is submitted
            form.addEventListener('submit', function(){
                hasUnsavedChanges = false;
            });
        });

        // Warn when clicking tab links
        const tabLinks = document.querySelectorAll('.nav-tab-wrapper .nav-tab');
        tabLinks.forEach(function(link){
            link.addEventListener('click', function(e){
                if(hasUnsavedChanges){
                    if(!confirm('You have unsaved changes. If you leave this tab, your changes will be lost. Continue?')){
                        e.preventDefault();
                        return false;
                    }
                    hasUnsavedChanges = false;
                }
            });
        });

        // Warn when leaving page
        window.addEventListener('beforeunload', function(e){
            if(hasUnsavedChanges){
                e.preventDefault();
                e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
                return e.returnValue;
            }
        });
    });
})();
