<?php
class ImageUpload {
    private static $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    private static $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    public static function upload($file, $directory = null) {
        $directory = $directory ?: UPLOAD_PATH;
        if (!is_dir($directory)) mkdir($directory, 0755, true);

        if ($file['error'] !== UPLOAD_ERR_OK) return ['success' => false, 'error' => 'Upload error.'];
        if ($file['size'] > UPLOAD_MAX_SIZE) return ['success' => false, 'error' => 'File too large (max 5MB).'];

        $mime = mime_content_type($file['tmp_name']);
        if (!in_array($mime, self::$allowedMimes)) return ['success' => false, 'error' => 'Invalid image type.'];

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, self::$allowedExts)) return ['success' => false, 'error' => 'Invalid file extension.'];

        $filename = uniqid('prod_', true) . '.' . $ext;
        $destination = rtrim($directory, '/') . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return ['success' => false, 'error' => 'Failed to move uploaded file.'];
        }

        self::resize($destination, $destination, 800, 800);
        return ['success' => true, 'filename' => $filename];
    }

    public static function delete($filename, $directory = null) {
        $directory = $directory ?: UPLOAD_PATH;
        $path = rtrim($directory, '/') . '/' . $filename;
        if ($filename && file_exists($path)) unlink($path);
    }

    public static function resize($source, $destination, $maxWidth = 800, $maxHeight = 800) {
        $info = getimagesize($source);
        if (!$info) return false;
        [$origW, $origH] = $info;
        if ($origW <= $maxWidth && $origH <= $maxHeight) return true;

        $ratio = min($maxWidth / $origW, $maxHeight / $origH);
        $newW = (int)($origW * $ratio);
        $newH = (int)($origH * $ratio);

        $createFns = [
            'image/jpeg' => 'imagecreatefromjpeg',
            'image/png'  => 'imagecreatefrompng',
            'image/gif'  => 'imagecreatefromgif',
            'image/webp' => 'imagecreatefromwebp',
        ];
        $fn = isset($createFns[$info['mime']]) ? $createFns[$info['mime']] : null;
        $srcImg = $fn ? $fn($source) : null;
        if (!$srcImg) return false;

        $dstImg = imagecreatetruecolor($newW, $newH);
        if ($info['mime'] === 'image/png' || $info['mime'] === 'image/webp') {
            imagealphablending($dstImg, false);
            imagesavealpha($dstImg, true);
        }
        imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $newW, $newH, $origW, $origH);

        $saveFns = [
            'image/jpeg' => function($img, $dest) { return imagejpeg($img, $dest, 85); },
            'image/png'  => function($img, $dest) { return imagepng($img, $dest, 8); },
            'image/gif'  => function($img, $dest) { return imagegif($img, $dest); },
            'image/webp' => function($img, $dest) { return imagewebp($img, $dest, 85); },
        ];
        if (isset($saveFns[$info['mime']])) {
            $saveFns[$info['mime']]($dstImg, $destination);
        }
        imagedestroy($srcImg);
        imagedestroy($dstImg);
        return true;
    }
}
