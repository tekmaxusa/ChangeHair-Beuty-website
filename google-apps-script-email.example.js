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

var RECIPIENT_EMAIL = 'your-salon@example.com'; // ← PALITAN: ilagay ang email ng salon

// Kapag binuksan ang URL sa browser (GET), hindi na lalabas "Script function not found"
function doGet() {
  return ContentService.createTextOutput('Form endpoint — use the Book Appointment form on the website.')
    .setMimeType(ContentService.MimeType.TEXT);
}

function doPost(e) {
  try {
    var params = parseParams(e);
    var name = params.name || '';
    var email = params.email || '';
    var phone = params.phone || '';
    var service = params.service || '';
    var date = params.date || '';
    var time = params.time || '';

    var subject = 'Book Appointment: ' + name + ' - ' + service;
    var body = [
      'Name: ' + name,
      'Email: ' + email,
      'Phone: ' + phone,
      'Service: ' + service,
      'Date: ' + date,
      'Time: ' + time
    ].join('\n');

    MailApp.sendEmail(RECIPIENT_EMAIL, subject, body);

    return ContentService.createTextOutput(JSON.stringify({ success: true }))
      .setMimeType(ContentService.MimeType.JSON);
  } catch (err) {
    return ContentService.createTextOutput(JSON.stringify({ success: false, error: err.toString() }))
      .setMimeType(ContentService.MimeType.JSON);
  }
}

// Kunin ang form data mula sa POST body (URL-encoded) o sa e.parameter
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
