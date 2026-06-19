<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoSupply - Dashboard Konstruksi Berkelanjutan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .navbar-brand { font-weight: bold; color: #198754 !important; }
        .card-counter { border-left: 5px solid #198754; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">EcoSupply Construction</a>
        </div>
    </nav>

    <div class="container">
        
        <?php if(isset($_GET['status'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php 
                    if($_GET['status'] == 'success_add') echo "Data material berhasil ditambahkan!";
                    if($_GET['status'] == 'success_delete') echo "Data material berhasil dihapus!";
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm card-counter p-3 bg-white">
                    <h6 class="text-muted">Total Stok Material Aktif</h6>
                    <?php 
                        $res = $conn->query("SELECT SUM(stok_saat_ini) as total FROM materials");
                        $data = $res->fetch_assoc();
                        echo "<h3 class='fw-bold text-success'>".($data['total'] ?? 0)." Unit</h3>";
                    ?>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm card-counter p-3 bg-white" style="border-left-color: #0dcaf0;">
                    <h6 class="text-muted">Vendor Bersertifikasi Hijau</h6>
                    <?php 
                        $res = $conn->query("SELECT COUNT(*) as total FROM vendors WHERE sertifikasi_hijau='Ya'");
                        $data = $res->fetch_assoc();
                        echo "<h3 class='fw-bold text-info'>".$data['total']." Vendor</h3>";
                    ?>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm card-counter p-3 bg-white" style="border-left-color: #dc3545;">
                    <h6 class="text-muted">Log Limbah Terdeteksi</h6>
                    <?php 
                        $res = $conn->query("SELECT SUM(jumlah_limbah) as total FROM waste_logs");
                        $data = $res->fetch_assoc();
                        echo "<h3 class='fw-bold text-danger'>".($data['total'] ?? 0)." Kg</h3>";
                    ?>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0 fw-bold text-secondary">Manajemen Rantai Pasok Berkelanjutan</h5>
                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">+ Tambah Material</button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Foto</th>
                                <th>Nama Material</th>
                                <th>Kategori</th>
                                <th>Vendor Pemasok (Tabel Vendor)</th>
                                <th class="text-center">Stok Aktif</th>
                                <th class="text-center">Total Limbah (Tabel Limbah)</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // QUERY SQL JOIN 3 TABEL (materials, vendors melalui supply_orders, dan waste_logs)
                            $sql = "SELECT m.id, m.nama_material, m.kategori, m.stok_saat_ini, m.satuan, m.foto_material,
                                           v.nama_vendor, v.sertifikasi_hijau,
                                           COALESCE(SUM(w.jumlah_limbah), 0) as total_limbah
                                    FROM materials m
                                    LEFT JOIN supply_orders so ON m.id = so.material_id
                                    LEFT JOIN vendors v ON so.vendor_id = v.id
                                    LEFT JOIN waste_logs w ON m.id = w.material_id
                                    GROUP BY m.id";
                            
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    $foto = !empty($row['foto_material']) ? 'uploads/' . $row['foto_material'] : 'https://via.placeholder.com/100x70?text=No+Image';
                                    echo "<tr>";
                                    echo "<td><img src='$foto' class='img-thumbnail' style='width: 80px; height: 60px; object-fit: cover;'></td>";
                                    echo "<td><span class='fw-bold'>{$row['nama_material']}</span></td>";
                                    echo "<td><span class='badge bg-secondary'>{$row['kategori']}</span></td>";
                                    echo "<td>{$row['nama_vendor']} " . ($row['sertifikasi_hijau'] == 'Ya' ? '' : '') . "</td>";
                                    echo "<td class='text-center fw-bold text-success'>{$row['stok_saat_ini']} {$row['satuan']}</td>";
                                    echo "<td class='text-center text-danger fw-bold'>{$row['total_limbah']} Kg</td>";
                                    echo "<td class='text-center'>
                                            <a href='index.php?action=material_delete&id={$row['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Hapus material ini?\")'>Hapus</a>
                                          </td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7' class='text-center py-4 text-muted'>Belum ada data material. Silakan tambahkan.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="index.php?action=material_store" method="POST" enctype="multipart/form-data" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Material Ramah Lingkungan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Material</label>
                        <input type="text" name="nama_material" class="form-control" placeholder="Contoh: Semen Portland Slag" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <input type="text" name="kategori" class="form-control" placeholder="Contoh: Pengikat / Struktur" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stok Awal</label>
                            <input type="number" name="stok_saat_ini" class="form-control" value="0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Satuan</label>
                            <input type="text" name="satuan" class="form-control" placeholder="Contoh: Sak, Kg, Batang" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Upload Foto Bukti/Material</label>
                        <input type="file" name="foto_material" class="form-control" accept="image/*" required>
                        <small class="text-muted">Format yang didukung: JPG, JPEG, PNG</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>