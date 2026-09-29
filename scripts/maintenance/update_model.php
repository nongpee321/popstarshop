<?php
$file = 'app/Models/Product.php';
$content = file_get_contents($file);
$content = str_replace("'legacy_sku',", "'legacy_sku',\n        'image_url',", $content);
file_put_contents($file, $content);
echo "Updated Product model.\n";
