<?php
/**
 * SoulScript - Universal Expiration & Lifecycle Helper (Single Source of Truth)
 * Standardizes 12+1=13 Month validity, 13th month grace period, and archival calculations.
 */

if (!defined('RENEWAL_FEE_INR')) {
    define('RENEWAL_FEE_INR', 299);
}

/**
 * Calculates page lifecycle status array
 *
 * @param array $page DB record containing expires_at and status
 * @return array Lifecycle state details
 */
function getPageLifecycleStatus($page) {
    $expiresAtRaw = $page['expires_at'] ?? null;
    $statusRaw = strtolower($page['status'] ?? 'live');

    // Default to +1 year from now if expires_at is empty
    $expiresTimestamp = !empty($expiresAtRaw) ? strtotime($expiresAtRaw) : strtotime('+1 year');
    $now = time();

    // 13th Month Grace Period End: 1 month after expires_at
    $graceEndTimestamp = strtotime('+1 month', $expiresTimestamp);

    $daysUntilExpires = (int)ceil(($expiresTimestamp - $now) / 86400);
    $daysUntilGraceEnd = (int)ceil(($graceEndTimestamp - $now) / 86400);

    // Compute State Flags
    $isArchived = ($statusRaw === 'archived') || ($now > $graceEndTimestamp);
    $isGracePeriod = !$isArchived && ($now > $expiresTimestamp) && ($now <= $graceEndTimestamp);
    $isLive = !$isArchived && !$isGracePeriod && ($statusRaw === 'live');

    $stateCode = 'active';
    if ($isArchived) {
        $stateCode = 'archived';
    } elseif ($isGracePeriod) {
        $stateCode = 'grace_period';
    }

    return [
        'state' => $stateCode,
        'is_live' => $isLive,
        'is_grace_period' => $isGracePeriod,
        'is_archived' => $isArchived,
        'expires_at' => date('Y-m-d H:i:s', $expiresTimestamp),
        'expires_at_formatted' => date('j M Y', $expiresTimestamp),
        'grace_end_formatted' => date('j M Y', $graceEndTimestamp),
        'days_until_expires' => $daysUntilExpires,
        'days_until_grace_end' => max(0, $daysUntilGraceEnd),
        'renewal_fee_inr' => RENEWAL_FEE_INR
    ];
}
