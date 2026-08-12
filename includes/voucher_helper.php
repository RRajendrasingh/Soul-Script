<?php
require_once __DIR__ . '/../config/db.php';

/**
 * Raksha Bandhan Amazon Voucher & Affiliate Engine
 * Safe, zero-leak, weighted probability & time-gated unlock helper
 */

// Target Unlock Timestamp: 28th August 2026, 12:00:00 PM IST
define('RAKHI_UNLOCK_TIMESTAMP', strtotime('2026-08-28 12:00:00'));

/**
 * Generate weighted random voucher amount for ₹449 plan
 * Probability Matrix:
 * 75.0% -> ₹100
 * 18.0% -> ₹150
 *  5.0% -> ₹250
 *  1.5% -> ₹500
 *  0.5% -> ₹2000 (Mega Bumper Winner!)
 */
function getRandomVoucherAmount() {
    $rand = mt_rand(1, 1000) / 10.0; // Random float 0.1 to 100.0
    if ($rand <= 75.0) {
        return 100;
    } elseif ($rand <= 93.0) {
        return 150;
    } elseif ($rand <= 98.0) {
        return 250;
    } elseif ($rand <= 99.5) {
        return 500;
    } else {
        return 2000;
    }
}

/**
 * Allocate Rakhi Voucher for a given Order and Page
 */
function allocateRakhiVoucher($orderId, $pageId = null) {
    if (empty($orderId)) return null;

    try {
        $db = getDB();

        // Check if already allocated
        $stmtChk = $db->prepare("SELECT * FROM rakhi_voucher_allocations WHERE order_id = ?");
        $stmtChk->execute([$orderId]);
        $existing = $stmtChk->fetch();

        if ($existing) {
            // Update page_id if it was NULL previously
            if (!empty($pageId) && empty($existing['page_id'])) {
                $updPage = $db->prepare("UPDATE rakhi_voucher_allocations SET page_id = ? WHERE order_id = ?");
                $updPage->execute([$pageId, $orderId]);
                $existing['page_id'] = $pageId;
            }
            return $existing;
        }

        // Calculate amount
        $amount = getRandomVoucherAmount();
        $assignedCode = null;

        // Try to claim available code from vault matching this amount
        $stmtVault = $db->prepare("SELECT * FROM rakhi_vouchers_vault WHERE status = 'available' AND amount = ? ORDER BY id ASC LIMIT 1");
        $stmtVault->execute([$amount]);
        $vaultItem = $stmtVault->fetch();

        if ($vaultItem) {
            $assignedCode = $vaultItem['voucher_code'];
            $updVault = $db->prepare("UPDATE rakhi_vouchers_vault SET status = 'assigned', assigned_order_id = ?, assigned_at = NOW() WHERE id = ?");
            $updVault->execute([$orderId, $vaultItem['id']]);
        }

        // Insert allocation
        $ins = $db->prepare("INSERT INTO rakhi_voucher_allocations (order_id, page_id, allocated_amount, voucher_code) VALUES (?, ?, ?, ?)");
        $ins->execute([$orderId, $pageId, $amount, $assignedCode]);

        return [
            'order_id' => $orderId,
            'page_id' => $pageId,
            'allocated_amount' => $amount,
            'voucher_code' => $assignedCode,
            'is_claimed' => 0
        ];
    } catch (Exception $e) {
        error_log("allocateRakhiVoucher Error: " . $e->getMessage());
        return null;
    }
}

/**
 * Get Secure Unlock State for Recipient Page (Server-Gated Verification)
 */
function getRakhiVoucherUnlockStatus($orderId, $pageId = null) {
    $now = time();
    $isUnlocked = ($now >= RAKHI_UNLOCK_TIMESTAMP);
    $secondsRemaining = max(0, RAKHI_UNLOCK_TIMESTAMP - $now);

    try {
        $db = getDB();
        $allocation = null;

        if (!empty($orderId)) {
            $stmt = $db->prepare("SELECT * FROM rakhi_voucher_allocations WHERE order_id = ?");
            $stmt->execute([$orderId]);
            $allocation = $stmt->fetch();
        } elseif (!empty($pageId)) {
            $stmt = $db->prepare("SELECT * FROM rakhi_voucher_allocations WHERE page_id = ?");
            $stmt->execute([$pageId]);
            $allocation = $stmt->fetch();
        }

        // If no allocation exists yet, create one lazily
        if (!$allocation && !empty($orderId)) {
            $allocation = allocateRakhiVoucher($orderId, $pageId);
        }

        if (!$isUnlocked) {
            // SAFE LOCKED RESPONSE: Raw code & amount are NEVER sent before unlock date!
            return [
                'unlocked' => false,
                'seconds_remaining' => $secondsRemaining,
                'unlock_date_formatted' => '28 August 2026, 12:00 PM IST',
                'allocated_amount' => null,
                'voucher_code' => null,
                'is_claimed' => 0
            ];
        }

        // UNLOCKED RESPONSE (28 Aug 12:00 PM onwards)
        return [
            'unlocked' => true,
            'seconds_remaining' => 0,
            'unlock_date_formatted' => '28 August 2026, 12:00 PM IST',
            'allocated_amount' => $allocation ? intval($allocation['allocated_amount']) : 100,
            'voucher_code' => $allocation ? $allocation['voucher_code'] : null,
            'is_claimed' => $allocation ? intval($allocation['is_claimed']) : 0
        ];
    } catch (Exception $e) {
        return [
            'unlocked' => false,
            'seconds_remaining' => $secondsRemaining,
            'unlock_date_formatted' => '28 August 2026, 12:00 PM IST',
            'allocated_amount' => null,
            'voucher_code' => null,
            'is_claimed' => 0
        ];
    }
}

/**
 * Get Curated Amazon Affiliate Products
 */
function getAffiliateProducts() {
    $defaultProducts = [
        [
            'id' => 1,
            'title' => 'Cadbury Celebrations Premium Rakhi Chocolate Gift Box',
            'category' => 'Chocolates 🍫',
            'price_text' => '₹349',
            'image_url' => 'https://images.unsplash.com/photo-1549007994-cb92caebd54b?auto=format&fit=crop&w=400&q=80',
            'affiliate_url' => 'https://www.amazon.in/dp/B0757FG9X6?tag=soulscript-21'
        ],
        [
            'id' => 2,
            'title' => 'Titan Karishma Analog Dial Women\'s Luxury Watch',
            'category' => 'Watches ⌚',
            'price_text' => '₹1,495',
            'image_url' => 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?auto=format&fit=crop&w=400&q=80',
            'affiliate_url' => 'https://www.amazon.in/dp/B00FLL4K90?tag=soulscript-21'
        ],
        [
            'id' => 3,
            'title' => 'The Body Shop British Rose Deluxe Beauty Gift Set',
            'category' => 'Skincare 💄',
            'price_text' => '₹1,895',
            'image_url' => 'https://images.unsplash.com/photo-1571781926291-c477ebfd024b?auto=format&fit=crop&w=400&q=80',
            'affiliate_url' => 'https://www.amazon.in/dp/B01HME57CS?tag=soulscript-21'
        ],
        [
            'id' => 4,
            'title' => 'Personalized Wooden Photo Frame Keepsake Desk Gift',
            'category' => 'Photo Keepsake 🖼️',
            'price_text' => '₹499',
            'image_url' => 'https://images.unsplash.com/photo-1513519245088-0e12902e5a38?auto=format&fit=crop&w=400&q=80',
            'affiliate_url' => 'https://www.amazon.in/dp/B08F7K9L2P?tag=soulscript-21'
        ]
    ];

    try {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM affiliate_products WHERE is_active = 1 ORDER BY sort_order ASC, id DESC");
        $dbProducts = $stmt->fetchAll();
        return !empty($dbProducts) ? $dbProducts : $defaultProducts;
    } catch (Exception $e) {
        return $defaultProducts;
    }
}
