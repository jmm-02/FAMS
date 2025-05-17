<?php
// Load the source image
$sourceImage = imagecreatefrompng('assets/logo.png');

// Define the sizes we need
$sizes = [72, 96, 128, 144, 152, 192, 384, 512];

// Create icons directory if it doesn't exist
if (!file_exists('assets/icons')) {
    mkdir('assets/icons', 0777, true);
}

// Generate each size
foreach ($sizes as $size) {
    // Create a new image with the desired size
    $newImage = imagecreatetruecolor($size, $size);
    
    // Preserve transparency
    imagealphablending($newImage, false);
    imagesavealpha($newImage, true);
    $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
    imagefilledrectangle($newImage, 0, 0, $size, $size, $transparent);
    
    // Resize the image
    imagecopyresampled(
        $newImage,
        $sourceImage,
        0, 0, 0, 0,
        $size, $size,
        imagesx($sourceImage),
        imagesy($sourceImage)
    );
    
    // Save the image
    $outputFile = "assets/icons/icon-{$size}x{$size}.png";
    imagepng($newImage, $outputFile);
    
    // Free up memory
    imagedestroy($newImage);
    
    echo "Generated {$outputFile}\n";
}

// Free up memory
imagedestroy($sourceImage);

echo "All icons have been generated successfully!\n";
?> 