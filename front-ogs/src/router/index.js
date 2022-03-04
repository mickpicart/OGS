import Vue from 'vue'
import VueRouter from 'vue-router'
import Auth from '@/utils/auth'

import Home from '@/views/Home.vue'

Vue.use(VueRouter)

const routes = [
	{
		path: '/',
		name: 'home',
		component: Home,
		meta: {
			allowAnonymous: true
		}
	},
	{ path: '*', redirect: '/404' },
	{
		path: '/404',
		name: '404',
		// route level code-splitting
		// this generates a separate chunk (about.[hash].js) for this route
		// which is lazy-loaded when the route is visited.
		component: () => import('../views/NotFound.vue')
	},
	{
		path: '/reset-password',
		name: 'reset-password',
		meta: {
			allowAnonymous: true
		},
		component: () => import('../views/ForgotPassword.vue')
	},
	{
		path: '/reset-password/:token',
		name: 'reset-password-form',
		meta: {
			allowAnonymous: true
		},
		component: () => import('../views/ResetPasswordForm.vue')
	},
	{
		path: '/dashboard',
		name: 'dashboard',
		// route level code-splitting
		// this generates a separate chunk (about.[hash].js) for this route
		// which is lazy-loaded when the route is visited.
		component: () => import('../views/Dashboard.vue')
	},
	{
		path: '/profile',
		name: 'profile',
		// route level code-splitting
		// this generates a separate chunk (about.[hash].js) for this route
		// which is lazy-loaded when the route is visited.
		component: () => import('../views/Profile.vue')
	},
	{
		path: '/siteview',
		name: 'siteview',
		// route level code-splitting
		// this generates a separate chunk (about.[hash].js) for this route
		// which is lazy-loaded when the route is visited.
		component: () => import('../views/SiteView.vue')
	}
]

const router = new VueRouter({
	mode: 'history',
	base: process.env.BASE_URL,
	routes
})

router.beforeEach((to, from, next) => {
	if (to.name == 'home' && Auth.isLoggedIn()) {
		next({ path: '/dashboard' })
	} else if (!to.meta.allowAnonymous && !Auth.isLoggedIn()) {
		next({
			path: '/'
		})
	}
	next()
})

export default router
