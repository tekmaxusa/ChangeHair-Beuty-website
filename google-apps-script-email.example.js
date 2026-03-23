/**
 * Google Apps Script — Change Hair & Beauty (contact + new booking)
 *
 * Matches the PHP email style (merchant = dark "Operations" shell, client = warm ivory shell).
 * Your server POSTs: event, name, email, phone, service, date, time, details (bookings only).
 *
 * SETUP
 * 1. https://script.google.com → New project → paste this file (rename to Code.gs or keep as one file).
 * 2. Set MERCHANT_EMAIL (and optional SALON_SUBLINE / SALON_ADDRESS / SALON_PHONE for client footer).
 * 3. SEND_CLIENT_ON_NEW_BOOKING: false if PHP already emails the client (recommended to avoid duplicates).
 * 4. Deploy → New deployment → Web app → Execute as: Me, Who has access: Anyone.
 * 5. Put the Web app URL in CHB_GOOGLE_SCRIPT_URL (or VITE_GOOGLE_SCRIPT_URL).
 */

var MERCHANT_EMAIL = 'gloriacloudco@gmail.com';

/** Shown under "A moment for you" on client emails (e.g. "The Vista"). */
var SALON_SUBLINE = 'The Vista · Lewisville, TX';

/** Footer strip on client emails (full address line). */
var SALON_ADDRESS = '2405 S Stemmons Fwy Ste 1126, Lewisville, TX 75067';

var SALON_PHONE = '+1 214-488-1122';

/**
 * If true, also emails the client on new_booking (HTML confirmation).
 * Set false when PHP mail() already sends client confirmation — avoids duplicate mail.
 */
var SEND_CLIENT_ON_NEW_BOOKING = true;

var BRAND = {
  gold: '#c7a66a',
  goldDark: '#8b6a2c',
  goldLight: '#dfbf83',
  ink: '#1d1a20',
  inkSoft: '#17161b',
  stone600: '#5d5750',
  stone500: '#837b71',
  stone400: '#b0a89b',
  merchantBg: '#141219',
  merchantOuter: '#dddde3',
  clientCream: '#fffdf9',
  clientOuter: '#eee8de',
  cardBorder: '#ebe5db',
  merchantCardBorder: '#d7d6df',
  grayCard: '#f7f7fb',
  white: '#ffffff',
};

function doGet() {
  return ContentService.createTextOutput('Change Hair & Beauty — POST webhook for contact & bookings.')
    .setMimeType(ContentService.MimeType.TEXT);
}

function doPost(e) {
  try {
    var p = parseParams(e);
    var event = String(p.event || '').toLowerCase();

    if (event === 'contact') {
      sendContactEmails(p);
    } else if (event === 'new_booking') {
      sendNewBookingEmails(p);
    } else {
      sendLegacyBookingStyle(p);
    }

    return ContentService.createTextOutput('ok').setMimeType(ContentService.MimeType.TEXT);
  } catch (err) {
    return ContentService.createTextOutput('error: ' + err).setMimeType(ContentService.MimeType.TEXT);
  }
}

function sendContactEmails(p) {
  var name = p.name || '—';
  var email = p.email || '—';
  var phone = p.phone || '—';
  var message = p.time || '';

  var subject = 'Website contact: ' + truncate(name.replace(/\s+/g, ' '), 80);
  var plain =
    'New message from the website contact form.\r\n\r\n' +
    'Name: ' +
    name +
    '\r\nEmail: ' +
    email +
    '\r\nPhone: ' +
    phone +
    '\r\n\r\nMessage:\r\n' +
    message +
    '\r\n';

  var inner =
    badgeHtml('Inquiry', 'merchant') +
    headlineMerchant('New website message') +
    '<p style="margin:0 0 32px 0;font-size:15px;line-height:1.65;color:#52525b;font-family:\'Segoe UI\',Tahoma,Geneva,Verdana,sans-serif;">' +
    'A visitor used the contact form. Use <strong style="color:#18181b;">Reply</strong> to answer from your inbox.</p>' +
    sectionRuleHtml('Contact details') +
    detailTableMerchant([
      ['Name', name],
      ['Email', email],
      ['Phone', phone],
    ]) +
    '<p style="margin:32px 0 14px 0;font-size:10px;font-weight:700;letter-spacing:0.2em;text-transform:uppercase;color:#71717a;font-family:\'Segoe UI\',Tahoma,Geneva,Verdana,sans-serif;">Message</p>' +
    quoteBlockHtml(nl2br(escapeHtml(message)));

  MailApp.sendEmail({
    to: MERCHANT_EMAIL,
    subject: subject,
    body: plain,
    htmlBody: wrapMerchant(subject, inner),
    replyTo: looksLikeEmail(email) ? email : undefined,
  });
}

function sendNewBookingEmails(p) {
  var name = p.name || '—';
  var email = p.email || '—';
  var phone = p.phone || '—';
  var service = p.service || '—';
  var date = p.date || '—';
  var time = p.time || '—';
  var details = p.details || '';

  var subject = 'New booking — ' + date;
  var plainMerchant =
    'New confirmed booking from the website.\r\n\r\n' +
    details +
    '\r\n\r\nClient: ' +
    name +
    '\r\nEmail: ' +
    email +
    '\r\nPhone: ' +
    phone +
    '\r\nServices: ' +
    service +
    '\r\n';

  var innerMerchant =
    badgeHtml('Confirmed booking', 'merchant') +
    headlineMerchant('Calendar update') +
    '<p style="margin:0 0 28px 0;font-size:15px;line-height:1.65;color:#52525b;font-family:\'Segoe UI\',Tahoma,Geneva,Verdana,sans-serif;">' +
    'The client booked online. This slot is stored as <strong style="color:#166534;">confirmed</strong>.</p>' +
    featuredDateTimeMerchant(date, time) +
    sectionRuleHtml('Client & service') +
    detailTableMerchant([
      ['Client', name],
      ['Email', email],
      ['Phone', phone],
      ['Services', service],
    ]) +
    '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:28px;"><tr><td style="padding:18px 20px;background-color:#f4f4f5;border-radius:12px;border:1px solid #d4d4d8;">' +
    '<p style="margin:0;font-size:13px;line-height:1.6;color:#3f3f46;font-family:\'Segoe UI\',Tahoma,Geneva,Verdana,sans-serif;">' +
    '<strong style="color:#18181b;">Quick action:</strong> Reply to reach the client.</p></td></tr></table>';

  if (details) {
    innerMerchant +=
      '<p style="margin:28px 0 12px 0;font-size:10px;font-weight:700;letter-spacing:0.2em;text-transform:uppercase;color:#71717a;">Full summary</p>' +
      quoteBlockHtml(nl2br(escapeHtml(details)));
  }

  MailApp.sendEmail({
    to: MERCHANT_EMAIL,
    subject: subject,
    body: plainMerchant,
    htmlBody: wrapMerchant('New confirmed booking.', innerMerchant),
    replyTo: looksLikeEmail(email) ? email : undefined,
  });

  if (SEND_CLIENT_ON_NEW_BOOKING && looksLikeEmail(email)) {
    var plainClient =
      'Hi ' +
      name +
      ',\r\n\r\nYour appointment is confirmed.\r\n\r\nDate: ' +
      date +
      '\r\nTime: ' +
      time +
      '\r\nService: ' +
      service +
      '\r\n\r\nWe look forward to seeing you.\r\n';

    var innerClient =
      badgeHtml('Confirmed', 'success') +
      headlineClient('Dear ' + escapeHtml(name) + ',') +
      '<p style="margin:0 0 28px 0;font-size:16px;line-height:1.65;color:#44403c;font-family:\'Segoe UI\',Tahoma,Geneva,Verdana,sans-serif;">' +
      'Thank you for choosing us. Your appointment is reserved.</p>' +
      featuredDateTimeClient(date, time) +
      sectionRuleHtml('Service') +
      detailTableClient([['Service', service]]) +
      '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:28px;"><tr><td style="padding:22px 24px;background:linear-gradient(145deg,#f2fbf5 0%,#e7f7ee 100%);border:1px solid #b7e6cb;border-radius:14px;">' +
      '<p style="margin:0;font-size:14px;line-height:1.65;color:#166534;font-family:\'Segoe UI\',Tahoma,Geneva,Verdana,sans-serif;">' +
      '<strong>Plans changed?</strong> Reply to this email or call us.</p></td></tr></table>' +
      visitStripHtml();

    MailApp.sendEmail({
      to: email,
      subject: 'You are booked — Change Hair & Beauty',
      body: plainClient,
      htmlBody: wrapClient('Your appointment is confirmed.', innerClient),
      replyTo: MERCHANT_EMAIL,
    });
  }
}

/** Legacy POST without event= (same shape as old form). */
function sendLegacyBookingStyle(p) {
  var name = p.name || '—';
  var email = p.email || '—';
  var phone = p.phone || '—';
  var service = p.service || '—';
  var date = p.date || '—';
  var time = p.time || '—';
  p.event = 'new_booking';
  p.details = 'Service: ' + service + '\nDate: ' + date + '\nTime: ' + time;
  sendNewBookingEmails(p);
}

// --- HTML builders (aligned with PHP contact_mail.php) ---

function wrapMerchant(preheader, innerHtml) {
  var year = new Date().getFullYear();
  var ph = escapeHtml(preheader);
  return (
    '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>' +
    '<body style="margin:0;padding:0;background-color:' +
    BRAND.merchantOuter +
    ';">' +
    '<span style="display:none!important;visibility:hidden;max-height:0;overflow:hidden;">' +
    ph +
    '</span>' +
    '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:' +
    BRAND.merchantOuter +
    ';padding:44px 18px;">' +
    '<tr><td align="center">' +
    '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:620px;">' +
    '<tr><td style="padding:0;background-color:' +
    BRAND.merchantBg +
    ';border-radius:20px 20px 0 0;">' +
    '<table width="100%" cellpadding="0" cellspacing="0"><tr>' +
    '<td style="height:4px;background-color:' +
    BRAND.gold +
    ';font-size:0;line-height:0;border-radius:20px 20px 0 0;">&nbsp;</td></tr>' +
    '<tr><td style="padding:32px 40px 28px 40px;background-color:#ffffff;">' +
    '<p style="margin:0 0 6px 0;font-size:10px;font-weight:700;letter-spacing:0.35em;text-transform:uppercase;color:#a8a29e;font-family:\'Segoe UI\',Tahoma,sans-serif;">Operations</p>' +
    '<p style="margin:0;font-size:28px;line-height:1.15;color:#1d1a20;font-family:Georgia,serif;">Change Hair <span style="color:' +
    BRAND.goldLight +
    ';">&amp;</span> Beauty</p>' +
    '<p style="margin:12px 0 0 0;font-size:13px;color:#78716c;font-family:\'Segoe UI\',Tahoma,sans-serif;">Merchant dashboard · internal notification</p>' +
    '</td></tr></table></td></tr>' +
    '<tr><td style="padding:0;background-color:' +
    BRAND.white +
    ';border:1px solid ' +
    BRAND.merchantCardBorder +
    ';border-top:none;border-radius:0 0 20px 20px;box-shadow:0 28px 48px -18px rgba(20,18,25,0.22),0 8px 22px -12px rgba(20,18,25,0.16);">' +
    '<table width="100%" cellpadding="0" cellspacing="0">' +
    '<tr><td style="padding:40px 40px 44px 40px;">' +
    innerHtml +
    '</td></tr>' +
    '<tr><td style="padding:28px 40px 36px 40px;background-color:#fafafa;border-top:1px solid ' +
    BRAND.merchantCardBorder +
    ';border-radius:0 0 20px 20px;">' +
    '<p style="margin:0;font-size:12px;line-height:1.65;color:#a8a29e;font-family:\'Segoe UI\',Tahoma,sans-serif;">Operational message from your booking system.</p>' +
    '<p style="margin:10px 0 0 0;font-size:11px;color:#737373;">&copy; ' +
    year +
    ' Change Hair & Beauty · Merchant</p>' +
    '</td></tr></table></td></tr></table></td></tr></table></body></html>'
  );
}

function wrapClient(preheader, innerHtml) {
  var year = new Date().getFullYear();
  var ph = escapeHtml(preheader);
  var sub = escapeHtml(SALON_SUBLINE);
  return (
    '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>' +
    '<body style="margin:0;padding:0;background-color:' +
    BRAND.clientOuter +
    ';">' +
    '<span style="display:none!important;visibility:hidden;max-height:0;overflow:hidden;">' +
    ph +
    '</span>' +
    '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:' +
    BRAND.clientOuter +
    ';padding:44px 18px;">' +
    '<tr><td align="center">' +
    '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:620px;">' +
    '<tr><td style="padding:0;background-color:' +
    BRAND.clientCream +
    ';border-radius:20px 20px 0 0;border:1px solid #eadfc9;border-bottom:none;">' +
    '<table width="100%" cellpadding="0" cellspacing="0"><tr>' +
    '<td style="height:5px;background-color:' +
    BRAND.gold +
    ';font-size:0;line-height:0;border-radius:20px 20px 0 0;">&nbsp;</td></tr>' +
    '<tr><td style="padding:36px 40px 32px 40px;background-color:#ffffff;">' +
    '<p style="margin:0 0 8px 0;font-size:11px;font-weight:700;letter-spacing:0.35em;text-transform:uppercase;color:#b45309;font-family:\'Segoe UI\',Tahoma,sans-serif;">Change Hair & Beauty</p>' +
    '<p style="margin:0;font-size:30px;line-height:1.12;color:#1c1917;font-family:Georgia,serif;">A moment for you</p>' +
    '<p style="margin:14px 0 0 0;font-size:14px;line-height:1.5;color:#57534e;font-family:\'Segoe UI\',Tahoma,sans-serif;">' +
    sub +
    '</p>' +
    '</td></tr></table></td></tr>' +
    '<tr><td style="padding:0;background-color:' +
    BRAND.white +
    ';border:1px solid ' +
    BRAND.cardBorder +
    ';border-top:none;border-radius:0 0 20px 20px;box-shadow:0 24px 44px -18px rgba(58,45,28,0.16),0 8px 18px -12px rgba(58,45,28,0.1);">' +
    '<table width="100%" cellpadding="0" cellspacing="0">' +
    '<tr><td style="padding:40px 40px 44px 40px;">' +
    innerHtml +
    '</td></tr>' +
    '<tr><td style="padding:28px 40px 36px 40px;background-color:#fdfbf7;border-top:1px solid ' +
    BRAND.cardBorder +
    ';border-radius:0 0 20px 20px;">' +
    '<p style="margin:0;font-size:12px;line-height:1.65;color:#78716c;">You received this from our website.</p>' +
    '<p style="margin:10px 0 0 0;font-size:11px;color:#a8a29e;">&copy; ' +
    year +
    ' Change Hair & Beauty</p>' +
    '</td></tr></table></td></tr></table></td></tr></table></body></html>'
  );
}

function badgeHtml(label, tone) {
  var styles = {
    merchant: 'background-color:#422006;color:#fde68a;border:1px solid #ca8a04;',
    success: 'background-color:#ecfdf5;color:#047857;border:1px solid #6ee7b7;',
  };
  var css = styles[tone] || styles.merchant;
  return (
    '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:22px;"><tr><td>' +
    '<span style="display:inline-block;padding:7px 16px;border-radius:999px;font-size:10px;font-weight:700;letter-spacing:0.16em;text-transform:uppercase;font-family:\'Segoe UI\',Tahoma,sans-serif;' +
    css +
    '">' +
    escapeHtml(label) +
    '</span></td></tr></table>'
  );
}

function headlineMerchant(text) {
  return (
    '<p style="margin:0 0 10px 0;font-size:28px;line-height:1.2;color:#18181b;font-family:Georgia,serif;">' +
    escapeHtml(text) +
    '</p>'
  );
}

function headlineClient(text) {
  return (
    '<p style="margin:0 0 8px 0;font-size:28px;line-height:1.2;color:#1c1917;font-family:Georgia,serif;">' +
    text +
    '</p>'
  );
}

function sectionRuleHtml(label) {
  return (
    '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 18px 0;">' +
    '<tr><td style="padding:0 0 10px 0;border-bottom:1px solid #e7e5e4;">' +
    '<p style="margin:0;font-size:10px;font-weight:700;letter-spacing:0.22em;text-transform:uppercase;color:#a8a29e;font-family:\'Segoe UI\',Tahoma,sans-serif;">' +
    escapeHtml(label) +
    '</p></td></tr></table>'
  );
}

function featuredDateTimeMerchant(dateStr, timeStr) {
  return (
    '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 28px 0;">' +
    '<tr><td style="padding:0 0 0 4px;background-color:' +
    BRAND.gold +
    ';border-radius:14px 0 0 14px;width:4px;font-size:0;">&nbsp;</td>' +
    '<td style="padding:26px 28px;background:linear-gradient(145deg,#18161c 0%,#221d27 75%);border-radius:0 14px 14px 0;">' +
    '<p style="margin:0;font-size:10px;font-weight:700;letter-spacing:0.28em;text-transform:uppercase;color:#a8a29e;">Appointment time</p>' +
    '<p style="margin:10px 0 4px 0;font-size:22px;line-height:1.25;color:#fafaf9;font-family:Georgia,serif;">' +
    escapeHtml(dateStr) +
    '</p>' +
    '<p style="margin:0;font-size:20px;color:#d4a853;font-family:Georgia,serif;">' +
    escapeHtml(timeStr) +
    '</p></td></tr></table>'
  );
}

function featuredDateTimeClient(dateStr, timeStr) {
  return (
    '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 28px 0;">' +
    '<tr><td style="padding:28px 30px;background:linear-gradient(145deg,#fffdf9 0%,#f9f2e6 100%);border:1px solid #e8dcc3;border-radius:16px;">' +
    '<p style="margin:0;font-size:10px;font-weight:700;letter-spacing:0.28em;text-transform:uppercase;color:#a18b5c;">Your reservation</p>' +
    '<p style="margin:12px 0 6px 0;font-size:26px;line-height:1.2;color:#1c1917;font-family:Georgia,serif;">' +
    escapeHtml(dateStr) +
    '</p>' +
    '<p style="margin:0;font-size:22px;color:#9a7b35;font-family:Georgia,serif;">' +
    escapeHtml(timeStr) +
    '</p></td></tr></table>'
  );
}

function detailTableMerchant(rows) {
  var b = BRAND;
  var html =
    '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;background-color:' +
    b.grayCard +
    ';border:1px solid ' +
    b.merchantCardBorder +
    ';border-radius:16px;">';
  for (var i = 0; i < rows.length; i++) {
    var rowBg = i % 2 === 0 ? '#f7f7fb' : '#ffffff';
    html +=
      '<tr style=\"background-color:' + rowBg + ';\"><td style=\"padding:16px 18px;border-bottom:1px solid ' +
      b.merchantCardBorder +
      ';width:32%;vertical-align:top;">' +
      '<p style="margin:0;font-size:10px;font-weight:700;letter-spacing:0.18em;text-transform:uppercase;color:#52525b;font-family:\'Segoe UI\',Tahoma,sans-serif;">' +
      escapeHtml(rows[i][0]) +
      '</p></td>' +
      '<td style="padding:16px 18px;border-bottom:1px solid ' +
      b.merchantCardBorder +
      ';vertical-align:top;">' +
      '<p style="margin:0;font-size:16px;line-height:1.55;color:#18181b;font-family:Georgia,serif;">' +
      escapeHtml(rows[i][1]) +
      '</p></td></tr>';
  }
  html += '</table>';
  return html;
}

function detailTableClient(rows) {
  var b = BRAND;
  var html =
    '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;background-color:#fafaf9;border:1px solid ' +
    b.cardBorder +
    ';border-radius:16px;">';
  for (var j = 0; j < rows.length; j++) {
    var rowBg2 = j % 2 === 0 ? '#fcfaf6' : '#ffffff';
    html +=
      '<tr style=\"background-color:' + rowBg2 + ';\"><td style=\"padding:16px 18px;border-bottom:1px solid ' +
      b.cardBorder +
      ';width:32%;vertical-align:top;">' +
      '<p style="margin:0;font-size:10px;font-weight:700;letter-spacing:0.18em;text-transform:uppercase;color:#78716c;">' +
      escapeHtml(rows[j][0]) +
      '</p></td>' +
      '<td style="padding:16px 18px;border-bottom:1px solid ' +
      b.cardBorder +
      ';vertical-align:top;">' +
      '<p style="margin:0;font-size:16px;line-height:1.55;color:#1c1917;font-family:Georgia,serif;">' +
      escapeHtml(rows[j][1]) +
      '</p></td></tr>';
  }
  html += '</table>';
  return html;
}

function quoteBlockHtml(innerEscaped) {
  return (
    '<table role="presentation" width="100%" cellpadding="0" cellspacing="0"><tr>' +
    '<td style="padding:22px 24px;background-color:#fafaf9;border-left:4px solid ' +
    BRAND.gold +
    ';border-radius:0 14px 14px 0;border:1px solid #e7e5e4;border-left-width:4px;">' +
    '<p style="margin:0;font-size:15px;line-height:1.7;color:#3f3f46;font-family:Georgia,serif;">' +
    innerEscaped +
    '</p></td></tr></table>'
  );
}

function visitStripHtml() {
  return (
    '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:32px;"><tr>' +
    '<td style="padding:22px 24px;background:linear-gradient(145deg,#17151a 0%,#232027 100%);border-radius:14px;">' +
    '<p style="margin:0 0 6px 0;font-size:10px;font-weight:700;letter-spacing:0.22em;text-transform:uppercase;color:#a8a29e;">Visit us</p>' +
    '<p style="margin:0;font-size:15px;line-height:1.55;color:#fafaf9;font-family:Georgia,serif;">' +
    escapeHtml(SALON_ADDRESS) +
    '</p>' +
    '<p style="margin:10px 0 0 0;font-size:14px;color:#d4a853;font-family:\'Segoe UI\',Tahoma,sans-serif;">' +
    escapeHtml(SALON_PHONE) +
    '</p></td></tr></table>'
  );
}

function escapeHtml(text) {
  if (text === null || text === undefined) return '—';
  return String(text)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;');
}

function nl2br(s) {
  return String(s).replace(/\r\n/g, '\n').replace(/\r/g, '\n').replace(/\n/g, '<br>');
}

function looksLikeEmail(s) {
  return String(s).indexOf('@') > 0 && String(s).length < 320;
}

function truncate(s, n) {
  s = String(s);
  return s.length <= n ? s : s.substring(0, n - 1) + '…';
}

function parseParams(e) {
  if (e.parameter && Object.keys(e.parameter).length > 0) {
    return e.parameter;
  }
  if (e.postData && e.postData.contents) {
    var body = e.postData.contents;
    var params = {};
    body.split('&').forEach(function (pair) {
      var parts = pair.split('=');
      if (parts.length >= 2) {
        var key = decodeURIComponent(parts[0].replace(/\+/g, ' '));
        var val = decodeURIComponent(parts.slice(1).join('=').replace(/\+/g, ' '));
        params[key] = val;
      }
    });
    return params;
  }
  return {};
}
