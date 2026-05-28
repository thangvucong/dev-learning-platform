function initPostComposer() {
    var root = document.querySelector('[data-post-composer]');
    if (!root) return;

    if (!window.toastui || !window.toastui.Editor) {
        // Editor is loaded via CDN on the create page
        return;
    }

    var editorEl = root.querySelector('[data-editor]');
    var inputEl = root.querySelector('textarea[name="content"]');
    if (!editorEl || !inputEl) return;

    var Editor = window.toastui.Editor;
    var editor = new Editor({
        el: editorEl,
        height: '520px',
        initialEditType: 'markdown',
        previewStyle: 'vertical',
        usageStatistics: false,
        placeholder: 'Viết bài của bạn bằng Markdown…',
        toolbarItems: [
            ['heading', 'bold', 'italic'],
            ['quote', 'ul', 'ol'],
            ['code', 'codeblock'],
            ['link', 'image']
        ],
        hooks: {
            addImageBlobHook: function (blob, callback) {
                var tokenMeta = document.querySelector('meta[name="csrf-token"]');
                var token = tokenMeta ? tokenMeta.getAttribute('content') : '';

                var form = new FormData();
                form.append('image', blob);

                fetch(root.getAttribute('data-upload-url'), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    body: form
                })
                    .then(function (res) { return res.json(); })
                    .then(function (json) {
                        if (json && json.url) {
                            callback(json.url, 'image');
                            return;
                        }
                        throw new Error('Upload failed');
                    })
                    .catch(function () {
                        callback('', 'Upload failed');
                    });
            }
        }
    });

    if (inputEl.value) {
        editor.setMarkdown(inputEl.value);
    }

    var formEl = root.closest('form');
    if (formEl) {
        formEl.addEventListener('submit', function () {
            inputEl.value = editor.getMarkdown();
        });
    }
}

window.addEventListener('load', initPostComposer, { once: true });

