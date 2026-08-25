import { createRouter, createWebHistory } from 'vue-router';
import HomePage from './pages/HomePage.vue';
import ApiDocsPage from './pages/ApiDocsPage.vue';
import { locales, setLocale } from './i18n';

const localeCodes = locales.map(({ code }) => code);

function localizedPath(locale, routeName) {
    return routeName === 'docs' ? `/${locale}/docs` : `/${locale}`;
}

function updateSeoTags(to) {
    const origin = window.location.origin;
    const routeName = to.name === 'docs' ? 'docs' : 'home';
    const alternates = [...locales, { code: 'x-default' }];

    document.querySelectorAll('link[data-locale-alternate]').forEach((element) => element.remove());
    alternates.forEach(({ code }) => {
        const link = document.createElement('link');
        const targetLocale = code === 'x-default' ? 'en' : code;
        link.rel = 'alternate';
        link.hreflang = code;
        link.href = `${origin}${localizedPath(targetLocale, routeName)}`;
        link.dataset.localeAlternate = '';
        document.head.appendChild(link);
    });

    let canonical = document.querySelector('link[rel="canonical"]');
    if (! canonical) {
        canonical = document.createElement('link');
        canonical.rel = 'canonical';
        document.head.appendChild(canonical);
    }
    canonical.href = `${origin}${to.path}`;
}

export const router = createRouter({
    history: createWebHistory(),
    routes: [
        { path: '/', redirect: '/en' },
        { path: '/docs', redirect: '/en/docs' },
        { path: '/:locale/docs', name: 'docs', component: ApiDocsPage },
        { path: '/:locale', name: 'home', component: HomePage },
        { path: '/:pathMatch(.*)*', redirect: '/en' },
    ],
    scrollBehavior() {
        return { top: 0 };
    },
});

router.beforeEach((to) => {
    if (! to.params.locale) {
        return true;
    }

    if (! localeCodes.includes(to.params.locale)) {
        return '/en';
    }

    setLocale(to.params.locale);
    return true;
});

router.afterEach((to) => updateSeoTags(to));
