"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
const path_1 = require("path");
const fs_1 = require("fs");
function themeplate() {
    return {
        name: 'vite-plugin-themeplate',
        enforce: 'post',
        configureServer(server) {
            const { config, httpServer } = server;
            httpServer?.once('listening', () => {
                const checker = setInterval(() => {
                    if (null !== server.resolvedUrls) {
                        const outDir = (0, path_1.resolve)(config.root, config.build.outDir);
                        const outFile = (0, path_1.resolve)(outDir, 'themeplate');
                        if (!(0, fs_1.existsSync)(outDir)) {
                            (0, fs_1.mkdirSync)(outDir);
                        }
                        (0, fs_1.writeFileSync)(outFile, server.resolvedUrls.local[0]);
                        clearInterval(checker);
                    }
                }, 0);
            });
        },
    };
}
exports.default = themeplate;
