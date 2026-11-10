<?php

function logAudit($conn, ?int $userId, string $action): void {
    $ipAddress = $_SERVER["REMOTE_ADDR"] ?? null;
    $userAgent = substr($_SERVER["HTTP_USER_AGENT"] ?? "", 0, 255);

    $stmt = $conn->prepare(
        "INSERT INTO audit_logs
        (user_id, action, ip_address, user_agent)
        VALUES (?, ?, ?, ?)"
    );

    if (!$stmt) {
        return;
    }

    $stmt->bind_param("isss", $userId, $action, $ipAddress, $userAgent);
    $stmt->execute();
    $stmt->close();
}