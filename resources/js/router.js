import { createRouter, createWebHistory } from 'vue-router';
import HomePage from './pages/HomePage.vue';
import ApiDocsPage from './pages/ApiDocsPage.vue';

export const router = createRouter({
    history: createWebHistory(),
    routes: [
        { path: '/', name: 'home', component: HomePage },
        { path: '/docs', name: 'docs', component: ApiDocsPage },
    ],
    scrollBehavior() {
        return { top: 0 };
    },
});
