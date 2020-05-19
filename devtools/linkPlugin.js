if (process.argv.length < 3) {
    console.error('Require path to source folder!');
    console.log("Run 'npm run linkPlugin <src>'.");
} else {
    const lnk = require('lnk');
    const fs = require('fs');
    const path = require('path');
    const dst = './Plugins';
    const srcPath = process.argv[2];
    if (fs.existsSync(dst)) {
        lnk(srcPath, dst)
            .then(() => console.log('Created link to plugin folder: ' + path.basename(srcPath)));
    } else {
        console.error('Could not access plugin folder!');
    }
}
