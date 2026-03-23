<?php

declare(strict_types=1);

?>
<div id="chb-booking-modal" class="chb-modal" aria-hidden="true" hidden>
    <div class="chb-modal__backdrop" data-chb-modal-close tabindex="-1"></div>
    <div class="chb-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="chb-booking-modal-title">
        <div class="chb-modal__head">
            <h2 id="chb-booking-modal-title" class="chb-modal__title">Book appointment</h2>
            <button type="button" class="chb-modal__close" data-chb-modal-close aria-label="Close">&times;</button>
        </div>
        <iframe class="chb-modal__iframe" title="Book appointment" data-chb-booking-iframe src="about:blank"></iframe>
    </div>
</div>
