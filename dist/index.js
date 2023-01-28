"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
const vite_1 = require("vite");
const path_1 = require("path");
const fs_1 = require("fs");
const configFile = 'vite.themeplate.json';
const defaultUrls = {
    local: [],
    network: [],
};
function writeConfig(config, isBuild, urls = defaultUrls) {
    const file = (0, path_1.resolve)(config.root, configFile);
    const outDir = config.build.outDir;
    const data = {
        outDir,
        isBuild,
        urls,
    };
    (0, fs_1.writeFileSync)(file, JSON.stringify(data, null, 2), 'utf8');
}
function themeplate() {
    let resolvedConfig;
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
        configResolved(config) {
            resolvedConfig = config;
        },
        writeBundle() {
            writeConfig(resolvedConfig, true);
        },
        configureServer(server) {
            const { config, httpServer } = server;
            const outFile = (0, path_1.resolve)(config.root, configFile);
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
                        if (!(0, fs_1.existsSync)(config.root)) {
                            (0, fs_1.mkdirSync)(config.root);
                        }
                        writeConfig(config, false, server.resolvedUrls);
                        clearInterval(checker);
                    }
                }, 0);
            });
        },
    };
}
exports.default = themeplate;
