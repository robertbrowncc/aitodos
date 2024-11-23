const fs = require('fs');
const canvas = require('canvas');

async function generateFavicon() {
    const size = 32;
    const c = canvas.createCanvas(size, size);
    const ctx = c.getContext('2d');
    
    ctx.font = '28px Arial';
    ctx.fillText('🏠', 2, 24);
    
    const pngBuffer = c.toBuffer('image/png');
    fs.writeFileSync('public/favicon.png', pngBuffer);
    fs.writeFileSync('public/favicon.ico', pngBuffer);
}

generateFavicon();
