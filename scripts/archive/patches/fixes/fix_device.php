<?php
$f = 'app/Http/Controllers/Api/PosApiController.php';
$c = file_get_contents($f);
$c = str_replace('$device?->user_id', 'null', $c);
file_put_contents($f, $c);
echo "Fixed device user constraint!\n";
