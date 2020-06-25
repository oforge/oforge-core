if (process.argv.length < 3) {
    console.error('Require path to source folder!');
    console.log("Run 'npm run linkPlugin <src> [<PluginFolderName>]'.");
} else {
    const lnk = require('lnk');
    const fs = require('fs');
    const path = require('path');
    const dst = './Plugins';
    const srcPath = process.argv[2];
    console.log(process.argv);
    if (fs.existsSync(dst)) {
        if (process.argv.length === 3) {
            lnk(srcPath, dst)
                .then(() => console.log('Created link to plugin folder: ' + path.basename(srcPath)));
        } else {
            lnk(srcPath, dst, {rename: process.argv[3]})
                .then(() => console.log('Created link to plugin folder: ' + path.basename(srcPath)));
        }
    } else {
        console.error('Could not access plugin folder!');
    }
}
