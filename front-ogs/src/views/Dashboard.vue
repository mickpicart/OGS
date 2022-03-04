<template>
	<div class="dashboard">
		<!-- Navigation component tag -->
		<Navigation />
		<!-- Loader HTML code, v-if means it will only be displayed when -->
		<!-- this.loader is true -->
		<loader
			v-if="loader"
			object="#ff9633"
			color1="#ffffff"
			color2="#17fd3d"
			size="5"
			speed="2"
			bg="#343a40"
			objectbg="#999793"
			opacity="80"
			disableScrolling="false"
			name="box"
			class="overlay-spinner"
		></loader>
		<main class="container-fluid">
			<div class="alert alert-warning">
				<!-- Button calling reloadExtAndErrorsToDbGridDatas() method -->
				<!-- when clicked -->
				<button
					v-on:click.prevent="reloadExtAndErrorsToDbGridDatas"
					class="btn btn-warning"
					aria-pressed="false"
				>
					Rafraîchir
				</button>
				<!-- Data Binding with getDate() method -->
				<span class="maj">
					MAJ :
					<!-- Data Binding : display date and time of last datas update from DataBase -->
					{{ this.date }} à {{ this.time }}</span
				>
			</div>

			<!-- Grid component tag -->
			<!-- Data Binding to its props : this.gridDatas are values got from API -->
			<!-- gridColumns are grid columns titles -->
			<Grid :data="this.gridData" :columns="gridColumns"></Grid>
		</main>
	</div>
</template>

<script>
// Import services
import UserService from '@/services/user.service'
// Import components Navigation and Grid
import Navigation from '@/components/Navigation.vue'
import Grid from '@/components/Grid.vue'
// Import EventBus that will allow datas sharing between Vue components
import EventBus from '@/services/bus.service'

export default {
	// Declare components to be used in Vue instance
	components: {
		Navigation,
		Grid
	},
	// Data attribute creation : following variables
	// are defined for data binding with View
	data() {
		return {
			gridData: [],
			gridColumns: [
				'sites',
				'configurations',
				'erreurs',
				'actions',
				'supervision'
			],
			datas: [],
			date: '',
			time: '',
			loader: ''
		}
	},
	// When Vue instance is mounted
	mounted() {
		// Get gridKey value from $emit in Grid component
		// and if it equals 1 datas are reloaded from DataBase
		// and Grid is refreshed
		EventBus.$on('gridKey', (gridKey) => {
			if (gridKey == 1) {
				this.loadDbGridDatas()
			}
		})
		// Datas are loaded from DataBase when Vue instance is mounted
		// and current date is displayed close to refresh button
		this.loadDbGridDatas()
		this.getDate()
	},
	// All methods used in this component
	methods: {
		// Get current Date et Time
		getDate: function () {
			this.date = new Date().toLocaleDateString()
			this.time = new Date().toLocaleTimeString()
		},
		// Datas are reloaded from DataBase
		// when refresh button is clicked
		reloadExtAndErrorsToDbGridDatas() {
			// Loading spinner used to show the loading state --> displayed, v-if = true
			this.loader = true
			// Put all external supervision datas and errors to DataBase
			// using putLoadextdatasAll method from UserService class
			UserService.putLoadextdatasAll()
				.then((response) => {
					// If operation is successfull (201) : Grid is refreshed with datas from DataBase
					// then a message box is displayed
					if (response.status === 201) {
						// If request answer is 201, loadDbGridDatas() is executed and pop up confirming
						// that all websites and their supervision datas and errors
						// were reloaded to DataBase and diplayed
						this.loadDbGridDatas()
						this.$alert(
							'Les données externes et les erreurs ont été correctement rechargées dans la base de données puis affichées.'
						)
					}
				})
				// Catch and display errors in console
				.catch((error) => {
					console.error(error)
				})
				// Loading spinner used to show the loading state --> hidden, v-if = false
				.finally(() => (this.loader = false))
		},
		loadDbGridDatas() {
			// Loading spinner used to show the loading state --> displayed, v-if = true
			this.loader = true
			// Get last supervision datas and errors from DataBase
			// using getSupervisiondbdatas method from UserService class
			UserService.getSupervisiondbdatas()
				.then((response) => {
					// If operation is successfull, datas are formatted
					if (response.data) {
						this.datas = response.data
						this.gridData = this.formatGridDatas()
					}
				})
				// Catch and display errors in console
				.catch((error) => {
					console.error(error)
				})
				// Loading spinner used to show the loading state --> hidden, v-if = false
				.finally(() => (this.loader = false))
		},
		// Format datas to be displayed in the Grid
		formatGridDatas() {
			let datas = this.datas
			// For all websites
			for (var i = 0, len = datas.length; i < len; i++) {
				// Datas corresponding to "Sites" column
				// Retrieve url, favicon and SSL status
				datas[i].sites = {
					url: datas[i].url,
					favicon:
						'https://www.google.com/s2/favicons?domain=' +
						datas[i].url,
					ssl: datas[i].is_ssl_valid,
					wp_admin_link: ''
				}

				// Build link to wp-admin url if this site is a WorPress one
				if (datas[i].cms === 'WordPress') {
					datas[i].sites.wp_admin_link = datas[i].url + '/wp-admin'
				}

				// Datas corresponding to "Configurations" column
				// when WP Extension Datas exist
				if (datas[i].wp_ext_datas) {
					datas[i].configurations = {
						// Retrieve PHP version
						version_php: JSON.parse(datas[i].wp_ext_datas)
							.version_php,
						// Retrieve WP version
						version_wp: JSON.parse(datas[i].wp_ext_datas).version_wp
					}
				}

				// Datas corresponding to "Erreurs" column
				// Unset variables
				datas[i].erreurs = {
					message: '',
					criticite: 0
				}
				// Retrieve Errors messages
				datas[i].erreurs.message = JSON.parse(datas[i].message)
				// Retrieve Errors criticity level
				datas[i].erreurs.criticite = datas[i].criticity

				// Datas corresponding to "Actions" column
				// Retrieve website id and updated_at info
				datas[i].actions = {
					id: datas[i].website_id,
					updated_at: datas[i].updated_at
				}

				// Datas corresponding to "Supervision" column
				datas[i].supervision = {
					is_supervised: datas[i].supervised,
					id: datas[i].website_id
				}
			}
			// Get date and time when datas have been formatted
			this.getDate()
			// Return datas to be displayed within the Grid
			return datas
		}
	}
}
</script>

<style>
div.alert.alert-warning {
	position: sticky;
	top: 64px;
	padding: 5px;
	margin-bottom: 0px;
	border-radius: 0px;
	z-index: 100;
}

button.btn.btn-warning {
	width: 140px;
	margin-left: 0px;
}

span.maj {
	margin-left: 10px;
}

#overlay-spinner {
	height: 100%;
	width: 100%;
	position: fixed;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	z-index: 1000;
}

/* Under 576px screen width update date and time disappear */
@media screen and (max-width: 576px) {
	span.maj {
		display: none;
	}
}
</style>
