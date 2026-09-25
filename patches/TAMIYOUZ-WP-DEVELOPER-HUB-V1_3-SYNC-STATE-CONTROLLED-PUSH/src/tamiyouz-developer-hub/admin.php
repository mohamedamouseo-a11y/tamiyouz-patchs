<?php
if (!defined('ABSPATH') || !current_user_can('manage_options')) {
    exit;
}

$base_file = __DIR__ . '/base.php';
$review_file = __DIR__ . '/review.php';

if (!is_file($base_file) || !is_file($review_file)) {
    echo '<div class="wrap"><h1>Developer Hub</h1><div class="notice notice-error"><p>Developer Hub V1.1 module file is missing.</p></div></div>';
    return;
}

require $base_file;
require $review_file;
