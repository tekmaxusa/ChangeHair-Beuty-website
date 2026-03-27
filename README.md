<div align="center">
<img width="1200" height="475" alt="GHBanner" src="https://github.com/user-attachments/assets/0aa67016-6eaf-458a-adb2-6e31a0763ed6" />
</div>

# Run and deploy your AI Studio app

This contains everything you need to run your app locally..

**First-time production setup:** [docs/INITIAL-SETUP.md](docs/INITIAL-SETUP.md) (MySQL, cPanel, GitHub Actions, secrets, OAuth).

**Ongoing deployment reference:** [docs/DEPLOYMENT.md](docs/DEPLOYMENT.md). The SPA lives at the repo root; the PHP API is in **`api/`**.

View your app in AI Studio: https://ai.studio/apps/a9bc5ac8-eeed-46e3-ac1e-5b8f40d38839

## Run Locally

**Prerequisites:**  Node.js


1. Install dependencies: `npm install`
2. Copy [.env.example](.env.example) to `.env` and set `VITE_API_URL` (and optional `GEMINI_API_KEY` if you use that flow)
3. Run the app: `npm run dev`

## Publish with GitHub Pages

The site deploys to GitHub Pages when you push to **`main`** or **`payment`** (see `.github/workflows/deploy.yml`).

1. **Push your code** to a GitHub repository (create one at [github.com/new](https://github.com/new) if needed).
2. **Enable GitHub Pages** in the repo:
   - Go to **Settings → Pages**
   - Under **Build and deployment**, set **Source** to **GitHub Actions**
3. **Trigger a deploy**: push to `main` or `payment`, or run **Actions → Deploy to GitHub Pages → Run workflow**.
4. After the workflow finishes, your site will be at:  
   `https://<your-username>.github.io/<repository-name>/`

## Deploy to Vercel (auto-deploy on every GitHub push)

Connect this repo to Vercel once; after that, **every push to `main` will deploy the site to Vercel automatically**.

### One-time setup: connect GitHub to Vercel

1. **Sign in to Vercel**  
   Go to [vercel.com](https://vercel.com) and sign in (use “Continue with GitHub” if you use GitHub).

2. **Import your repository**  
   - Click **Add New… → Project**.  
   - Select **Import Git Repository** and choose your GitHub account if prompted.  
   - Find and select **changehair-beauty** (or your repo: `https://github.com/tekmaxusa/changehair-beauty`).  
   - Click **Import**.

3. **Configure the project**  
   - **Framework Preset:** Vite (should be auto-detected).  
   - **Build Command:** `npm run build` (default).  
   - **Output Directory:** `dist` (default).  
   - **Root Directory:** leave blank.  
   - Click **Deploy**.

4. **Done**  
   After the first deploy, every push to the `main` branch will trigger a new deployment. Your site will be available at a URL like `https://your-project.vercel.app` (you can add a custom domain in Vercel’s project settings).
