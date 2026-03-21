<?php

declare(strict_types=1);

/**
 * Tawk.to widget: one config source (salon_data), visitor context when logged in,
 * page tags for login / signup / dashboard / marketing. See docs/TAWK-MANUAL.md
 */

$root = dirname(__DIR__, 2);
require_once $root . '/config/session.php';
session_bootstrap();

if (!isset($salon) || !is_array($salon) || ($salon['tawk_property_id'] ?? '') === '') {
    /** @var array<string, mixed> $salon */
    $salon = require $root . '/config/salon_data.php';
}

$prop = (string) ($salon['tawk_property_id'] ?? '');
$wid = (string) ($salon['tawk_widget_id'] ?? '');
if ($prop === '' || $wid === '') {
    return;
}

$embedSrc = 'https://embed.tawk.to/' . rawurlencode($prop) . '/' . rawurlencode($wid);

$chbTawkVisitor = null;
if (!empty($_SESSION['user_id'])) {
    $em = trim((string) ($_SESSION['user_email'] ?? ''));
    if ($em !== '' && filter_var($em, FILTER_VALIDATE_EMAIL)) {
        $chbTawkVisitor = [
            'name' => trim((string) ($_SESSION['user_name'] ?? '')),
            'email' => $em,
        ];
    }
}

$uri = (string) ($_SERVER['REQUEST_URI'] ?? '');
$script = basename((string) ($_SERVER['SCRIPT_NAME'] ?? ''));
$pageTags = ['change-hair-beauty'];

if (str_contains($uri, '/dashboard')) {
    $pageTags[] = 'client-dashboard';
    $pageTags[] = 'online-booking';
} elseif ($script === 'login.php') {
    $pageTags[] = 'auth-login';
} elseif ($script === 'signup.php') {
    $pageTags[] = 'auth-signup';
} else {
    $pageTags[] = 'marketing-site';
}

if ($chbTawkVisitor !== null) {
    $pageTags[] = 'logged-in';
}

$extra = $GLOBALS['chbTawkExtraTags'] ?? null;
if (is_array($extra)) {
    foreach ($extra as $t) {
        if (is_string($t) && $t !== '') {
            $pageTags[] = $t;
        }
    }
}

$pageTags = array_values(array_unique($pageTags));

$jsonFlags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE;
$visitorJs = $chbTawkVisitor !== null ? json_encode($chbTawkVisitor, $jsonFlags) : 'null';
$tagsJs = json_encode($pageTags, $jsonFlags);

?>
<!-- Tawk.to — Change Hair & Beauty (docs/TAWK-MANUAL.md) -->
<script type="text/javascript">
var Tawk_API = Tawk_API || {};
var Tawk_LoadStart = new Date();
var CHB_TAWK_VISITOR = <?= $visitorJs ?>;
var CHB_TAWK_TAGS = <?= $tagsJs ?>;

Tawk_API.onLoad = function () {
  try {
    if (CHB_TAWK_VISITOR && CHB_TAWK_VISITOR.email) {
      Tawk_API.setAttributes(
        {
          name: CHB_TAWK_VISITOR.name || '',
          email: CHB_TAWK_VISITOR.email
        },
        function () {}
      );
    }
    if (CHB_TAWK_TAGS && CHB_TAWK_TAGS.length && typeof Tawk_API.addTags === 'function') {
      Tawk_API.addTags(CHB_TAWK_TAGS, function () {});
    }
  } catch (e) {}
};

(function () {
  var s1 = document.createElement('script');
  var s0 = document.getElementsByTagName('script')[0];
  s1.async = true;
  s1.src = <?= json_encode($embedSrc, $jsonFlags) ?>;
  s1.charset = 'UTF-8';
  s1.setAttribute('crossorigin', '*');
  s0.parentNode.insertBefore(s1, s0);
})();
</script>
