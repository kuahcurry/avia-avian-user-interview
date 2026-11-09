<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard SIMSP - PT Avia Avian, Tbk</title>

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    <style>
        body {
            background-color: #f8f9fa;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        .header-title {
            font-weight: 700;
            color: #1e293b;
        }
        .table thead th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }
        .badge-status {
            font-size: 0.85rem;
            padding: 0.45em 0.75em;
        }
        /* Custom Select2 dropdown height alignment */
        .select2-container .select2-selection--single {
            height: 38px !important;
            padding: 5px;
        }
    </style>
</head>
<body>

<div class="container my-5">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <div>
            <h2 class="header-title mb-1">SIMSP - Spare Parts Inventory</h2>
            <p class="text-muted mb-0">Divisi Manufaktur PT Avia Avian, Tbk &bull; Sistem Monitoring &amp; Pencatatan Penggunaan</p>
        </div>
        <div>
            <button type="button" class="btn btn-primary px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modal-usage">
                <i class="bi bi-plus-lg"></i> + Catat Penggunaan Spare Part
            </button>
        </div>
    </div>

    <!-- Filter Section (Real-Time Tanpa Tombol Search) -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-4">
            <h6 class="card-subtitle mb-3 text-muted fw-semibold">FILTER INVENTARIS</h6>
            <div class="row g-3">
                <!-- Search Input with Debounce -->
                <div class="col-md-5">
                    <label class="form-label small text-muted">Pencarian Nama / Kode</label>
                    <input type="text" id="filter-search" class="form-control" placeholder="Ketik nama atau kode spare part...">
                </div>

                <!-- Category Filter -->
                <div class="col-md-4">
                    <label class="form-label small text-muted">Kategori</label>
                    <select id="filter-category" class="form-select">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div class="col-md-3">
                    <label class="form-label small text-muted">Status Stok</label>
                    <select id="filter-status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="safe">Safe (Aman)</option>
                        <option value="critical">Critical (Kritis)</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="ps-4">Part Code</th>
                            <th>Part Name</th>
                            <th>Category</th>
                            <th class="text-center">Current Stock</th>
                            <th class="text-center">Minimum Stock</th>
                            <th class="text-center pe-4">Status</th>
                        </tr>
                    </thead>
                    <tbody id="inventory-table-body">
                        <!-- Konten akan dimuat via jQuery AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 py-3 text-muted small d-flex justify-content-between align-items-center">
            <span id="table-info-count">Memuat ringkasan data...</span>
        </div>
    </div>
</div>

<!-- Modal Form Pencatatan Pemakaian Spare Part -->
<div class="modal fade" id="modal-usage" tabindex="-1" aria-labelledby="modalUsageLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalUsageLabel">Catat Penggunaan Spare Part</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Area Error Server / Validasi Klien -->
                <div id="modal-error-alert" class="alert alert-danger d-none py-2 px-3 small" role="alert"></div>

                <form id="form-usage" novalidate>
                    <!-- Select Machine (Searchable via Select2) -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Mesin <span class="text-danger">*</span></label>
                        <select id="usage-machine-id" class="form-select select2-machine" required style="width: 100%;">
                            <option value="">-- Pilih Mesin --</option>
                            @foreach ($machines as $m)
                                <option value="{{ $m->id }}">{{ $m->machine_code }} - {{ $m->name }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">Silakan pilih mesin yang menggunakan spare part.</div>
                    </div>

                    <!-- Select Spare Part (Searchable via Select2) -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Spare Part <span class="text-danger">*</span></label>
                        <select id="usage-spare-part-id" class="form-select select2-spare-part" required style="width: 100%;">
                            <option value="">-- Pilih Spare Part --</option>
                            @foreach ($spareParts as $sp)
                                <option value="{{ $sp->id }}" data-current-stock="{{ $sp->current_stock }}">
                                    [{{ $sp->part_code }}] {{ $sp->part_name }} (Sisa Stok: {{ $sp->current_stock }})
                                </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">Silakan pilih spare part yang digunakan.</div>
                    </div>

                    <!-- Input Quantity -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Jumlah Penggunaan (Qty) <span class="text-danger">*</span></label>
                        <input type="number" id="usage-quantity" class="form-control" min="1" placeholder="Contoh: 2" required>
                        <div class="invalid-feedback">Quantity harus berupa angka bulat dan lebih dari 0.</div>
                    </div>

                    <!-- Input Recorded By -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Dicatat Oleh (Nama Teknisi) <span class="text-danger">*</span></label>
                        <input type="text" id="usage-recorded-by" class="form-control" placeholder="Contoh: John Doe" required>
                        <div class="invalid-feedback">Nama petugas/teknisi wajib diisi.</div>
                    </div>

                    <!-- Textarea Notes -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Catatan (Notes)</label>
                        <textarea id="usage-notes" class="form-control" rows="2" placeholder="Alasan penggantian atau catatan teknis (opsional)..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="form-usage" id="btn-submit-usage" class="btn btn-primary px-4">
                    <span id="btn-spinner" class="spinner-border spinner-border-sm d-none me-1" role="status" aria-hidden="true"></span>
                    <span id="btn-text">Simpan Data</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Non-Blocking Toast Notification Container -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1080;">
    <div id="liveToast" class="toast align-items-center text-white border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body fw-semibold" id="toast-message">
                Notifikasi
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<!-- JavaScript Dependencies: jQuery, Select2, Bootstrap 5 Bundle -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Logika jQuery & AJAX Sesuai Ketentuan Soal 3 -->
<script>
$(document).ready(function() {

    // Setup global CSRF token untuk semua AJAX request jQuery
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        }
    });

    /**
     * Inisialisasi Searchable Dropdown Select2
     * dropdownParent diarahkan ke modal agar input pencarian select2 bisa diketik di dalam modal bootstrap
     */
    $('#usage-machine-id').select2({
        theme: 'bootstrap-5',
        dropdownParent: $('#modal-usage'),
        placeholder: '-- Pilih Mesin --',
        allowClear: true
    });

    $('#usage-spare-part-id').select2({
        theme: 'bootstrap-5',
        dropdownParent: $('#modal-usage'),
        placeholder: '-- Pilih Spare Part --',
        allowClear: true
    });

    /**
     * Helper: Menampilkan Toast Notification Non-Blocking
     * @param {string} message Pesan yang ditampilkan
     * @param {string} type 'success' (hijau) atau 'danger' (merah)
     */
    function showToast(message, type = 'success') {
        const toastEl = $('#liveToast');
        toastEl.removeClass('bg-success bg-danger bg-warning');
        
        if (type === 'danger') {
            toastEl.addClass('bg-danger');
        } else if (type === 'warning') {
            toastEl.addClass('bg-warning text-dark');
        } else {
            toastEl.addClass('bg-success');
        }

        $('#toast-message').text(message);
        const bsToast = new bootstrap.Toast(toastEl[0], { delay: 4000 });
        bsToast.show();
    }

    /**
     * Debounce Function:
     * Menunda eksekusi fungsi pencarian teks agar tidak membanjiri server dengan request AJAX
     * di setiap ketukan keyboard pengguna.
     */
    function debounce(callback, delay) {
        let timer;
        return function(...args) {
            clearTimeout(timer);
            timer = setTimeout(() => {
                callback.apply(this, args);
            }, delay);
        };
    }

    /**
     * Render Baris Tabel Inventory
     * @param {Array} items List data suku cadang dari API
     */
    function renderInventoryTable(items) {
        const tbody = $('#inventory-table-body');
        tbody.empty();

        if (!items || items.length === 0) {
            tbody.html(`
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                        Tidak ada data suku cadang yang sesuai dengan kriteria filter.
                    </td>
                </tr>
            `);
            $('#table-info-count').text('Menampilkan 0 data suku cadang');
            return;
        }

        let rowsHtml = '';
        items.forEach(function(item) {
            // Evaluasi status: Kritis jika current_stock <= minimum_stock
            const isCritical = item.current_stock <= item.minimum_stock;
            const statusBadge = isCritical 
                ? '<span class="badge bg-danger badge-status">Critical</span>' 
                : '<span class="badge bg-success badge-status">Safe</span>';

            rowsHtml += `
                <tr>
                    <td class="ps-4 fw-bold text-secondary">${item.part_code}</td>
                    <td class="fw-semibold text-dark">${item.part_name}</td>
                    <td><span class="badge bg-light text-dark border">${item.category || '-'}</span></td>
                    <td class="text-center fw-bold ${isCritical ? 'text-danger' : 'text-success'}">${item.current_stock}</td>
                    <td class="text-center text-muted">${item.minimum_stock}</td>
                    <td class="text-center pe-4">${statusBadge}</td>
                </tr>
            `;
        });

        tbody.html(rowsHtml);
        $('#table-info-count').text(`Menampilkan ${items.length} data suku cadang`);
    }

    /**
     * Load Data Inventory via jQuery AJAX
     * Mendukung mode silent update untuk auto-refresh tanpa memicu kedipan spinner
     * @param {boolean} isSilent Jika true, jangan tampilkan spinner loading
     */
    function loadInventoryData(isSilent = false) {
        const searchVal = $('#filter-search').val().trim();
        const categoryVal = $('#filter-category').val();
        const statusVal = $('#filter-status').val();

        // Tampilkan indikator loading hanya jika bukan silent auto-refresh
        if (!isSilent) {
            $('#inventory-table-body').html(`
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <div class="spinner-border text-primary spinner-border-sm me-2" role="status"></div>
                        Memuat data inventaris terbaru...
                    </td>
                </tr>
            `);
        }

        $.ajax({
            url: '/api/spare-parts/fetch',
            method: 'GET',
            data: {
                search: searchVal,
                category: categoryVal,
                status: statusVal
            },
            dataType: 'json',
            success: function(response) {
                const data = response.data || [];
                renderInventoryTable(data);
            },
            error: function(xhr, status, error) {
                console.error('AJAX Load Inventory Error:', error);
                // Tampilkan pesan error dan tombol retry jika request gagal
                $('#inventory-table-body').html(`
                    <tr>
                        <td colspan="6" class="text-center py-4 text-danger">
                            <p class="mb-2 fw-semibold">Gagal memuat data dari server (${xhr.status}: ${error}).</p>
                            <button type="button" id="btn-retry-fetch" class="btn btn-sm btn-outline-danger px-3">
                                <i class="bi bi-arrow-clockwise"></i> Coba Lagi (Retry)
                            </button>
                        </td>
                    </tr>
                `);
                $('#table-info-count').text('Gagal memuat data.');
            }
        });
    }

    // Event handler tombol Retry jika koneksi sebelumnya gagal
    $(document).on('click', '#btn-retry-fetch', function() {
        loadInventoryData();
    });

    /**
     * Event Listeners untuk Filtering Real-Time
     * 1. Search text menggunakan mekanisme Debounce (350ms)
     * 2. Category dan Status langsung terupdate saat dipilih (change)
     */
    $('#filter-search').on('input', debounce(function() {
        loadInventoryData();
    }, 350));

    $('#filter-category, #filter-status').on('change', function() {
        loadInventoryData();
    });

    /**
     * Handle Submit Form Recording Penggunaan Spare Part (Modal)
     */
    $('#form-usage').on('submit', function(e) {
        e.preventDefault();

        const form = $(this);
        const errorAlert = $('#modal-error-alert');
        errorAlert.addClass('d-none').empty();

        // Ambil nilai input
        const machineId = $('#usage-machine-id').val();
        const sparePartId = $('#usage-spare-part-id').val();
        const quantityUsed = parseInt($('#usage-quantity').val(), 10);
        const recordedBy = $('#usage-recorded-by').val().trim();
        const notes = $('#usage-notes').val().trim();

        // 1. Client-Side Validation
        let errors = [];
        if (!machineId) {
            errors.push('Mesin wajib dipilih.');
        }
        if (!sparePartId) {
            errors.push('Spare Part wajib dipilih.');
        }
        if (isNaN(quantityUsed) || quantityUsed <= 0) {
            errors.push('Quantity pemakaian harus berupa angka bulat lebih dari 0.');
        }
        if (!recordedBy) {
            errors.push('Nama teknisi pencatat (Recorded By) wajib diisi.');
        }

        if (errors.length > 0) {
            const errorHtml = '<ul class="mb-0 ps-3">' + errors.map(err => `<li>${err}</li>`).join('') + '</ul>';
            errorAlert.html(errorHtml).removeClass('d-none');
            return;
        }

        // Siapkan payload JSON
        const payload = {
            machine_id: parseInt(machineId, 10),
            spare_part_id: parseInt(sparePartId, 10),
            quantity_used: quantityUsed,
            recorded_by: recordedBy,
            notes: notes.length > 0 ? notes : null
        };

        // State UI saat proses AJAX berjalan: disable button & tampilkan loading spinner
        const submitBtn = $('#btn-submit-usage');
        const spinner = $('#btn-spinner');
        const btnText = $('#btn-text');

        submitBtn.prop('disabled', true);
        spinner.removeClass('d-none');
        btnText.text('Menyimpan...');

        // 2. Submit via jQuery AJAX
        $.ajax({
            url: '/api/spare-parts/usage/record',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(payload),
            dataType: 'json',
            success: function(response) {
                // Sukses: Bersihkan form, tutup modal, refresh tabel, tampilkan toast
                form[0].reset();
                $('#usage-machine-id').val(null).trigger('change');
                $('#usage-spare-part-id').val(null).trigger('change');

                // Tutup Bootstrap Modal
                const modalInstance = bootstrap.Modal.getInstance($('#modal-usage')[0]);
                if (modalInstance) {
                    modalInstance.hide();
                }

                // Tampilkan notifikasi toast sukses non-blocking
                showToast(response.message || 'Penggunaan spare part berhasil dicatat!', 'success');

                // Refresh data tabel inventory agar stok terbaru langsung tercermin
                loadInventoryData();
            },
            error: function(xhr) {
                console.error('AJAX Submit Error:', xhr);
                let messageHtml = 'Terjadi kesalahan saat menyimpan data.';

                if (xhr.status === 400 && xhr.responseJSON && xhr.responseJSON.error) {
                    // Kesalahan bisnis logic (contoh: stok kurang)
                    messageHtml = xhr.responseJSON.error;
                } else if (xhr.status === 422 && xhr.responseJSON) {
                    // Validasi server gagal
                    if (xhr.responseJSON.errors) {
                        const serverErrors = Object.values(xhr.responseJSON.errors).flat();
                        messageHtml = '<ul class="mb-0 ps-3">' + serverErrors.map(e => `<li>${e}</li>`).join('') + '</ul>';
                    } else if (xhr.responseJSON.message) {
                        messageHtml = xhr.responseJSON.message;
                    }
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    messageHtml = xhr.responseJSON.message;
                }

                errorAlert.html(messageHtml).removeClass('d-none');
            },
            complete: function() {
                // Pulihkan tombol submit
                submitBtn.prop('disabled', false);
                spinner.addClass('d-none');
                btnText.text('Simpan Data');
            }
        });
    });

    /**
     * Auto-Refresh Real-Time (Setiap 15 Detik)
     * Ketentuan:
     * - Hanya update data tabel (bukan seluruh halaman)
     * - Tidak menginterupsi pengguna saat sedang fokus mengetik di filter atau saat modal terbuka
     */
    setInterval(function() {
        const isModalOpen = $('#modal-usage').hasClass('show');
        const isUserFocusingInput = $('input:focus, select:focus, textarea:focus').length > 0;

        // Eksekusi auto-refresh silent jika user tidak sedang mengetik atau membuka modal
        if (!isModalOpen && !isUserFocusingInput) {
            loadInventoryData(true); // Silent mode: true
        }
    }, 15000);

    // Initial Load saat halaman pertama kali dibuka
    loadInventoryData();
});
</script>
</body>
</html>
