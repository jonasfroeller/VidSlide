// @ts-check

/* 
const displaySettingsPage = async (locale) => {
   await loadNamespaceAsync(locale, 'settings')
   setLocale(locale)

   // goto settings page
}
*/

/**
 * @typedef { import('$translation/i18n-types').Translation } Translation
 */

/** @type { Translation } */
const de_home = {
    LangSelect: {
        lang: ''
    },
    ThemeSelect: {
        theme: ''
    },
    Sidebar: {
        home: '',
        search: '',
        account: '',
        settings: ''
    },
    Header: {
        logIn: '',
        logOut: ''
    },
    SignInUp: {
        
    },
    pages: {
        home: {
            video_info_display_actions: {
                comments: '',
                description: ''
            }
        },
        search: {
            video_search_bar: {
                search: ''
            }
        },
        account: {
            
        },
        settings: {
            
        }
    }
}

export default de_home