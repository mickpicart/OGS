<template>
	<div class="siteview">
		<!-- Navigation component tag -->
		<Navigation />
		<main class="container-fluid">
			<!-- Data binding : display when datas where created -->
			{{ this.datas.created_at }}
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
	name: 'siteview',
	// Declare components to be used in Vue instance
	components: {
		Navigation
	},
	// Data attribute creation : following variables
	// are defined for data binding with View
	data() {
		return {
			datas: ''
		}
	},
	// When Vue instance is mounted
	mounted() {
		EventBus.$on('datas', (datas) => {
			this.datas = datas
			console.log(this.datas)
		})
	},
	// All methods used in this component
	// None for the moment as this component is under construction
	methods: {}
}
</script>

<style lang="scss">
.alert {
	padding: 5px;
	margin-bottom: 0px;
	border-radius: 0px;
}

.btn.btn-warning {
	width: 110px;
}
</style>
