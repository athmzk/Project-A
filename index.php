<?php

require_once 'config.php';


$action = isset($_GET['action']) ? $_GET['action'] : 'dashboard';

switch ($action) {
    
    
    case 'material_store':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nama_material = $_POST['nama_material'];
            $kategori      = $_POST['kategori'];
            $stok_saat_ini = $_POST['stok_saat_ini'];
            $satuan        = $_POST['satuan'];
            
            $nama_file = $_FILES['foto_material']['name'];
            $tmp_file  = $_FILES['foto_material']['tmp_name'];
            $ukuran    = $_FILES['foto_material']['size'];
            
            $ekstensi_diperbolehkan = ['jpg', 'jpeg', 'png'];
            $x = explode('.', $nama_file);
            $ekstensi = strtolower(end($x));
            
            if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
                $nama_file_baru = time() . '_' . $nama_file;
                $folder_tujuan  = 'uploads/' . $nama_file_baru;
                
                if (move_uploaded_file($tmp_file, $folder_tujuan)) {
                    $query = "INSERT INTO materials (nama_material, kategori, stok_saat_ini, satuan, foto_material) 
                              VALUES ('$nama_material', '$kategori', '$stok_saat_ini', '$satuan', '$nama_file_baru')";
                    
                    if ($conn->query($query)) {
                        header("Location: index.php?action=dashboard&status=success_add");
                    } else {
                        echo "Gagal menyimpan ke database: " . $conn->error;
                    }
                }
            } else {
                header("Location: index.php?action=dashboard&status=invalid_file");
            }
        }
        break;

    case 'material_delete':
        $id = $_GET['id'];
        
        $res = $conn->query("SELECT foto_material FROM materials WHERE id = $id");
        $row = $res->fetch_assoc();
        if ($row['foto_material'] && file_exists('uploads/' . $row['foto_material'])) {
            unlink('uploads/' . $row['foto_material']);
        }

        $query = "DELETE FROM materials WHERE id = $id";
        if ($conn->query($query)) {
            header("Location: index.php?action=dashboard&status=success_delete");
        }
        break;

    case 'dashboard':
    default:
        include 'view.php';
        break;
}
?>