<template>
	<div class="profile">
		<!-- Navigation component tag -->
		<Navigation />
		<main class="container-fluid">
			<!-- Data binding : display connected user name and details -->
			Bonjour {{ currentUser.name }},<br />{{ currentUser }}
		</main>
	</div>
</template>

<script>
// Import component Navigation
import Navigation from '@/components/Navigation.vue'
// Import EventBus that will allow datas sharing between Vue components
import EventBus from '@/services/bus.service'

export default {
	// Declare this Vue instance name
	name: 'Profile',
	// Declare components to be used in Vue instance
	components: {
		Navigation
	},
	// Data attribute creation : following variables
	// are defined for data binding with View
	data() {
		return {
			user: ''
		}
	},
	// When Vue instance is mounted
	mounted() {
		EventBus.$on('user', (user) => {
			this.user = user
		})
	},
	computed: {
		// This computed property "currentUser" will only re-evaluate
		// when its reactive dependency "user" have changed from the EventBus
		currentUser() {
			return this.user
		}
	},
	// All methods used in this component
	methods: {}
}
</script>

<style lang="scss">
@media screen and (max-width: 576px) {
	span.disconnect {
		display: none;
	}
}
</style>
