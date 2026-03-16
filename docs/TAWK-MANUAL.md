# Tawk.to Manual — Change Hair & Beauty Website

## 1. What is Tawk.to?

Tawk.to is a free live chat service. It adds a chat widget to your website so visitors can message you, and you reply from the Tawk dashboard or mobile app.

---

## 2. How It's Set Up on This Site

### 2.1 Widget (floating chat bubble)

- **Where:** `index.html` (bottom of `<body>`).
- **What it does:** Loads the Tawk script so the chat bubble appears on every page (e.g. bottom-right).
- **Your IDs:**
  - **Property ID:** `69b675beb2bda41c36e81e18`
  - **Widget ID:** `1jjobnssh`
- **Embed URL:**  
  `https://embed.tawk.to/69b675beb2bda41c36e81e18/1jjobnssh`

### 2.2 "Live chat" links

- **Where:** `src/App.tsx`
  - Contact section: "Live chat" link with icon
  - Footer: chat icon
- **Link used:**  
  `https://tawk.to/chat/69b675beb2bda41c36e81e18/`
- **What it does:** Opens your Tawk chat in a new browser tab (same property as the widget).

---

## 3. Logging In and Replying to Chats

### 3.1 Log in

1. Go to **https://dashboard.tawk.to**
2. Sign in (e.g. "Continue with Google" or your email/password)
3. Select the property for **Change Hair & Beauty** (or "Royal Beauty Care" if that's the name in Tawk)

### 3.2 Go online

- In the dashboard, find your status (e.g. "Go online" or "Available").
- Click to set status to **Online** or **Available**.
- If you stay offline, visitors can still send messages, but you won't get live notifications and they may think no one is there.

### 3.3 Reply on the website (dashboard)

1. In the left sidebar, open **Monitoring** or **Inbox** (or **Chat**).
2. Click a conversation to open it.
3. Type in the reply box at the bottom and press **Send**.

### 3.4 Reply on your phone

1. Install **Tawk.to** from the App Store (iOS) or Google Play (Android).
2. Open the app and log in with the same account.
3. Turn on notifications so you see new messages.
4. Open a chat and reply from the app.

---

## 4. Dashboard Overview

- **Monitoring / Inbox:** See and reply to live and past chats.
- **History:** List of past conversations (visitor, time, agent).
- **Reporting:** Chats answered, missed, response time, etc.
- **Administration → Agents:** Add team members and set who can reply.
- **Administration → Channels (or Widget):** Get embed code, change widget look (e.g. position, color, "We are here" text).

---

## 5. Common Tasks

### 5.1 Change widget text (e.g. "We are here")

- Dashboard → **Administration** → **Channels** (or widget/appearance).
- Edit the widget / welcome message or label (name may be "Widget" or "Chat widget").
- Save. No code change needed.

### 5.2 Use a different Tawk property or widget

- In Tawk, create or select the property and get the **embed code**.
- In that code you'll see a URL like:  
  `https://embed.tawk.to/NEW_PROPERTY_ID/NEW_WIDGET_ID`
- In your project:
  - **index.html:** Replace the `s1.src='...'` URL with that full embed URL.
  - **src/App.tsx:** Change `TAWK_CHAT_URL` to:  
    `https://tawk.to/chat/NEW_PROPERTY_ID/`
- Redeploy the site.

### 5.3 Add another person to reply (agent)

- Dashboard → **Administration** → **Agents**.
- Invite by email; they accept and get agent access.
- Assign them to the right department if you use departments.

### 5.4 Turn on AI / automated replies (optional)

- In the dashboard, look for **"Access the limitless power of AI"** or **AI Assist** (in "What's the latest" or **Add-ons**).
- Follow the steps to enable AI so the bot can reply when you're offline or to suggest replies.

---

## 6. Troubleshooting

| Issue | What to check |
|-------|----------------|
| No chat bubble on site | Hard refresh (Ctrl+F5). Confirm the script in `index.html` is present and the embed URL is correct. Check browser console for script errors. |
| "Third-party cookie will be blocked" in console | Browser privacy message; usually doesn't stop the widget. Chat should still work. |
| I don't get new messages | Make sure you're **Online** in the dashboard; check **Monitoring/Inbox** and the Tawk app notifications. |
| Visitor says they didn't get a reply | Confirm you're an **Agent** (not only Viewer). Reply from **Monitoring/Inbox** or the app and send the message. |
| "Live chat" or footer link doesn't open chat | Confirm `TAWK_CHAT_URL` in `src/App.tsx` is `https://tawk.to/chat/69b675beb2bda41c36e81e18/` (same Property ID as the widget). |

---

## 7. Quick Reference

| Item | Value |
|------|--------|
| Dashboard | https://dashboard.tawk.to |
| Property ID | 69b675beb2bda41c36e81e18 |
| Widget ID | 1jjobnssh |
| Embed URL | https://embed.tawk.to/69b675beb2bda41c36e81e18/1jjobnssh |
| Direct chat URL | https://tawk.to/chat/69b675beb2bda41c36e81e18/ |
| Script location in project | `index.html` (before `</body>`) |
| Chat link constant in project | `TAWK_CHAT_URL` in `src/App.tsx` |
