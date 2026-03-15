<?php

function ensure_audit_table($con)
{
    mysqli_query($con, "CREATE TABLE IF NOT EXISTS audit_logs (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        actor_type VARCHAR(30) NOT NULL,
        actor_id VARCHAR(80) NOT NULL,
        action_name VARCHAR(120) NOT NULL,
        entity_type VARCHAR(60) NOT NULL,
        entity_id VARCHAR(80) NULL,
        details TEXT NULL,
        ip_address VARCHAR(80) NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_entity (entity_type, entity_id),
        INDEX idx_actor (actor_type, actor_id),
        INDEX idx_created (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}

function audit_log($con, $actorType, $actorId, $actionName, $entityType, $entityId = null, $details = '')
{
    ensure_audit_table($con);

    $aType = mysqli_real_escape_string($con, (string)$actorType);
    $aId = mysqli_real_escape_string($con, (string)$actorId);
    $action = mysqli_real_escape_string($con, (string)$actionName);
    $eType = mysqli_real_escape_string($con, (string)$entityType);
    $eId = mysqli_real_escape_string($con, (string)$entityId);
    $dets = mysqli_real_escape_string($con, (string)$details);
    $ip = mysqli_real_escape_string($con, $_SERVER['REMOTE_ADDR'] ?? 'unknown');

    mysqli_query($con, "INSERT INTO audit_logs(actor_type, actor_id, action_name, entity_type, entity_id, details, ip_address)
                       VALUES('$aType', '$aId', '$action', '$eType', '$eId', '$dets', '$ip')");
}
