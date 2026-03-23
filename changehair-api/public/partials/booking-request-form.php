<?php

declare(strict_types=1);

/**
 * Home-page booking request (POST to /).
 *
 * @var list<array<string,mixed>> $chb_book_categories
 * @var array<string, list<string>> $chb_book_slotsByDate
 * @var list<string> $chb_book_postServices
 * @var string $chb_book_postDate
 * @var string $chb_book_postTime
 * @var string $chb_book_slotsJson
 * @var string $chb_book_defaultDate
 */

$chb_book_form_action = $chb_book_form_action ?? '/book-appointment.php';

?>
<div class="chb-panel chb-dashboard-panel chb-book-panel chb-book-panel--home">
    <header class="chb-book-panel-head">
        <h3 class="chb-section-title">Request a time</h3>
        <p class="chb-book-intro">Select one or more services, then pick date and time. The salon will confirm or cancel by email. View status anytime in your <a href="/dashboard/">client dashboard</a>.</p>
    </header>

    <?php if ($chb_book_slotsByDate === []): ?>
        <p class="msg err">No open dates in the next 90 days. Please check back later or contact the salon.</p>
    <?php else: ?>
        <form method="post" action="<?= h($chb_book_form_action) ?>" class="chb-book-form" id="chb-book-form">
            <input type="hidden" name="action" value="book">

            <div class="chb-book-fieldset" role="group" aria-labelledby="chb-book-step-service">
                <h4 class="chb-book-legend" id="chb-book-step-service">1. Service</h4>
                <div class="chb-book-services">
                    <?php foreach ($chb_book_categories as $cat): ?>
                        <section class="chb-book-cat" aria-labelledby="cat-<?= h($cat['id']) ?>">
                            <h5 class="chb-book-cat-title" id="cat-<?= h($cat['id']) ?>"><?= h($cat['name']) ?></h5>
                            <ul class="chb-book-svc-list" role="list">
                                <?php foreach ($cat['services'] as $svc): ?>
                                    <?php
                                    $val = $cat['id'] . '|' . $svc['name'];
                                    $checked = in_array($val, $chb_book_postServices, true);
                                    ?>
                                    <li>
                                        <label class="chb-book-svc">
                                            <input
                                                type="checkbox"
                                                name="services[]"
                                                value="<?= h($val) ?>"
                                                class="chb-book-svc-input"
                                                <?= $checked ? 'checked' : '' ?>>
                                            <span class="chb-book-svc-card">
                                                <span class="chb-book-svc-name"><?= h($svc['name']) ?></span>
                                                <span class="chb-book-svc-price"><?= h($svc['price']) ?></span>
                                            </span>
                                        </label>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </section>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="chb-book-fieldset chb-book-fieldset-datetime" role="group" aria-labelledby="chb-book-step-datetime">
                <h4 class="chb-book-legend" id="chb-book-step-datetime">2. Date &amp; time</h4>
                <div class="chb-book-datetime-grid">
                    <div class="chb-book-field">
                        <label class="chb-book-label" for="booking_date">Date</label>
                        <select id="booking_date" name="booking_date" class="chb-book-select" required>
                            <?php foreach (array_keys($chb_book_slotsByDate) as $d): ?>
                                <option value="<?= h($d) ?>" <?= $d === $chb_book_defaultDate ? 'selected' : '' ?>><?= h($d) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="chb-book-field">
                        <label class="chb-book-label" for="booking_time">Time</label>
                        <select id="booking_time" name="booking_time" class="chb-book-select" required>
                            <option value="">Select time</option>
                        </select>
                    </div>
                </div>
                <p class="chb-book-slot-note">30-minute slots · 9:00 a.m.–5:00 p.m.</p>
            </div>

            <button type="submit" class="chb-submit chb-book-submit">Submit booking request</button>
        </form>

        <script type="application/json" id="chb-slots-by-date"><?= $chb_book_slotsJson ?></script>
        <script>
        (function () {
          var raw = document.getElementById('chb-slots-by-date');
          var dateSel = document.getElementById('booking_date');
          var timeSel = document.getElementById('booking_time');
          if (!raw || !dateSel || !timeSel) return;
          var byDate = JSON.parse(raw.textContent || '{}');
          var savedTime = <?= json_encode($chb_book_postTime, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

          function fillTimes() {
            var d = dateSel.value;
            var slots = byDate[d] || [];
            timeSel.innerHTML = '';
            if (!slots.length) {
              var o = document.createElement('option');
              o.value = '';
              o.textContent = 'No times available';
              o.selected = true;
              timeSel.appendChild(o);
              timeSel.disabled = true;
              return;
            }
            timeSel.disabled = false;
            var keep = savedTime && slots.indexOf(savedTime) !== -1;
            var ph = document.createElement('option');
            ph.value = '';
            ph.textContent = 'Select time';
            ph.setAttribute('disabled', 'disabled');
            ph.selected = !keep;
            timeSel.appendChild(ph);
            slots.forEach(function (t) {
              var opt = document.createElement('option');
              opt.value = t;
              opt.textContent = t;
              if (savedTime === t) opt.selected = true;
              timeSel.appendChild(opt);
            });
          }

          dateSel.addEventListener('change', function () { savedTime = ''; fillTimes(); });
          fillTimes();
        })();
        </script>
    <?php endif; ?>
</div>
