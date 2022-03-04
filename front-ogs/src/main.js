import Vue from 'vue'
import App from './App.vue'
// Import Vue Router
import router from './router'
// Import Vue Simple Alert used to display alert and confirm windows
import VueSimpleAlert from 'vue-simple-alert'
Vue.use(VueSimpleAlert)
// Import Vue UI Preloader used to display a loading spinner
import loader from 'vue-ui-preloader'
Vue.use(loader)
// Import Bootstrap used for Navigation and Login boxes design
import 'bootstrap/dist/css/bootstrap.min.css'

Vue.config.productionTip = false

// Root Vue instance rendering #app template
new Vue({
	router,
	render: (h) => h(App)
}).$mount('#app')
