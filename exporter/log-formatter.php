<?php

declare(strict_types=1);

ob_implicit_flush(true);

while (($line = fgets(STDIN)) !== false) {
    $line = rtrim($line);
    if ($line === '') {
        continue;
    }

    // [Day Mon DD HH:MM:SS YYYY] ip:port [status]: METHOD /path
    if (preg_match('/^\[([^\]]+)\] (\S+?):\d+ \[(\d+)\]: (\w+) (\S+)/', $line, $m)) {
        echo json_encode([
            'time_local'  => $m[1],
            'remote_addr' => $m[2],
            'status'      => $m[3],
            'request'     => $m[4] . ' ' . $m[5],
        ], JSON_UNESCAPED_SLASHES) . "\n";
        continue;
    }

    // Skip connection-level noise: Accepted / Closing
    if (preg_match('/\b(?:Accepted|Closing)\b/', $line)) {
        continue;
    }

    // Fallback for unexpected lines (e.g. PHP errors)
    echo json_encode([
        'time_local' => date('D M j H:i:s Y'),
        'message'    => $line,
    ], JSON_UNESCAPED_SLASHES) . "\n";
}