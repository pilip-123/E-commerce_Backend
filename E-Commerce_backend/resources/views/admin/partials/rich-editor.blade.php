@push('styles')
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<style>
/* ═══════════════════════════════════════════════
   Section Card — Reusable Form Section
   ═══════════════════════════════════════════════ */

.section-card {
    background: var(--admin-surface, #fff);
    border: 1px solid var(--admin-border, #E5E7EB);
    border-radius: 16px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.section-card-header {
    padding: 20px 24px 0 24px;
}

.section-card-body {
    padding: 16px 24px 24px 24px;
}

/* ═══════════════════════════════════════════════
   Rich Text Editor — White Card Design
   ═══════════════════════════════════════════════ */

/* ─── Card Wrapper ─── */
.quill-editor-wrapper {
    border-radius: 12px;
    border: 1px solid #E5E7EB;
    background: #fff;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    transition: border-color 0.2s ease, box-shadow 0.25s ease;
    overflow: hidden;
}

.quill-editor-wrapper:focus-within {
    border-color: var(--admin-primary, #059669);
    box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.12), 0 1px 2px rgba(0, 0, 0, 0.05);
}

/* ─── Toolbar ─── */
.quill-editor-wrapper .ql-toolbar {
    border: 0;
    border-bottom: 1px solid #E5E7EB;
    background: #fff;
    padding: 12px 16px;
    border-radius: 12px 12px 0 0;
}

.quill-editor-wrapper .ql-toolbar .ql-formats {
    margin-right: 12px;
    position: relative;
}

/* Toolbar buttons */
.quill-editor-wrapper .ql-toolbar button {
    width: 32px;
    height: 32px;
    padding: 5px;
    border-radius: 8px;
    transition: background 0.15s ease;
    position: relative;
}

.quill-editor-wrapper .ql-toolbar button:hover {
    background: rgba(0, 0, 0, 0.06);
}

.quill-editor-wrapper .ql-toolbar button.ql-active {
    background: rgba(5, 150, 105, 0.10);
}

/* Color picker */
.quill-editor-wrapper .ql-toolbar .ql-picker {
    height: 32px;
    padding: 2px 8px;
    border-radius: 8px;
    transition: background 0.15s ease;
}

.quill-editor-wrapper .ql-toolbar .ql-picker:hover {
    background: rgba(0, 0, 0, 0.06);
}

.quill-editor-wrapper .ql-toolbar .ql-picker.ql-expanded {
    background: rgba(0, 0, 0, 0.04);
}

.quill-editor-wrapper .ql-toolbar .ql-picker-label {
    font-size: 13px;
    font-weight: 600;
    color: var(--admin-text, #1e293b);
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 0 4px;
}

.quill-editor-wrapper .ql-toolbar .ql-picker-label svg {
    width: 16px;
    height: 16px;
}

.quill-editor-wrapper .ql-toolbar .ql-picker-options {
    z-index: 1050;
    border-color: #E5E7EB;
    background: #fff;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    border-radius: 12px;
    padding: 6px;
    margin-top: 4px;
}

/* ─── Editor Content Area ─── */
.quill-editor-wrapper .ql-container {
    border: 0;
    font-size: 14px;
    font-family: inherit;
    line-height: 1.6;
}

.quill-editor-wrapper .ql-editor {
    min-height: 150px;
    padding: 16px;
    color: var(--admin-text, #1e293b);
    overflow-y: auto;
}

.quill-editor-wrapper .ql-editor:focus {
    outline: none;
}

/* Placeholder */
.quill-editor-wrapper .ql-editor.ql-blank::before {
    color: #9CA3AF;
    font-style: normal;
    font-size: 14px;
    left: 16px;
    right: 16px;
    opacity: 1;
}

/* ─── Scrollbar ─── */
.quill-editor-wrapper .ql-editor::-webkit-scrollbar {
    width: 5px;
}
.quill-editor-wrapper .ql-editor::-webkit-scrollbar-track {
    background: transparent;
}
.quill-editor-wrapper .ql-editor::-webkit-scrollbar-thumb {
    background: #D1D5DB;
    border-radius: 3px;
}
.quill-editor-wrapper .ql-editor::-webkit-scrollbar-thumb:hover {
    background: #9CA3AF;
}

/* ─── Dark Mode ─── */
[data-theme="dark"] .section-card {
    background: #1e293b;
    border-color: #374151;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
}

[data-theme="dark"] .quill-editor-wrapper {
    border-color: #374151;
    background: #1e293b;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
}

[data-theme="dark"] .quill-editor-wrapper:focus-within {
    border-color: #34d399;
    box-shadow: 0 0 0 3px rgba(52, 211, 153, 0.15), 0 1px 3px rgba(0, 0, 0, 0.2);
}

[data-theme="dark"] .quill-editor-wrapper .ql-toolbar {
    background: #1e293b;
    border-color: #374151;
}

[data-theme="dark"] .quill-editor-wrapper .ql-toolbar button:hover {
    background: rgba(255, 255, 255, 0.08);
}

[data-theme="dark"] .quill-editor-wrapper .ql-toolbar button.ql-active {
    background: rgba(52, 211, 153, 0.15);
}

[data-theme="dark"] .quill-editor-wrapper .ql-toolbar .ql-picker:hover {
    background: rgba(255, 255, 255, 0.08);
}

[data-theme="dark"] .quill-editor-wrapper .ql-toolbar .ql-picker.ql-expanded {
    background: rgba(255, 255, 255, 0.05);
}

[data-theme="dark"] .quill-editor-wrapper .ql-toolbar .ql-picker-label {
    color: #f1f5f9;
}

[data-theme="dark"] .quill-editor-wrapper .ql-toolbar .ql-picker-options {
    border-color: #475569;
    background: #1e293b;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
}

[data-theme="dark"] .quill-editor-wrapper .ql-editor {
    color: #f1f5f9;
}

[data-theme="dark"] .quill-editor-wrapper .ql-editor.ql-blank::before {
    color: #6B7280;
}

[data-theme="dark"] .ql-toolbar .ql-stroke {
    stroke: #9CA3AF;
}

[data-theme="dark"] .ql-toolbar .ql-fill {
    fill: #9CA3AF;
}

[data-theme="dark"] .ql-toolbar .ql-picker {
    color: #9CA3AF;
}

[data-theme="dark"] .ql-toolbar button:hover .ql-stroke,
[data-theme="dark"] .ql-toolbar button.ql-active .ql-stroke {
    stroke: #34d399;
}

[data-theme="dark"] .ql-toolbar button:hover .ql-fill,
[data-theme="dark"] .ql-toolbar button.ql-active .ql-fill {
    fill: #34d399;
}

[data-theme="dark"] .ql-toolbar .ql-picker:hover,
[data-theme="dark"] .ql-toolbar .ql-picker.ql-expanded {
    color: #34d399;
}

/* ─── Responsive ─── */
@media (max-width: 576px) {
    .section-card-header {
        padding: 16px 16px 0 16px;
    }
    .section-card-body {
        padding: 12px 16px 16px 16px;
    }
    .quill-editor-wrapper .ql-toolbar {
        padding: 10px 12px;
    }
    .quill-editor-wrapper .ql-toolbar .ql-formats {
        margin-right: 8px;
    }
    .quill-editor-wrapper .ql-editor {
        min-height: 130px;
        padding: 14px;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
(function() {
    function initRichEditor(textareaId) {
        var textarea = document.getElementById(textareaId);
        if (!textarea) return;

        var wrapper = document.createElement('div');
        wrapper.className = 'quill-editor-wrapper';
        textarea.parentNode.insertBefore(wrapper, textarea.nextSibling);
        textarea.style.display = 'none';

        var quill = new Quill(wrapper, {
            theme: 'snow',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    [{ 'color': [] }],
                    ['clean']
                ]
            },
            placeholder: 'Write your product description...'
        });

        quill.root.innerHTML = textarea.value;

        var form = textarea.closest('form');
        if (form) {
            form.addEventListener('submit', function() {
                textarea.value = quill.root.innerHTML;
            });
        }
    }

    @if (isset($editorId))
        initRichEditor('{{ $editorId }}');
    @endif
})();
</script>
@endpush
