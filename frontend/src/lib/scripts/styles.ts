export const CSS_Styles: CSS_STYLES = {
	SMALL_ELEMENT: {
		AVATAR_SIZE: 'w-[2.75rem]',
		FONT_PRIMARY: 'text-lg',
		FONT_SECONDARY: 'text-md text-primary-700 dark:text-primary-600',
		FONT_TERTIARY: 'text-sm text-primary-800 dark:text-primary-500'
	},
	MEDIUM_ELEMENT: {
		AVATAR_SIZE: 'w-[3.5rem]',
		FONT_PRIMARY: 'text-xl',
		FONT_SECONDARY: 'text-lg text-primary-700 dark:text-primary-600',
		FONT_TERTIARY: 'text-md text-primary-800 dark:text-primary-500'
	},
	LARGE_ELEMENT: {
		AVATAR_SIZE: 'w-[4.25rem]',
		FONT_PRIMARY: 'text-2xl',
		FONT_SECONDARY: 'text-xl text-primary-700 dark:text-primary-600',
		FONT_TERTIARY: 'text-lg text-primary-800 dark:text-primary-500'
	}
}

type CSS_STYLES = {
	SMALL_ELEMENT: CSS_STYLE,
	MEDIUM_ELEMENT: CSS_STYLE,
	LARGE_ELEMENT: CSS_STYLE
}

type CSS_STYLE = {
	AVATAR_SIZE: string,
	FONT_PRIMARY: string,
	FONT_SECONDARY: string,
	FONT_TERTIARY: string
}