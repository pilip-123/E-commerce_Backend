<div class="btn-group">
    <button type="button" class="btn btn-outline-success btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-download me-1"></i>Export
    </button>
    <ul class="dropdown-menu dropdown-menu-end shadow-sm rounded-3 border-0">
        <li>
            <a class="dropdown-item py-2" href="{{ $exportRoute }}?format=xlsx">
                <i class="bi bi-file-earmark-excel text-success me-2"></i>Excel (.xlsx)
            </a>
        </li>
        <li>
            <a class="dropdown-item py-2" href="{{ $exportRoute }}?format=csv">
                <i class="bi bi-file-earmark-spreadsheet text-info me-2"></i>CSV (.csv)
            </a>
        </li>
        <li>
            <a class="dropdown-item py-2" href="{{ $exportRoute }}?format=pdf">
                <i class="bi bi-file-earmark-pdf text-danger me-2"></i>PDF (.pdf)
            </a>
        </li>
        <li>
            <a class="dropdown-item py-2" href="{{ $exportRoute }}?format=doc">
                <i class="bi bi-file-earmark-word text-primary me-2"></i>Word Document (.doc)
            </a>
        </li>
        <li>
            <a class="dropdown-item py-2" href="{{ $exportRoute }}?format=html">
                <i class="bi bi-file-earmark-text text-warning me-2"></i>HTML Document (.html)
            </a>
        </li>
    </ul>
</div>
