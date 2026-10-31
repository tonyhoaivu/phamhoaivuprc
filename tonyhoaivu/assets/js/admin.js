/**
 * TonyHoaiVu CMS - Admin JavaScript
 * WordPress-inspired Editor & UI controllers
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Slugify helper function in JS
    const slugify = (text) => {
        return text
            .toString()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase()
            .trim()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');
    };

    // Auto-generate slug from title
    const titleInput = document.getElementById('title');
    const slugInput = document.getElementById('slug');
    if (titleInput && slugInput && (!slugInput.value || slugInput.value.trim() === '')) {
        titleInput.addEventListener('input', (e) => {
            slugInput.value = slugify(e.target.value);
        });
    }

    // 2. WYSIWYG Editor Setup
    const editorArea = document.getElementById('wysiwyg-content');
    const rawTextarea = document.getElementById('raw-content');
    const btnVisual = document.getElementById('btn-mode-visual');
    const btnHtml = document.getElementById('btn-mode-html');
    const headingSelector = document.getElementById('heading-selector');
    const postForm = document.getElementById('post-form');

    if (editorArea && rawTextarea) {
        // Toolbar buttons
        document.querySelectorAll('.tb-btn[data-cmd]').forEach((btn) => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const cmd = btn.getAttribute('data-cmd');
                const val = btn.getAttribute('data-val') || null;
                document.execCommand(cmd, false, val);
                editorArea.focus();
            });
        });

        // Headings dropdown
        if (headingSelector) {
            headingSelector.addEventListener('change', () => {
                const tag = headingSelector.value;
                document.execCommand('formatBlock', false, tag);
                editorArea.focus();
            });
        }

        // Insert Link
        const btnLink = document.getElementById('tb-insert-link');
        if (btnLink) {
            btnLink.addEventListener('click', () => {
                const url = prompt('Nhập địa chỉ URL liên kết (https://...):');
                if (url) {
                    document.execCommand('createLink', false, url);
                }
            });
        }

        // Insert Image
        const btnImg = document.getElementById('tb-insert-img');
        if (btnImg) {
            btnImg.addEventListener('click', () => {
                const imgUrl = prompt('Nhập URL hình ảnh:');
                if (imgUrl) {
                    document.execCommand('insertImage', false, imgUrl);
                }
            });
        }

        // Insert Table
        const btnTable = document.getElementById('tb-insert-table');
        if (btnTable) {
            btnTable.addEventListener('click', () => {
                const tableHtml = '<table border="1" style="width:100%; border-collapse:collapse; margin:15px 0;"><thead><tr><th style="padding:8px; border:1px solid #ccc;">Cột 1</th><th style="padding:8px; border:1px solid #ccc;">Cột 2</th><th style="padding:8px; border:1px solid #ccc;">Cột 3</th></tr></thead><tbody><tr><td style="padding:8px; border:1px solid #ccc;">Dữ liệu 1</td><td style="padding:8px; border:1px solid #ccc;">Dữ liệu 2</td><td style="padding:8px; border:1px solid #ccc;">Dữ liệu 3</td></tr></tbody></table><p></p>';
                document.execCommand('insertHTML', false, tableHtml);
            });
        }

        // Mode toggling: Visual <-> HTML
        if (btnVisual && btnHtml) {
            btnVisual.addEventListener('click', () => {
                editorArea.innerHTML = rawTextarea.value;
                editorArea.classList.remove('hidden');
                rawTextarea.classList.add('hidden');
                btnVisual.classList.add('active');
                btnHtml.classList.remove('active');
            });

            btnHtml.addEventListener('click', () => {
                rawTextarea.value = editorArea.innerHTML;
                editorArea.classList.add('hidden');
                rawTextarea.classList.remove('hidden');
                btnHtml.classList.add('active');
                btnVisual.classList.remove('active');
            });
        }

        // Sync to textarea before form submission
        if (postForm) {
            postForm.addEventListener('submit', () => {
                if (!editorArea.classList.contains('hidden')) {
                    rawTextarea.value = editorArea.innerHTML;
                }
            });
        }
    }

    // 3. Featured Image Live Preview Sync
    const featImgInput = document.getElementById('featured_image');
    const featImgPreview = document.getElementById('featured-image-preview');
    if (featImgInput && featImgPreview) {
        featImgInput.addEventListener('input', (e) => {
            const url = e.target.value.trim();
            if (url) {
                featImgPreview.innerHTML = `<img src="${url}" alt="" style="max-width:100%; height:auto; border-radius:4px;">`;
            } else {
                featImgPreview.innerHTML = '<span class="placeholder-text text-xs text-muted">Chưa chọn ảnh</span>';
            }
        });
    }

    // 4. Live Preview Modal
    const previewBtn = document.getElementById('btn-preview-post');
    const previewModal = document.getElementById('preview-modal');
    if (previewBtn && previewModal) {
        const previewTitle = document.getElementById('preview-modal-title');
        const previewBody = document.getElementById('preview-modal-body');

        previewBtn.addEventListener('click', () => {
            const currentTitle = titleInput ? titleInput.value : 'Tiêu đề xem trước';
            const currentContent = editorArea && !editorArea.classList.contains('hidden') ? editorArea.innerHTML : (rawTextarea ? rawTextarea.value : '');
            
            previewTitle.textContent = currentTitle || 'Xem trước bài viết';
            previewBody.innerHTML = `
                <h1 style="font-size:24px; font-weight:700; margin-bottom:15px;">${currentTitle}</h1>
                <div class="article-content" style="line-height:1.7;">${currentContent}</div>
            `;
            previewModal.classList.remove('hidden');
        });

        const closeBtn = previewModal.querySelector('.close-modal');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => previewModal.classList.add('hidden'));
        }
        previewModal.addEventListener('click', (e) => {
            if (e.target === previewModal) previewModal.classList.add('hidden');
        });
    }

    // 5. Copy Media URL buttons
    document.querySelectorAll('.btn-copy-url').forEach((btn) => {
        btn.addEventListener('click', () => {
            const url = btn.getAttribute('data-url');
            if (navigator.clipboard && url) {
                navigator.clipboard.writeText(url).then(() => {
                    const originalText = btn.textContent;
                    btn.textContent = 'Đã sao chép!';
                    setTimeout(() => { btn.textContent = originalText; }, 2000);
                });
            }
        });
    });

    // 6. Media File input change
    const mediaFileInput = document.getElementById('media_file');
    const selectedFileName = document.getElementById('selected-file-name');
    if (mediaFileInput && selectedFileName) {
        mediaFileInput.addEventListener('change', () => {
            if (mediaFileInput.files && mediaFileInput.files[0]) {
                selectedFileName.textContent = 'Tệp đã chọn: ' + mediaFileInput.files[0].name + ' (' + Math.round(mediaFileInput.files[0].size / 1024) + ' KB)';
                selectedFileName.classList.remove('hidden');
            }
        });
    }

    // 7. Check all boxes
    const cbAll = document.getElementById('cb-select-all-1');
    if (cbAll) {
        cbAll.addEventListener('change', () => {
            document.querySelectorAll('input[name="post_ids[]"]').forEach((cb) => {
                cb.checked = cbAll.checked;
            });
        });
    }
});
