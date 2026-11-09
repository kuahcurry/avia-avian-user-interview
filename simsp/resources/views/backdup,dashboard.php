<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    body {
        font-family: Canterra, sans-serif;
        margin: 0;
        padding: 0;
    }
</style>
</head>
<body>
<div class="container my-5">
        <h1 class="mb-4">SIMSP - System Inventory Management Spare Part</h1>
    <div class="row g-3">
        <div class="col-md-4">
            <input type="text" id="filter-search" placeholder="Cari nama atau kode spare part..." class="form-control">
        </div>
        <div class="col-md-3">
            <select id="filter-category" class="form-select">
                <option value="">Semua Kategori</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat }}">{{ $cat }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">    
            <select id="filter-status" class="form-select">
                <option value="">Pilih Status</option>
                <option value="safe">Aman</option>
                <option value="critical">Kritis</option>
            </select>
        </div>
        <div class="col-md-3 text-end">
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modal-usage">+ Catat Pemakaian</button>
        </div>
    </div>

    <div class="card shadow-sm mt-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Part Code</th>
                        <th>Part Name</th>
                        <th>Category</th>
                        <th>Current Stock</th>
                        <th>Minimum Stock</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="inventory-table-body">
                    <!-- Data AJAX akan di-render ke sini -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="toast-container position-fixed top-0 end-0 p-3">
    <div id="liveToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toast-message">
                Notifikasi
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>