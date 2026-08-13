# SoulScript — Agent Guidelines & Memory

## 🚨 Production Safety & Code Guidelines
1. **Production Code Only (No Local IP/Testing Background Servers)**:
   - Work strictly on production-ready code targeting `https://digitalyogi24.com`.
   - Do NOT launch local background servers (`127.0.0.1:8000`, local MySQL daemons), local IP test scripts, or local testing code.
   - Keep codebase completely clean of local test scripts, IP hardcoding, or transient dev daemons.

2. **Never Break Production Environment Fallbacks**:
   - Environment files like `config/config.env.php` are ignored by Git (`.gitignore`).
   - When editing core configuration files like `config/config.php` or `config/db.php`, **always ensure smart environment detection** is active so Hostinger production (`digitalyogi24.com`, DB: `u810420317_SoulScript`, User: `u810420317_soulscript`) works seamlessly even when `config.env.php` is absent on the server.
   - Never replace production fallback logic with local-only XAMPP defaults (`root@127.0.0.1`).

3. **Pre-Push Sanity Check**:
   - Verify that all environment-sensitive configuration changes preserve production availability before committing and pushing.

4. **CRITICAL: Absolute Protection of User Uploads, Partner Avatars & Media Assets from Git Wipes**:
   - `uploads/*` and `assets/default_gallery/*` MUST ALWAYS be ignored in `.gitignore` and `uploads/.gitignore`.
   - NEVER commit overrides that untrack or remove `uploads/*` ignore rules from `.gitignore`.
   - ALWAYS preserve dual-storage protection for BOTH user scrapbook photos (`media_{hash}.webp`) and partner profile avatars (`receiver_photo` / `avatar_{hash}.webp`): write files to persistent storage (`/home/u810420317/domains/digitalyogi24.com/uploads_persistent/`) and maintain PHP auto-healing in `includes/media_helper.php`.

5. **CRITICAL: The 3 Golden Architectural & Testing Rules**:
   - **Rule 1: Market Standard Benchmark First**: Always research and analyze top-tier market standards (Shopify, WordPress CMS, enterprise patterns) before building or altering any feature.
   - **Rule 2: Mandatory Chrome Browser Verification**: Never declare a task completed or ask the user to check live until I have run Chrome browser automated/manual testing myself, verified 100% of scenarios (Insert, Update, Delete, Toggle, Refresh), and ensured zero regression.
   - **Rule 3: Mandatory 360° View Impact Analysis**: Always evaluate the complete 360° lifecycle of a feature before coding — asking: *"If we Add, Update, Delete, or Toggle X, how does it affect existing features? Will anything break or regress?"* Anticipate all CRUD edge cases proactively.
