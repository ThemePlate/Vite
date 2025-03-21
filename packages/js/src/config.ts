
export const configFile = 'vite.themeplate.json';

export const defaultUrls = {
	local: [],
	network: [],
};

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
