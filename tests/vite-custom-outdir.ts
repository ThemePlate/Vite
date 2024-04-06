import fullConfig from './vite.config';

export default {
	...fullConfig,
	root: 'outdir',
	build: {
		...fullConfig.build,
		outDir: 'custom',
	}
};
