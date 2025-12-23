# README: Deploying Multi-Instance PHP API with PostgreSQL on Elest.io

This project runs **5 independent instances** of the same PHP application (e.g., an API that sends emails), each with:
- Its own subdomain (e.g., app1.yourdomain.com → app5.yourdomain.com)
- Isolated PostgreSQL database
- Separate email configuration
- Shared codebase (single `index.php` that adapts via environment variables)

Everything is defined in a single `docker-compose.yml` file and runs on one VM.

Elest.io fully supports deploying custom multi-container Docker Compose stacks via their **CI/CD Pipelines** feature using the "Custom docker-compose" template.

## Prerequisites

1. An account on [https://elest.io](https://elest.io) (sign up if needed).
2. A Git repository (public or private on GitHub/GitLab) containing your project files:
   - `docker-compose.yml` (from previous message)
   - `app/public/index.php` (shared Hello World page)
   - `nginx/sites/app1.conf` → `app5.conf` (Nginx virtual hosts)
3. Your own domain name (e.g., yourdomain.com) with DNS control to point subdomains to the Elest.io VM IP.
4. Strong unique passwords for each database (update in `docker-compose.yml`).

## Step-by-Step Deployment on Elest.io

1. **Log in to your Elest.io dashboard** → https://dash.elest.io

2. In the left sidebar, click **CI/CD** → **Create a new pipeline**.

3. Choose **Docker compose** as the deployment source.

4. Select **Custom docker-compose template**.

5. Connect your Git provider (GitHub or GitLab) if not already done, and select your repository and branch containing the project files.

6. Choose your infrastructure:
   - **New infrastructure** (recommended): Select provider (e.g., Hetzner, DigitalOcean), region, and instance size.
     - Recommendation: At least 4 vCPU, 8 GB RAM, 160 GB storage to comfortably run Nginx + 5 PHP-FPM + 5 PostgreSQL containers.
   - Or select an existing VM if you prefer.

7. In the configuration step:
   - Paste your full `docker-compose.yml` into the Docker Compose editor (or it may auto-detect from repo).
   - **Exposed ports**: Add only HTTP port 80 (Nginx listens on 80).
     - Protocol: HTTP
     - Host port: 80
     - Container port: 80
     - Interface: leave default
   - **Reverse proxy / Inbound routes**:
     - You will configure domains later (see below).
   - No build commands needed (we use official images).

8. Click **Create CI/CD pipeline**.

   Elest.io will provision the VM, pull your repo, and start all containers via `docker compose up -d`. This takes a few minutes.

9. Once deployed, go to your pipeline dashboard:
   - Find the public IP address of the VM.
   - Note the default subdomain (something like `yourpipeline-xxxx.elestio.app`).

## Configuring Custom Subdomains

Elest.io automatically handles Let's Encrypt SSL for custom domains.

1. In your pipeline dashboard → **Domains** tab → **Add domain**.

2. Add each subdomain one by one:
   - `app1.yourdomain.com`
   - `app2.yourdomain.com`
   - ...
   - `app5.yourdomain.com`

3. For each domain:
   - Elest.io will provide DNS records (usually CNAME pointing to your pipeline's default subdomain or A record to the IP).
   - Add these records in your domain registrar's DNS settings.

4. Wait for DNS propagation and SSL issuance (usually fast on Elest.io).

5. Elest.io's reverse proxy will automatically route traffic based on the `server_name` in your Nginx configs (which point to the correct `php1` → `php5` containers).

## Verification

After deployment and DNS setup:

- Visit `app1.yourdomain.com` → Should show "Hello from Customer One!" with successful DB connection to its own database.
- Similarly for app2 → app5, each showing their own name, email config, and isolated DB.

## Updates & Maintenance

- Push changes to your Git repo → Elest.io can auto-redeploy on commit (configure in pipeline settings).
- Or manually update config/code via the dashboard's VS Code editor (Tools tab).
- Elest.io handles OS updates, monitoring, backups, firewall, etc.

## Notes & Limitations

- All 5 instances run on **one VM**. Choose a sufficiently powerful plan to avoid resource contention.
- The official `php:8.3-fpm` image lacks the PostgreSQL PDO driver by default. If you encounter connection issues, create a custom Dockerfile to install `pdo_pgsql` and update the php services to `build: ./php-dockerfile` (or ask Elest.io support for help).
- For production: Consider adding a mail container (e.g., Postfix) or using external SMTP.
- If you need more isolation or scaling, deploy separate pipelines (but that costs more).


Enjoy your multi-tenant PHP setup! 🚀

If you need help customizing further (e.g., adding PHPMailer for real emails), let me know.
