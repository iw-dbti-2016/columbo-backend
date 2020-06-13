module.exports = {
	theme: {
		extend: {
			colors: {
				'primary': 'var(--bg-primary)',
				'box'    : 'var(--bg-box)',
				'box-fade': 'var(--bg-box-fade)',
			},
			textColor: {
				'primary'  : 'var(--text-primary)',
				'fade'     : 'var(--text-fade)',
				'fade-more': 'var(--text-fade-more)',
			}
		},
		fontFamily: {
			'sans': 'Montserrat,Helvetica,Arial,sans-serif',
			'mono': 'Roboto Mono,monospace'
		},
	},
	variants: {
		borderWidth: ['last'],
		margin: ['last'],
	},
	plugins: []
}
