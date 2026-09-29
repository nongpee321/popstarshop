<?php
$f='app/Http/Controllers/PosController.php';
$c=file_get_contents($f);
$c=str_replace("'sku_code',", "'sku_code',\n                'image_url',", $c);
file_put_contents($f, $c);
echo "PosController updated.\n";
