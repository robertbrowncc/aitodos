// Safely get the browser API
const getBrowserAPI = () => {
    // Check if we're in a WebExtension context
    if (typeof browser !== 'undefined') {
        return browser;
    }
    // Check if we're in a Chrome extension context
    if (typeof chrome !== 'undefined' && chrome.runtime) {
        return chrome;
    }
    // If neither is available, return a dummy API that won't throw errors
    return {
        runtime: {
            getURL: (path) => path
        }
    };
};

const injectContentOnce = (content, id) => {
    if (document.getElementById(id)) {
        return; // Already injected
    }
    injectContent(content, id);
};

const injectContent = (content, id) => {
    try {
        const browserAPI = getBrowserAPI();
        
        // Create style element
        const style = document.createElement('style');
        if (id) {
            style.id = id;
        }
        
        // Handle both string content and URLs
        if (content.startsWith('http') || content.startsWith('/')) {
            // It's a URL, try to get the content
            fetch(browserAPI.runtime.getURL(content))
                .then(response => response.text())
                .then(css => {
                    style.textContent = css;
                })
                .catch(err => console.error('Failed to load styles:', err));
        } else {
            // It's direct CSS content
            style.textContent = content;
        }
        
        // Inject into document
        (document.head || document.documentElement).appendChild(style);
        
    } catch (error) {
        console.error('Error injecting styles:', error);
    }
};

// Export for use in modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { injectContent, injectContentOnce };
}
