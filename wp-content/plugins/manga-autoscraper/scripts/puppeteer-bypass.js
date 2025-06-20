const puppeteer = require('puppeteer');

async function bypassCloudflare(url) {
    const browser = await puppeteer.launch({
        headless: true,
        args: [
            '--no-sandbox',
            '--disable-setuid-sandbox',
            '--disable-dev-shm-usage',
            '--disable-accelerated-2d-canvas',
            '--disable-gpu',
            '--window-size=1920x1080'
        ]
    });

    try {
        const page = await browser.newPage();

        // Set viewport
        await page.setViewport({
            width: 1920,
            height: 1080
        });

        // Set user agent
        await page.setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');

        // Set extra headers
        await page.setExtraHTTPHeaders({
            'Accept-Language': 'en-US,en;q=0.9',
            'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Connection': 'keep-alive',
            'Upgrade-Insecure-Requests': '1'
        });

        // Navigate to URL
        await page.goto(url, {
            waitUntil: 'networkidle0',
            timeout: 30000
        });

        // Wait for Cloudflare challenge to complete
        await page.waitForFunction(() => {
            return !document.querySelector('#challenge-running') && 
                   !document.querySelector('#challenge-error');
        }, { timeout: 30000 });

        // Get cookies
        const cookies = await page.cookies();
        const cookieString = cookies.map(cookie => `${cookie.name}=${cookie.value}`).join('; ');

        // Get page content
        const content = await page.content();

        // Close browser
        await browser.close();

        // Return content with cookies
        return JSON.stringify({
            content: content,
            cookies: cookieString
        });
    } catch (error) {
        if (browser) {
            await browser.close();
        }
        throw error;
    }
}

// Get URL from command line arguments
const url = process.argv[2];

if (!url) {
    console.error('Please provide a URL');
    process.exit(1);
}

// Run bypass
bypassCloudflare(url)
    .then(result => {
        console.log(result);
        process.exit(0);
    })
    .catch(error => {
        console.error(error.message);
        process.exit(1);
    }); 