$start = Get-Date
$response = Invoke-WebRequest -Uri "http://localhost:8000/login" -UseBasicParsing -TimeoutSec 30
$end = Get-Date
$duration = $end - $start
Write-Output "Duration: $($duration.TotalMilliseconds) ms"
