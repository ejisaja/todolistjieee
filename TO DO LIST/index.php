<?php
$koneksi = mysqli_connect('localhost', 'root', '', 'todolistrpl');

// tambah tugas
if (isset($_POST['add_tugas'])) {
    $tugas = trim($_POST['tugas']);
    $deskripsi = trim($_POST['deskripsi']);
    $priority = intval($_POST['priority']);
    $due_date = $_POST['due_date'];

    if (!empty($tugas) && !empty($deskripsi) && $priority > 0 && !empty($due_date)) {
        mysqli_query($koneksi, "INSERT INTO tugas (tugas, deskripsi, priority, due_date, status) 
                                VALUES ('$tugas', '$deskripsi', '$priority', '$due_date', '0')");
    } else {
        echo "<script>alert('Semua kolom harus diisi dengan benar!');</script>";
    }
}

// tandai selesai
if (isset($_GET['complete'])) {
    $id = intval($_GET['complete']);
    mysqli_query($koneksi, "UPDATE tugas SET status = 1 WHERE id = $id");
    header("Location:index.php");
    exit();
}

// hapus tugas
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($koneksi, "DELETE FROM tugas WHERE id = $id");
    header("Location:index.php");
    exit();
}

// ambil semua data
$tugas = mysqli_query($koneksi, "SELECT * FROM tugas ORDER BY status ASC, priority ASC, due_date ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Aplikasi To Do List RPL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .text-decoration-line-through {
            text-decoration: line-through;
            color: gray;
        }
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        .deskripsi {
            font-size: 0.9em;
            color: #6c757d;
        }
    </style>
</head>
<body>
<div class="container my-4">
    <div class="card text-center shadow mb-4">
        <div class="card-body">
            <h2 class="card-title text-primary mb-0">📋 DAILY TO DO LIST REZY</h2>
            <p class="card-text text-muted">Catat, atur prioritas, dan selesaikan tugasmu dengan mudah!</p>
        </div>
    </div>

    <!-- Form -->
    <form method="POST" class="border rounded bg-light p-4 mb-4 shadow-sm">
        <div class="mb-3">
            <label class="form-label">Nama Tugas</label>
            <input type="text" name="tugas" class="form-control" required placeholder="Masukkan nama tugas">
        </div>
        <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="2" placeholder="Detail atau catatan tugas..." required></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Prioritas</label>
            <select name="priority" class="form-control" required>
                <option value="" disabled selected>Pilih prioritas</option>
                <option value="1">Penting dan Mendesak</option>
                <option value="2">Penting dan Tidak Mendesak</option>
                <option value="3">Tidak Penting Tapi Mendesak</option>
                <option value="4">Tidak Penting dan Tidak Mendesak</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Tanggal</label>
            <input type="date" name="due_date" class="form-control" value="<?= date('Y-m-d'); ?>" required>
        </div>
        <button type="submit" name="add_tugas" class="btn btn-primary w-100">Tambah</button>
    </form>

    <!-- Tabel -->
    <table class="table table-striped table-hover shadow-sm">
        <thead class="table-primary">
        <tr>
            <th>No</th>
            <th>Tugas</th>
            <th>Prioritas</th>
            <th>Tanggal</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $no = 1;
        $prioritas_text = [
            '1' => ['Penting & Mendesak', 'danger'],
            '2' => ['Penting & Tidak Mendesak', 'warning'],
            '3' => ['Tidak Penting Tapi Mendesak', 'info'],
            '4' => ['Tidak Penting & Tidak Mendesak', 'secondary']
        ];

        while ($row = mysqli_fetch_assoc($tugas)) {
            $status = $row['status'] == 0 ? 'Belum Selesai' : 'Selesai';
            $status_icon = $row['status'] == 0 ? 'bi-hourglass-split text-warning' : 'bi-check-circle-fill text-success';

            $pkey = strval($row['priority']);
            $priority_label = $prioritas_text[$pkey][0] ?? 'N/A';
            $priority_class = $prioritas_text[$pkey][1] ?? 'dark';
        ?>
            <tr>
                <td><?= $no++; ?></td>
                <td class="<?= $row['status'] == 1 ? 'text-decoration-line-through' : '' ?>">
                    <?= htmlspecialchars($row['tugas']); ?>
                    <div class="deskripsi"><?= htmlspecialchars($row['deskripsi']); ?></div>
                </td>
                <td><span class="badge bg-<?= $priority_class ?>"><?= $priority_label ?></span></td>
                <td><?= $row['due_date']; ?></td>
                <td><i class="bi <?= $status_icon ?>"></i> <?= $status; ?></td>
                <td>
                    <?php if ($row['status'] == 0) { ?>
                        <a href="?complete=<?= $row['id'] ?>" class="btn btn-outline-success btn-sm"><i class="bi bi-check2-circle"></i> Selesai</a>
                    <?php } ?>
                    <a href="?delete=<?= $row['id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Yakin ingin menghapus tugas ini?')">
                        <i class="bi bi-trash"></i> Hapus
                    </a>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
