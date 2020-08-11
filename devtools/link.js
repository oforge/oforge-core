// npm run link p|plugin <srcPath> [<FolderName>]'
// npm run link t|theme <srcPath> [<FolderName>]'

const INDEX_CONTEXT = 2;
const INDEX_SRC = 3;
const INDEX_NAME = 4;

var printHelp = false;
let dst = null;
let context = null;

if (process.argv.length < 3) {
    console.error('Require argument context: p|plugin|t|theme');
    printHelp = true;
} else {
    switch (process.argv[INDEX_CONTEXT].toLowerCase()) {
        case 'p':
        case 'plugin':
            dst = './Plugins';
            context = 'Plugin';
            break;
        case 't':
        case 'theme':
            dst = './Themes';
            context = 'Theme';
            break;
    }
    if (context === null) {
        console.error('Argument context must be of: p|plugin|t|theme');
        printHelp = true;
    }
}

if (process.argv.length < 4) {
    console.error('Require argument srcPath!');
    printHelp = true;
}
if (printHelp) {
    console.log("Run 'npm run link <context=p|plugin|t|theme> <srcPath> [<FolderName>]'.");
} else {
    const lnk = require('lnk');
    const fs = require('fs');
    const path = require('path');
    const srcPath = process.argv[INDEX_SRC];
    if (fs.existsSync(dst)) {
        if (process.argv.length === 4) {
            lnk(srcPath, dst)
                .then(() => console.log('Created link to ' + context + ' folder: ' + path.basename(srcPath)));
        } else {
            lnk(srcPath, dst, {rename: process.argv[INDEX_NAME]})
                .then(() => console.log('Created link to ' + context + ' folder: ' + path.basename(srcPath)));
        }
    } else {
        console.error('Could not access ' + context + ' folder!');
    }
}
