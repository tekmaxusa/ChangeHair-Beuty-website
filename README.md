<div align="center">
<img width="1200" height="475" alt="GHBanner" src="https://github.com/user-attachments/assets/0aa67016-6eaf-458a-adb2-6e31a0763ed6" />
</div>

# Run and deploy your AI Studio app

This contains everything you need to run your app locally.

View your app in AI Studio: https://ai.studio/apps/a9bc5ac8-eeed-46e3-ac1e-5b8f40d38839

## Run Locally

**Prerequisites:**  Node.js


1. Install dependencies:
   `npm install`
2. Set the `GEMINI_API_KEY` in [.env.local](.env.local) to your Gemini API key
3. Run the app:
   `npm run dev`

## Publish with GitHub Pages

The site is set up to deploy automatically to GitHub Pages when you push to the `main` branch.

1. **Push your code** to a GitHub repository (create one at [github.com/new](https://github.com/new) if needed).
2. **Enable GitHub Pages** in the repo:
   - Go to **Settings → Pages**
   - Under **Build and deployment**, set **Source** to **GitHub Actions**
3. **Trigger a deploy**: push to `main` or run the workflow from **Actions → Deploy to GitHub Pages → Run workflow**.
4. After the workflow finishes, your site will be at:  
   `https://<your-username>.github.io/<repository-name>/`

The Tawk.to live chat widget is included on the site and will appear once the script loads.
