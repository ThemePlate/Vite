
export const configFile = 'vite.themeplate.json';

export const defaultUrls = {
	local: [],
	network: [],
};

export const defaultConfig = {
	build: {
		manifest: true,
	},
	server: {
		headers: {
			'Access-Control-Allow-Origin': '*',
		},
	},
}

export function ensure( comment: string ) {
	comment = comment.trim();

	if ( ! comment.startsWith( '/*!' ) ) {
		comment = '/*! ' + comment;
	}

	if ( ! comment.endsWith( '*/' ) ) {
		comment += ' */';
	}

	return comment;
}
