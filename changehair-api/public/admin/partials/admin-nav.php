<?php

declare(strict_types=1);

/** @var string $chbAdminNavActive */
$chbAdminNavActive = $chbAdminNavActive ?? 'dashboard';

?>
<nav class="chb-admin-nav chb-admin-nav--tabs" aria-label="Admin menu">
    <a class="chb-admin-nav__link<?= $chbAdminNavActive === 'dashboard' ? ' is-active' : '' ?>" href="/admin/">Dashboard</a>
    <span class="chb-admin-nav__sep" aria-hidden="true">·</span>
    <a class="chb-admin-nav__link<?= $chbAdminNavActive === 'bookings' ? ' is-active' : '' ?>" href="/admin/bookings.php">Bookings</a>
    <span class="chb-admin-nav__sep" aria-hidden="true">·</span>
    <a class="chb-admin-nav__link<?= $chbAdminNavActive === 'accounts' ? ' is-active' : '' ?>" href="/admin/users.php">Accounts</a>
</nav>
