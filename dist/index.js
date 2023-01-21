"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
const vite_1 = require("vite");
const path_1 = require("path");
const fs_1 = require("fs");
function themeplate() {
    return {
        name: 'vite-plugin-themeplate',
        enforce: 'post',
        config(config) {
            return (0, vite_1.mergeConfig)(config, {
                build: {
                    manifest: true,
                },
            });
        },
        configureServer(server) {
            const { config, httpServer } = server;
            const outDir = (0, path_1.resolve)(config.root, config.build.outDir);
            const outFile = (0, path_1.resolve)(outDir, 'themeplate');
            const clean = () => {
                if ((0, fs_1.existsSync)(outFile)) {
                    (0, fs_1.rmSync)(outFile);
                }
            };
            process.on('exit', clean);
            process.on('SIGINT', process.exit);
            process.on('SIGTERM', process.exit);
            process.on('SIGHUP', process.exit);
            httpServer?.once('listening', () => {
                const checker = setInterval(() => {
                    if (null !== server.resolvedUrls) {
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
