<div id="export-opname-overlay" class="export-opname-overlay" aria-hidden="true">
    <div class="export-opname-panel" role="status" aria-live="polite">
        <div class="export-opname-icon">
            <i class="fas fa-file-excel"></i>
        </div>
        <h4 class="mb-2">Menyiapkan Export Opname</h4>
        <p id="export-opname-status" class="text-muted mb-3">Mengumpulkan data...</p>
        <div class="progress export-opname-progress mb-2">
            <div id="export-opname-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated"
                role="progressbar" style="width: 0%" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
            </div>
        </div>
        <div id="export-opname-percent" class="fw-bold fs-5">0%</div>
        <div id="export-opname-error" class="alert alert-danger mt-3 mb-0 d-none"></div>
        <div id="export-opname-actions" class="mt-3 d-none">
            <button id="export-opname-retry" type="button" class="btn btn-primary btn-sm">Coba lagi</button>
            <button id="export-opname-close" type="button" class="btn btn-secondary btn-sm">Tutup</button>
        </div>
    </div>
</div>

<style>
    .export-opname-overlay {
        position: fixed;
        inset: 0;
        z-index: 99999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(12, 18, 28, .82);
        backdrop-filter: blur(3px);
    }

    .export-opname-overlay.is-visible {
        display: flex;
    }

    .export-opname-panel {
        width: min(520px, 100%);
        padding: 30px;
        text-align: center;
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, .35);
    }

    .export-opname-icon {
        margin-bottom: 12px;
        color: #198754;
        font-size: 42px;
    }

    .export-opname-progress {
        height: 24px;
        border-radius: 12px;
    }
</style>

<script>
    (() => {
        if (window.opnameExportLoadingInitialized) return;
        window.opnameExportLoadingInitialized = true;

        const overlay = document.getElementById('export-opname-overlay');
        const progressBar = document.getElementById('export-opname-progress-bar');
        const percentText = document.getElementById('export-opname-percent');
        const statusText = document.getElementById('export-opname-status');
        const errorBox = document.getElementById('export-opname-error');
        const actions = document.getElementById('export-opname-actions');
        const retryButton = document.getElementById('export-opname-retry');
        const closeButton = document.getElementById('export-opname-close');
        let active = false;
        let progress = 0;
        let progressTimer = null;
        let lastUrl = null;

        const setProgress = (value) => {
            progress = Math.max(0, Math.min(100, Math.round(value)));
            progressBar.style.width = `${progress}%`;
            progressBar.setAttribute('aria-valuenow', progress);
            percentText.textContent = `${progress}%`;
        };

        const stopTimer = () => {
            if (progressTimer) window.clearInterval(progressTimer);
            progressTimer = null;
        };

        const startProgress = () => {
            setProgress(3);
            progressTimer = window.setInterval(() => {
                if (progress >= 95) return;
                const increment = progress < 55 ? 3 : progress < 80 ? 2 : 1;
                setProgress(Math.min(95, progress + increment));
                statusText.textContent = progress < 65
                    ? 'Mengumpulkan data dan menyusun sheet...'
                    : 'Membuat file Excel...';
            }, 900);
        };

        const filenameFromResponse = (response) => {
            const disposition = response.headers.get('Content-Disposition') || '';
            const utfMatch = disposition.match(/filename\*=UTF-8''([^;]+)/i);
            if (utfMatch) return decodeURIComponent(utfMatch[1]);
            const plainMatch = disposition.match(/filename="?([^";]+)"?/i);
            return plainMatch ? plainMatch[1] : 'Opname Gudang.xlsx';
        };

        const showError = (message) => {
            stopTimer();
            active = false;
            progressBar.classList.remove('progress-bar-animated');
            progressBar.classList.add('bg-danger');
            statusText.textContent = 'Export gagal';
            errorBox.textContent = message;
            errorBox.classList.remove('d-none');
            actions.classList.remove('d-none');
        };

        const runExport = async (url) => {
            if (active) return;
            active = true;
            lastUrl = url;
            errorBox.classList.add('d-none');
            actions.classList.add('d-none');
            progressBar.classList.add('progress-bar-animated');
            progressBar.classList.remove('bg-danger', 'bg-success');
            statusText.textContent = 'Mengumpulkan data...';
            overlay.classList.add('is-visible');
            overlay.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            startProgress();

            try {
                const response = await fetch(url, {
                    credentials: 'same-origin',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (!response.ok) {
                    throw new Error(response.status === 504
                        ? 'Server melewati batas waktu (504). Silakan coba lagi.'
                        : `Server gagal membuat file (HTTP ${response.status}).`);
                }

                const blob = await response.blob();
                stopTimer();
                setProgress(100);
                progressBar.classList.remove('progress-bar-animated');
                progressBar.classList.add('bg-success');
                statusText.textContent = 'File selesai. Mengunduh...';

                const objectUrl = URL.createObjectURL(blob);
                const downloadLink = document.createElement('a');
                downloadLink.href = objectUrl;
                downloadLink.download = filenameFromResponse(response);
                document.body.appendChild(downloadLink);
                downloadLink.click();
                downloadLink.remove();
                window.setTimeout(() => URL.revokeObjectURL(objectUrl), 60000);

                window.setTimeout(() => {
                    overlay.classList.remove('is-visible');
                    overlay.setAttribute('aria-hidden', 'true');
                    document.body.style.overflow = '';
                    active = false;
                }, 700);
            } catch (error) {
                showError(error.message || 'Export gagal. Silakan coba lagi.');
            }
        };

        document.addEventListener('click', (event) => {
            const link = event.target.closest('.js-export-opname');
            if (!link) return;
            event.preventDefault();
            runExport(link.href);
        });

        retryButton.addEventListener('click', () => runExport(lastUrl));
        closeButton.addEventListener('click', () => {
            overlay.classList.remove('is-visible');
            overlay.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        });
    })();
</script>
