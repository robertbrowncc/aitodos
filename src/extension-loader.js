// Ensure browser API compatibility
if (typeof browser === 'undefined') {
    var browser = chrome;
}

// Wrap code in an async IIFE to use await
(async function() {
    try {
        // Initialize your extension
        console.log('Extension loader initialized');
        
        // Example of using the browser API
        const storage = await browser.storage.local.get();
        console.log('Storage loaded:', storage);

        // Add your extension initialization code here
        
    } catch (error) {
        console.error('Error initializing extension:', error);
    }
})();
