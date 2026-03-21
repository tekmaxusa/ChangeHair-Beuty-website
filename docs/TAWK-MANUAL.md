# Tawk.to Manual — Change Hair & Beauty (PHP site)

## 1. What is Tawk.to?

Tawk.to is a free live chat service. It adds a chat widget so visitors can message you; you reply from the Tawk dashboard or mobile app.

---

## 2. How It’s Wired on This Stack

### 2.1 Single source of truth

All IDs and the direct chat URL live in **`change-hair-beauty/config/salon_data.php`**:

| Key | Purpose |
|-----|---------|
| `tawk_property_id` | Property ID (embed + dashboard) |
| `tawk_widget_id` | Widget ID (embed script) |
| `tawk_chat` | Direct link if the widget isn’t ready (new tab) |

The embed URL is built as:  
`https://embed.tawk.to/{tawk_property_id}/{tawk_widget_id}`

### 2.2 Widget script (every page that loads chat)

- **File:** `change-hair-beauty/public/partials/tawk.php`
- **Included from:** `public/index.php`, `public/login.php`, `public/signup.php`, `public/dashboard/index.php` (before `</body>`).
- **Behavior:**
  - Loads the Tawk embed from `salon_data.php`.
  - **Logged-in users:** after the widget loads, sets visitor **name** and **email** via `Tawk_API.setAttributes` (from the PHP session) so agents see who is chatting.
  - **Tags** (for filtering in Tawk): always `change-hair-beauty`, plus context:
    - `marketing-site` — home / marketing pages
    - `auth-login` — login page
    - `auth-signup` — signup page
    - `client-dashboard`, `online-booking` — `/dashboard/` (booking)
    - `logged-in` — whenever a session user is detected

Optional extra tags: set `$GLOBALS['chbTawkExtraTags'] = ['custom-tag'];` **before** `require` of `tawk.php` on a specific page.

### 2.3 “Live chat” and footer “Chat” links

- **Where:** `public/partials/section-contact-footer.php`
- **Classes:** `chb-tawk-open` on those links (same `href` as `tawk_chat`).
- **JS:** `public/js/site.js` — if `Tawk_API.maximize` is available, click **opens the widget** instead of leaving the site; if the script hasn’t loaded yet, the normal link opens the Tawk tab.

---

## 3. Logging In and Replying (salon staff)

### 3.1 Log in

1. Go to **https://dashboard.tawk.to**
2. Sign in (e.g. Continue with Google or email/password)
3. Open the property that matches **`tawk_property_id`** in `salon_data.php`

### 3.2 Go online

Set status to **Online** / **Available** so visitors get timely replies.

### 3.3 Reply

Use **Monitoring / Inbox / Chat** in the dashboard or the Tawk mobile app.

---

## 4. Dashboard Overview (Tawk)

- **Monitoring / Inbox:** Live and past chats (use **tags** to spot dashboard vs marketing visitors).
- **History:** Past conversations.
- **Reporting:** Volume, response times, etc.
- **Administration → Agents:** Invite teammates.
- **Administration → Channels / Widget:** Appearance, welcome text, position.

---

## 5. Common Tasks

### 5.1 Change widget text or colors

Tawk dashboard → **Administration** → widget / channel settings. No code change.

### 5.2 Use a different Tawk property

1. In Tawk, copy the new embed snippet; note **property** and **widget** IDs in the URL  
   `https://embed.tawk.to/PROPERTY_ID/WIDGET_ID`
2. Update **`salon_data.php`:** `tawk_property_id`, `tawk_widget_id`, and `tawk_chat`  
   (`https://tawk.to/chat/PROPERTY_ID/`).

### 5.3 Visitor name/email security (optional)

Tawk supports a **hash** with `setAttributes` so visitors can’t spoof email. This project does **not** set `hash` by default. To enable later, add server-side HMAC per [Tawk’s docs](https://developer.tawk.to/js-api/) and extend `tawk.php`.

### 5.4 Apollo / AI (“dapat masagot ang tanong tungkol sa website”)

Ang **Apollo** ay trained sa **Tawk dashboard**, hindi sa `tawk.php`.  
Para saklawin ang **lahat ng karaniwang tanong** tungkol sa site: (1) i-paste ang **master article** sa Knowledge Base, (2) **i-enable ang Knowledge Base** (at kung puwede **website URL crawl**) sa **AI Assist → Data sources**, (3) magdagdag ng **Shortcuts** para sa madalas na phrasing.

- **Master article + shortcut table:** [`docs/TAWK-APOLLO-KNOWLEDGE.md`](TAWK-APOLLO-KNOWLEDGE.md) — may buong menu, oras, address, lahat ng section ng homepage, booking rules, at FAQs.  
- **Opisyal na gabay:** [Train Apollo / AI Assist](https://help.tawk.to/article/how-to-train-ai-assists-apollo-ai-to-respond-to-your-chats), [Knowledge Base](https://help.tawk.to/article/setting-up-your-knowledge-base), [Data sources](https://help.tawk.to/article/understanding-ai-assist%E2%80%99s-data-sources)

---

## 6. Troubleshooting

| Issue | What to check |
|-------|----------------|
| No bubble | Hard refresh. Confirm `tawk.php` is required on that page. Console for script errors. |
| Wrong property | `salon_data.php` IDs vs Tawk embed snippet. |
| Links always open new tab | `site.js` must load (`defer` on login/signup/home/dashboard). |
| Agent doesn’t see name/email | User must be **logged in**; session must have `user_email` / `user_name`. |
| Apollo / AI walang alam sa login, signup, booking | I-import ang teksto mula sa **`docs/TAWK-APOLLO-KNOWLEDGE.md`** sa Tawk Knowledge Base at i-enable bilang AI data source; tingnan §5.4. |
| Third-party cookie warnings | Often harmless; chat can still work. |

---

## 7. Quick reference

| Item | Value / location |
|------|------------------|
| Dashboard | https://dashboard.tawk.to |
| Config | `change-hair-beauty/config/salon_data.php` (`tawk_*` keys) |
| Embed partial | `change-hair-beauty/public/partials/tawk.php` |
| Open-widget links | class `chb-tawk-open` + `site.js` |
| Direct chat URL | `tawk_chat` in `salon_data.php` |
| Apollo / AI answers | `docs/TAWK-APOLLO-KNOWLEDGE.md` → paste into Tawk Knowledge Base + enable as data source |
