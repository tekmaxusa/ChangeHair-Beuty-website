# Tawk Apollo / AI Assist — Knowledge for Change Hair & Beauty

**Apollo** (Tawk’s AI) has **no access to source code**. It must **read text** from the **Knowledge Base**, **Shortcuts**, **website scrape**, or **uploaded files** in the [Tawk dashboard](https://dashboard.tawk.to).  
**Purpose of this document:** one broad **“master article”** that, once imported and connected to AI Assist, lets Apollo answer most questions about the website — **if you follow the setup below**.

### Best practice so it can “answer everything”

1. Paste the full **“Knowledge article — copy paste”** into one or more articles in the **Knowledge Base**.  
2. Under **AI Assist → Data sources**, enable the **Knowledge Base** (and **Plain text** copy if available).  
3. Add the **live website URL** as a data source (crawl) **alongside** the Knowledge Base — so text from the pages themselves is included.  
4. **Shortcuts** at the end — add many triggers (synonyms) for common questions.  
5. **Test** in Tawk preview; if answers are wrong, expand the article or shortcuts.

Tawk help: [Train Apollo / AI Assist](https://help.tawk.to/article/how-to-train-ai-assists-apollo-ai-to-respond-to-your-chats) · [Knowledge Base](https://help.tawk.to/article/setting-up-your-knowledge-base) · [Data sources](https://help.tawk.to/article/understanding-ai-assist%E2%80%99s-data-sources)

---

## Knowledge article — copy paste (English)

*Replace `https://YOUR-DOMAIN.com` with your real domain. Example: `https://changehairbeauty.com`.*

---

### Change Hair & Beauty — Complete website knowledge base (AI and support)

Use this document to answer visitor questions about the **Change Hair & Beauty** website and salon. Prefer short, clear answers. If something is not described here (medical advice, exact stylist availability, same-day walk-ins), tell the visitor to **call +1 214-488-1122** or wait for a human agent on chat.

---

#### Business summary

**Change Hair & Beauty** is a hair salon in **Lewisville, Texas**, focused on **Cut, Color, Perm, and Style**. The site explains services and prices, shows a photo gallery linked to Instagram, displays client testimonials, and offers **online booking** after the client creates a **free account** and logs in to the **client dashboard**.

---

#### Homepage — sections and what visitors find there

Visitors scroll the **single main marketing page** (`/` or `https://YOUR-DOMAIN.com/`). Main sections:

1. **Hero (top)** — rotating headlines about precision cuts, color, perms, Japanese magic straight, and styling. There is a **Book appointment** call-to-action that sends users to log in or sign up before booking.  
2. **Our Story** (`#story`) — short story about the salon, **K-beauty**-style techniques, **20+ years experience**, **5k+ happy clients** (as shown on the site).  
3. **Signature Services** — four blocks: **Cut**, **Color**, **Perm**, **Style**, each with description, sample service list with prices, images, and **Book appointment** buttons that route through the same booking flow (account required).  
4. **Our Services (menu)** (`#services`) — full **price list** by category (same numbers as below).  
5. **Visual Portfolio** (`#gallery`) — grid of salon photos; hovering shows **Instagram**; images link to Instagram posts. Below the grid: **Follow on Instagram** and **Follow on Facebook**.  
6. **What Our Clients Say** — testimonials carousel (client reviews).  
7. **Book appointment** (`#booking`) — explains booking with a free account; buttons **Book online** (→ login or dashboard) and **Contact us** (→ `#contact`).  
8. **Contact Us** (`#contact`) — **address**, **hours**, **phone**, **Live chat** link, embedded map.  
9. **Footer** — logo, **Chat**, **Instagram**, **Facebook**, copyright.

**In-page anchors:** `#story`, `#services`, `#gallery`, `#booking`, `#contact` — usable as `https://YOUR-DOMAIN.com/#contact`.

---

#### Address, phone, hours (official)

- **Location name:** The Vista  
- **Address:** 2405 S Stemmons Fwy Ste 1126, Lewisville, TX 75067  
- **Phone:** +1 214-488-1122 (tap-to-call link on the site)  
- **Hours:**  
  - Saturday: 10 AM – 7 PM  
  - Sunday: 1 – 6 PM  
  - Monday: **Closed**  
  - Tuesday: 10 AM – 7 PM  
  - Wednesday: 10 AM – 7 PM  
  - Thursday: 10 AM – 7 PM  
  - Friday: 10 AM – 7 PM  

**Live chat** uses **Tawk.to** (widget on the site and “Chat” / “Live chat” links). When a human agent is online in Tawk, they can take over from the AI.

---

#### Social media

- **Instagram:** https://www.instagram.com/changehairbeauty/  
- **Facebook:** https://www.facebook.com/changehairbeauty/  

Linked from the portfolio area and footer.

---

#### Services and prices (Our Services menu on the website)

Prices show **“+”** (starting from). Final price may vary by hair length and service.

**CUT**  
- Women — $35+  
- Men — $25+  
- Kids — $25+  

**COLOR**  
- Root — $80+  
- Manicure — $80+  
- Highlight (F) — $200+  
- Highlight (M) — $150+  

**PERM**  
- Men’s Iron Perm — $130+  
- Basic Women’s Perm — $100+  
- Set / Digital — $200+  
- Magic Setting — $250+  
- Japanese Magic Straight — $230+  

**STYLE**  
- Shampoo — $20+  
- Blow Dry — $35+  
- Upstyle — $130+  
- Makeup — $150+  

**Booking note:** In the **client dashboard**, booking uses **checkboxes** under categories **Cut, Color, Perm, Style** with options aligned to this menu (e.g. Women/Men/Kids under Cut). Visitors can select **more than one** service in one appointment (e.g. cut + color).

---

#### Accounts: sign up, log in, Google

**Sign up (create account)**  
- Path: **`/signup`** → `https://YOUR-DOMAIN.com/signup`  
- Or click **SIGN UP** in the header.  
- Enter **Name**, **Email**, **Password** (minimum **8 characters**). Submit **Sign up**.  
- After success, the site asks them to **log in**.

**Log in**  
- Path: **`/login`**  
- Or **LOG IN** in the header.  
- Email + password, button **Continue**.  
- Optional: **Continue with Google** — only works if the salon has configured **Google OAuth** on the server. If the button looks disabled or shows a configuration message, use **email and password** instead.

**After login**  
- Users are typically sent to **`/dashboard`** (client dashboard), especially when they came from **Book** / **Book online** / **Client dashboard** links.

**Logged-in header**  
- Navigation can show **CLIENT DASHBOARD** (or similar) pointing to `/dashboard`.

---

#### Online booking (client dashboard)

**URL:** `https://YOUR-DOMAIN.com/dashboard` — **must be logged in.**

**Book appointment block**  
1. **Step 1 — Service:** Check **one or more** services (multiple categories allowed, e.g. Cut + Color).  
2. **Step 2 — Date & time:** Choose **date**, then **time**.  
3. **Confirm booking.**

**Rules (as implemented on the site)**  
- **30-minute** time slots, generally **9:00 a.m.–5:00 p.m.** (only **available** slots appear).  
- Each **date + time** can be booked **only once** (one client per slot).  
- Dates with **no free slots** or **fully booked** days may **not appear** in the date list.  
- The system looks ahead about **90 days** for open dates (marketing text: next 90 days).  
- For **today**, times in the **past** are not bookable.  
- If the page says there are **no open dates**, ask the visitor to **call the salon** or try again later.

**After booking**  
- A confirmation message appears; **Your appointments** table on the same page lists **service summary**, **date**, **time**, and **booked at** timestamp.  
- **Cancel or reschedule online:** not described on the public site — tell visitors to **phone the salon** for changes unless the site later adds that feature.

---

#### Password / account problems

- There is **no self-service “Forgot password”** link described in the public UI. If someone forgot their password, they should **call +1 214-488-1122** or use **live chat** for staff help.  
- **Wrong email at sign-up:** they may need to sign up again with the correct email or contact the salon.

---

#### Website tech / general FAQs

- **Mobile:** The site is responsive; booking works on phones.  
- **Language:** The site UI is primarily **English**.  
- **Booking without account:** Not supported — they need a **free account** first.  
- **Walk-ins / phone booking:** Always possible in real life; the **website** only describes **online** booking through the dashboard. Recommend **calling** for same-day or urgent requests.  
- **Payments:** Not processed on the website in this stack; pricing is shown as a guide (**$xx+**).

---

#### When to escalate to humans

- Allergies, pregnancy, scalp conditions, or **chemical service safety** — recommend **professional consultation in salon** or by phone.  
- **Disputes, refunds, no-show policy** — salon staff only.  
- **Stylist by name** (e.g. Young) — testimonials mention stylists; for **guaranteed assignment** or schedule, **call the salon**.

---

## Suggested Shortcuts (add triggers in Tawk)

Create one shortcut per row; in the “trigger” field, add **every variant** Tawk allows (use a separate shortcut if the UI only accepts one trigger).

| Topic | Example visitor phrases | Core answer (paste into Tawk; expand if you want) |
|-------|-------------------------|-----------------------------------------------------|
| Sign up | create account, register, sign up, new account | Click **SIGN UP** or go to `/signup`. Enter name, email, password (8+ characters), then sign up and **log in**. |
| Log in | login, log in, sign in, how to access dashboard | **LOG IN** or `/login` — email and password, or **Continue with Google** if enabled. |
| Book | book, booking, appointment, schedule, reserve | Log in → `/dashboard` → pick **one or more services** → **date** and **time** → **Confirm booking**. 30-minute slots; one booking per slot. |
| Prices | how much, cost, price, menu | Use the **Our Services** prices on the site: CUT (Women $35+, Men $25+, Kids $25+), COLOR, PERM, STYLE as in the knowledge article. |
| Hours | open, hours, closed monday, sunday | Full weekly hours in **Contact**; **Monday closed**; Sat 10–7, Sun 1–6, Tue–Fri 10–7. |
| Address / directions | where, location, address, maps | **The Vista**, 2405 S Stemmons Fwy Ste 1126, Lewisville, TX 75067 — map on **`#contact`**. |
| Phone | call, number, phone | **+1 214-488-1122** |
| Instagram / Facebook | ig, instagram, facebook, social | Links in **Visual Portfolio** and footer; Instagram **https://www.instagram.com/changehairbeauty/** |
| Gallery / photos | photos, portfolio, pictures | Section **`#gallery`** — images link to Instagram posts. |
| Reviews | reviews, testimonials, clients say | **What Our Clients Say** on the homepage (testimonial carousel). |
| Services | what do you offer, perm, color, highlights | **Cut, Color, Perm, Style** — descriptions in **Signature Services** and full menu under **`#services`**. |
| Google button | google login, continue with google | **Continue with Google** works when OAuth is configured; if disabled, use **email and password**. |
| No dates / error booking | no slots, fully booked, cannot book | Day may be full or outside the bookable window; **call the salon** or try another date. |
| Cancel / change | reschedule, cancel appointment | **No self-service cancel/reschedule** on the site — **call +1 214-488-1122**. |
| Forgot password | forgot password, reset password | **No forgot-password page** on the site — **call the salon** or use live chat for staff help. |
| Live chat | talk to human, agent | **Tawk** chat widget; a **human agent** replies when online in Tawk. |
| Book online button | book online button | **`#booking`** and hero — sends you to **log in** or **dashboard** depending on session. |
| Multiple services | cut and color same appointment | Yes — in **dashboard** booking, use **checkboxes** to select **multiple services** in one appointment. |
| K-beauty / story | korean, k-beauty, experience | **Our Story** section mentions K-beauty techniques and years of experience (as on the site). |

---

## Note (limitations)

No AI is **100% correct** on every question. When the website gains new features, **update** the Knowledge Base article and Shortcuts. This document matches the `api` PHP app (`config/salon_data.php` and booking flow) — if live copy differs, sync the text here and in Tawk.
