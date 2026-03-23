<?php

declare(strict_types=1);

/** @var array $salon */

$igLabel = $chbSocialInstagramLabel ?? 'Follow on Instagram';
$fbLabel = $chbSocialFacebookLabel ?? 'Follow on Facebook';

?>
<a class="chb-social-branded-link" href="<?= h($salon['instagram']) ?>" target="_blank" rel="noopener noreferrer">
    <svg class="chb-social-branded-icon chb-social-branded-icon--ig" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
        <circle cx="12" cy="12" r="4"/>
        <circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/>
    </svg>
    <span><?= h($igLabel) ?></span>
</a>
<a class="chb-social-branded-link" href="<?= h($salon['facebook']) ?>" target="_blank" rel="noopener noreferrer">
    <svg class="chb-social-branded-icon chb-social-branded-icon--fb" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
        <path fill="currentColor" d="M24 12.073C24 5.446 18.627 0 12 0S0 5.446 0 12.073c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
    </svg>
    <span><?= h($fbLabel) ?></span>
</a>
<?php unset($chbSocialInstagramLabel, $chbSocialFacebookLabel, $igLabel, $fbLabel); ?>
