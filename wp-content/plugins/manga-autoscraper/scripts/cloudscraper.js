const cloudscraper = require('cloudscraper');

// Get URL from command line arguments
const url = process.argv[2];

if (!url) {
    console.error('Please provide a URL');
    process.exit(1);
}

// Configure cloudscraper
const options = {
    url: url,
    headers: {
        'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
        'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
        'Accept-Language': 'en-US,en;q=0.5',
        'Connection': 'keep-alive',
        'Upgrade-Insecure-Requests': '1',
        'Cache-Control': 'max-age=0'
    },
    cloudflareTimeout: 30000,
    cloudflareMaxTimeout: 30000,
    followAllRedirects: true,
    resolveWithFullResponse: true
};

// Make request
cloudscraper(options)
    .then(response => {
        // Return response with cookies
        console.log(JSON.stringify({
            content: response.body,
            cookies: response.request.headers.cookie
        }));
        process.exit(0);
    })
    .catch(error => {
        console.error(error.message);
        process.exit(1);
    }); 