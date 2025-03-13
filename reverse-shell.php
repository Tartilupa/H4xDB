<?php
session_start();

// Pridobi trenutno mapo (dostop do celotnega strežnika)
$directory = isset($_GET['dir']) ? realpath($_GET['dir']) : __DIR__;

// Nalaganje datoteke
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $target_file = $directory . DIRECTORY_SEPARATOR . basename($_FILES['file']['name']);
    move_uploaded_file($_FILES['file']['tmp_name'], $target_file);
}

// Brisanje datoteke
if (isset($_GET['delete'])) {
    $file_to_delete = realpath($directory . DIRECTORY_SEPARATOR . $_GET['delete']);
    if (file_exists($file_to_delete)) {
        unlink($file_to_delete);
    }
    header('Location: ?dir=' . urlencode($directory));
    exit;
}

// Pridobivanje seznama datotek
$files = scandir($directory);
?><!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Manager</title>
</head>
<body>
    <h2>File Manager</h2>
    
    <!-- Obrazec za nalaganje -->
    <form action="?dir=<?php echo urlencode($directory); ?>" method="post" enctype="multipart/form-data">
        <input type="file" name="file" required>
        <button type="submit">Naloži</button>
    </form>
    
    <h3>Mapa: <?php echo htmlspecialchars($directory); ?></h3>
    <ul>
        <?php if ($directory !== '/'): ?>
            <li><a href="?dir=<?php echo urlencode(dirname($directory)); ?>">🔙 Nazaj</a></li>
        <?php endif; ?>
        <?php foreach ($files as $file): ?>
            <?php if ($file !== '.' && $file !== '..'): ?>
                <?php $file_path = $directory . DIRECTORY_SEPARATOR . $file; ?>
                <li>
                    <?php if (is_dir($file_path)): ?>
                        <a href="?dir=<?php echo urlencode($file_path); ?>">📁 <?php echo htmlspecialchars($file); ?></a>
                    <?php else: ?>
                        <a href="<?php echo htmlspecialchars($file_path); ?>" download>📄 <?php echo htmlspecialchars($file); ?></a>
                        <a href="?dir=<?php echo urlencode($directory); ?>&delete=<?php echo urlencode($file); ?>" onclick="return confirm('Želite izbrisati?');">🗑️</a>
                    <?php endif; ?>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
</body>
</html>