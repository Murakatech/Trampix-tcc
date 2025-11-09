// Generate public/favicon.ico from the Trampix PNG logo using png-to-ico
const fs = require('fs');
const path = require('path');
let pngToIco = require('png-to-ico');
// Some versions export as default in CommonJS interop
pngToIco = pngToIco.default || pngToIco;

const srcPng = path.resolve(__dirname, '../storage/app/public/img/logo_trampix.png');
const outIco = path.resolve(__dirname, '../public/favicon.ico');

async function run() {
  try {
    if (!fs.existsSync(srcPng)) {
      console.error('Source PNG not found:', srcPng);
      process.exit(1);
    }
    const buf = await pngToIco(srcPng);
    fs.writeFileSync(outIco, buf);
    console.log('Generated favicon.ico at', outIco);
  } catch (err) {
    console.error('Failed to generate favicon.ico:', err);
    process.exit(1);
  }
}

run();