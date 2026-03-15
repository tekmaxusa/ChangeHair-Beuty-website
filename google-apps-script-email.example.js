/**
 * Google Apps Script — Book Appointment form → email
 *
 * SETUP:
 * 1. Go to https://script.google.com → New project
 * 2. Paste this entire code
 * 3. Change RECIPIENT_EMAIL below to your salon email
 * 4. Save (Ctrl+S), then Deploy → New deployment → Web app
 *    - Execute as: Me
 *    - Who has access: Anyone
 * 5. Copy the Web app URL — the site already uses your URL, so no need to change .env
 */

var RECIPIENT_EMAIL = 'your-salon@example.com'; // ← Replace with your salon email

// When the URL is opened in a browser (GET), prevents "Script function not found" error
function doGet() {
  return ContentService.createTextOutput('Form endpoint — use the Book Appointment form on the website.')
    .setMimeType(ContentService.MimeType.TEXT);
}

function doPost(e) {
  try {
    var params = parseParams(e);
    var name = params.name || '—';
    var email = params.email || '—';
    var phone = params.phone || '—';
    var service = params.service || '—';
    var date = params.date || '—';
    var time = params.time || '—';

    var subject = '📅 New Appointment Request: ' + name + ' — ' + service;

    var htmlBody = buildEmailHtml(name, email, phone, service, date, time);
    var plainBody = 'Change Hair & Beauty — Appointment Request\n\nName: ' + name + '\nEmail: ' + email + '\nPhone: ' + phone + '\nService: ' + service + '\nDate: ' + date + '\nTime: ' + time;

    MailApp.sendEmail({
      to: RECIPIENT_EMAIL,
      subject: subject,
      body: plainBody,
      htmlBody: htmlBody
    });

    return ContentService.createTextOutput(JSON.stringify({ success: true }))
      .setMimeType(ContentService.MimeType.JSON);
  } catch (err) {
    return ContentService.createTextOutput(JSON.stringify({ success: false, error: err.toString() }))
      .setMimeType(ContentService.MimeType.JSON);
  }
}

function buildEmailHtml(name, email, phone, service, date, time) {
  var salonName = 'Change Hair &amp; Beauty';
  var gold = '#c5a059';
  var dark = '#1a1a1a';
  var gray = '#6b7280';
  var lightBg = '#f8f7f5';

  return [
    '<!DOCTYPE html>',
    '<html>',
    '<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Appointment Request</title></head>',
    '<body style="margin: 0; padding: 32px 20px; background: #e8e6e2; font-family: \'Segoe UI\', Tahoma, Geneva, Verdana, sans-serif;">',
    '<div style="max-width: 560px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08);">',

    '<!-- Header -->',
    '<div style="background: ' + dark + '; padding: 28px 32px; text-align: center;">',
    '<div style="height: 3px; background: linear-gradient(90deg, transparent, ' + gold + ', transparent); margin-bottom: 20px;"></div>',
    '<h1 style="margin: 0; font-size: 24px; font-weight: 600; color: #fff; letter-spacing: 0.08em;">' + salonName + '</h1>',
    '<p style="margin: 10px 0 0; font-size: 11px; color: rgba(255,255,255,0.75); text-transform: uppercase; letter-spacing: 0.2em;">New Appointment Request</p>',
    '</div>',

    '<!-- Intro -->',
    '<div style="padding: 28px 32px 20px;">',
    '<p style="margin: 0; font-size: 15px; color: #374151; line-height: 1.5;">You have a new appointment request from your website. Details below.</p>',
    '</div>',

    '<!-- Details card -->',
    '<div style="margin: 0 32px 24px; background: ' + lightBg + '; border-radius: 10px; border: 1px solid #e5e2dd; overflow: hidden;">',
    '<table style="border-collapse: collapse; width: 100%; font-size: 14px;">',
    '<tr><td style="padding: 16px 20px; color: ' + gray + '; font-size: 11px; text-transform: uppercase; letter-spacing: 0.08em; width: 120px;">Guest</td><td style="padding: 16px 20px; color: ' + dark + '; font-weight: 600;">' + escapeHtml(name) + '</td></tr>',
    '<tr style="background: #fff;"><td style="padding: 14px 20px; color: ' + gray + '; font-size: 11px; text-transform: uppercase; letter-spacing: 0.08em;">Email</td><td style="padding: 14px 20px;"><a href="mailto:' + escapeHtml(email) + '" style="color: ' + gold + '; text-decoration: none; font-weight: 500;">' + escapeHtml(email) + '</a></td></tr>',
    '<tr><td style="padding: 14px 20px; color: ' + gray + '; font-size: 11px; text-transform: uppercase; letter-spacing: 0.08em;">Phone</td><td style="padding: 14px 20px;"><a href="tel:' + escapeHtml(phone) + '" style="color: ' + gold + '; text-decoration: none; font-weight: 500;">' + escapeHtml(phone) + '</a></td></tr>',
    '<tr style="background: #fff;"><td style="padding: 14px 20px; color: ' + gray + '; font-size: 11px; text-transform: uppercase; letter-spacing: 0.08em;">Service</td><td style="padding: 14px 20px; color: ' + dark + '; font-weight: 600;">' + escapeHtml(service) + '</td></tr>',
    '<tr><td style="padding: 14px 20px; color: ' + gray + '; font-size: 11px; text-transform: uppercase; letter-spacing: 0.08em;">Preferred date</td><td style="padding: 14px 20px; color: ' + dark + ';">' + escapeHtml(date) + '</td></tr>',
    '<tr style="background: #fff;"><td style="padding: 14px 20px; color: ' + gray + '; font-size: 11px; text-transform: uppercase; letter-spacing: 0.08em;">Preferred time</td><td style="padding: 14px 20px; color: ' + dark + ';">' + escapeHtml(time) + '</td></tr>',
    '</table>',
    '</div>',

    '<!-- CTA -->',
    '<div style="padding: 0 32px 28px;">',
    '<p style="margin: 0; font-size: 13px; color: #6b7280;">Reply to this email or call the guest to confirm the appointment.</p>',
    '</div>',

    '<!-- Footer -->',
    '<div style="padding: 18px 32px; background: ' + dark + '; font-size: 11px; color: rgba(255,255,255,0.7); text-align: center;">' + salonName + ' &middot; The Vista &middot; Lewisville, TX &middot; (214) 488-1122</div>',
    '</div>',
    '</body>',
    '</html>'
  ].join('\n');
}

function escapeHtml(text) {
  if (!text) return '—';
  return String(text)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;');
}

// Get form data from POST body (URL-encoded) or from e.parameter
function parseParams(e) {
  if (e.parameter && Object.keys(e.parameter).length > 0) {
    return e.parameter;
  }
  if (e.postData && e.postData.contents) {
    var body = e.postData.contents;
    var params = {};
    body.split('&').forEach(function (pair) {
      var parts = pair.split('=');
      if (parts.length === 2) {
        params[decodeURIComponent(parts[0].replace(/\+/g, ' '))] = decodeURIComponent(parts[1].replace(/\+/g, ' '));
      }
    });
    return params;
  }
  return {};
}
