import fullConfig from './vite.config';

const { input } = fullConfig.build?.rollupOptions!;

export default {
	...fullConfig,
	root: 'unnamed',
	build: {
		...fullConfig.build,
		emptyOutDir: true,
		rollupOptions: {
			...fullConfig.build?.rollupOptions,
			input: Object.values( input! )
		}
	}
};
