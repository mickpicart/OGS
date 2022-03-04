<template>
	<div class="wrapper">
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
		<div class="alert alert-primary">
			<!-- Search form using v-model searchQuery -->
			<form id="search">
				Recherche
				<input
					name="query"
					class="form-control"
					v-model="searchQuery"
					placeholder="URL"
				/>
			</form>
			<!-- Add url form using v-model url -->
			<form action="#" method="post" class="form-inline">
				<input
					name="query"
					class="form-control"
					id="inputUrl"
					placeholder="URL"
					v-model="url"
				/>

				<!-- Button calling addUrl() method when clicked -->
				<button
					v-on:click.prevent="addUrl"
					type="submit"
					class="btn btn-warning"
				>
					Ajouter Site
				</button>
			</form>
		</div>
		<div class="grid-template">
			<table>
				<thead>
					<tr>
						<th
							v-for="(column, i1) in columns"
							:key="i1"
							@click="sortBy(column)"
							:class="{ active: sortKey == column }"
						>
							<!-- Data Binding retrieving column values and capitalizing first letter -->
							{{ column | capitalize }}
							<span
								class="arrow"
								:class="sortOrders[column] > 0 ? 'asc' : 'dsc'"
							></span>
						</th>
					</tr>
				</thead>

				<tbody>
					<tr v-for="(entry, i2) in filteredData" :key="i2">
						<td v-for="(column, i3) in columns" :key="i3">
							<img
								class="favicon"
								v-if="i3 == 0 && entry[column]['favicon']"
								:src="entry[column]['favicon']"
								alt=""
							/>
							<img
								class="sslicon"
								v-if="
									i3 == 0 &&
									entry[column] &&
									entry[column]['ssl'] == 1
								"
								src="../images/Lock.png"
								alt=""
							/>
							<img
								class="sslhidden"
								v-else-if="i3 == 0 && entry[column]['ssl'] != 1"
								src="../images/Lock.png"
								alt=""
							/>
							<a
								v-if="
									i3 == 0 &&
									entry[column] &&
									entry[column]['wp_admin_link']
								"
								:href="entry[column]['wp_admin_link']"
								target="_blank"
							>
								<img
									class="wpadminicon"
									src="../images/WP_admin.png"
									alt=""
								/>
							</a>
							<a
								v-else-if="
									i3 == 0 && !entry[column]['wp_admin_link']
								"
							>
								<img
									class="wpadminhidden"
									src="../images/WP_admin.png"
									alt=""
								/>
							</a>
							<a
								v-if="i3 == 0 && entry[column]['url']"
								:href="entry[column]['url']"
								target="_blank"
							>
								<!-- Data Binding retrieving website url -->
								{{ entry[column]['url'] }}</a
							>

							<div v-if="i3 == 1 && entry[column]">
								<div>
									<!-- Data Binding retrieving website PHP version -->
									PHP {{ entry[column]['version_php'] }}
								</div>
								<div>
									<!-- Data Binding retrieving website WordPress version -->
									WP {{ entry[column]['version_wp'] }}
								</div>
							</div>

							<div
								v-if="i3 == 2 && entry[column]['criticite'] > 0"
								:style="styleObject(entry[column]['criticite'])"
							>
								<div @click="expand()">
									Niveau de criticité :
									{{ entry[column]['criticite'] }}
								</div>
								<div v-show="show">
									<ul v-if="entry[column]['message']">
										<li
											v-for="(item, index) in entry[
												column
											]['message']"
											:key="item[index]"
										>
											{{ item }}
										</li>
									</ul>
								</div>
							</div>

							<div v-if="i3 == 3">
								<img
									class="link look"
									src="../images/Look.png"
									alt=""
									@click="emitDatas(entry)"
								/>

								<img
									data-toggle="popover"
									data-trigger="focus"
									:title="
										'Dernière MAJ : ' +
										entry[column]['updated_at']
									"
									class="link"
									src="../images/Refresh.png"
									alt=""
									@click="
										loadExtDatasOne(entry[column]['id'])
									"
								/>
								<img
									class="link trash"
									src="../images/Trash.png"
									alt=""
									@click="delUrl(entry[column]['id'])"
								/>
							</div>

							<div v-if="i3 == 4" class="form-check form-switch">
								<input
									class="form-check-input"
									type="checkbox"
									id="flexSwitchCheckChecked"
									:checked="entry[column]['is_supervised']"
									@click="supChange(entry[column]['id'])"
								/>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</template>

<script>
// Import services
import UserService from '@/services/user.service'
// Import EventBus that will allow datas sharing between Vue components
import EventBus from '@/services/bus.service'

export default {
	// Declare this Vue instance name
	name: 'grid',
	props: {
		data: Array,
		columns: Array
	},
	// Data attribute creation : following variables
	// are defined for data binding with View
	data() {
		return {
			searchQuery: '',
			sortKey: '',
			sortOrders: {},
			url: '',
			id: '',
			show: '',
			color: '',
			items: [],
			loader: '',
			gridKey: ''
		}
	},
	watch: {
		gridKey: function (val) {
			EventBus.$emit('gridKey', val)
		}
	},
	// This computed property "filteredData" will only re-evaluate
	// when its reactive dependencies have changed.
	computed: {
		// Search method combined with sorting
		filteredData: function () {
			let sortKey = this.sortKey
			let filterKey = this.searchQuery && this.searchQuery.toLowerCase()
			let order = this.sortOrders[sortKey] || 1
			let data = this.data

			if (filterKey) {
				data = data.filter(function (row) {
					return Object.keys(row).some(function (key) {
						return (
							String(row[key]).toLowerCase().indexOf(filterKey) >
							-1
						)
					})
				})
			}
			if (sortKey) {
				data = data.slice().sort(function (a, b) {
					a = a[sortKey]
					b = b[sortKey]
					return (a === b ? 0 : a > b ? 1 : -1) * order
				})
			}
			return data
		}
	},
	// Capitalize method to uppercase first character
	filters: {
		capitalize: function (str) {
			return str.charAt(0).toUpperCase() + str.slice(1)
		}
	},
	// All methods used in this component
	methods: {
		// Use EventBus to emit datas on click and redirect to siteview route
		// where such datas will be used to display
		emitDatas(val) {
			EventBus.$emit('datas', val)
			this.$router.push({ name: 'siteview' })
		},
		addUrl() {
			// Loading spinner used to show the loading state --> displayed, v-if = true
			this.loader = true
			// Regular expression pattern corresponding http and https url format
			var urlPattern = new RegExp(
				/(http|https):\/\/[\w-]+(\.[\w-]+)+([\w.,@?^=%&amp;:~+#-]*[\w@?^=%&amp;~+#-])?/
			)
			// Comparison between Regexp pattern and url input from user interface
			if (urlPattern.test(this.url)) {
				// If they match, put this url into DataBase using putUrl method from UserService class
				UserService.putUrl(this.url)
					.then((response) => {
						// If request answer is 201, pop up confirming
						// that this website has been added to DataBase
						if (response.status === 201) {
							this.$alert(
								'Le site web ' +
									response.data.website.url +
									' a été correctement ajouté en base de données.'
							)
							// To rerender the Grid component via gridKey increment and through the EventBus
							this.gridKey++
							this.url = ''
						}
					})
					// Catch errors
					.catch((error) => {
						// If request answer is 409, pop up explaining
						// that url already exists in DataBase
						if (error.response.status === 409) {
							this.$alert(
								'Ce site web existe déjà dans la base de données.'
							)
							this.url = ''
						}
					})
					// To rerender the Grid component via gridKey increment and through the EventBus
					// Loading spinner used to show the loading state --> hidden, v-if = false
					.finally(() => ((this.gridKey = 0), (this.loader = false)))
			}
			// If request answer is 400, pop up explaining
			// that url couldn't be put in DataBase due to wrong url format
			else
				this.$alert(
					"Le site web n'a pu être ajouté, merci de vérifier le format de l'URL (http:// ou https://)."
				)
			// Loading spinner used to show the loading state --> hidden, v-if = false
			this.loader = false
		},
		delUrl(id) {
			this.$confirm(
				'Êtes-vous sûr de vouloir supprimer ce site web de la base de données ?'
			).then(() => {
				// Loading spinner used to show the loading state --> displayed, v-if = true
				this.loader = true
				// If user confirms, delete this url from DataBase using deleteUrl method from UserService class
				UserService.deleteUrl(id)
					.then((response) => {
						// If request answer is 200, pop up confirming
						// that this website and its supervision datas and errors
						// were deleted from DataBase
						if (response.status === 200) {
							this.$alert(
								'Site web et données associées correctement supprimés de la base de données.'
							)
							// To rerender the Grid component via gridKey increment and through the EventBus
							this.gridKey++
						}
					})
					// Catch errors
					.catch((error) => {
						// If request answer is 400, pop up explaining
						// that url couldn't be deleted from DataBase
						if (error.response.status === 400) {
							this.$alert("Le site web n'a pu être supprimé.")
						}
					})
					// To rerender the Grid component via gridKey increment and through the EventBus
					// Loading spinner used to show the loading state --> hidden, v-if = false
					.finally(() => ((this.gridKey = 0), (this.loader = false)))
			})
		},
		// Commute column sort order when clicking on <th>
		sortBy: function (key) {
			this.sortKey = key
			this.sortOrders[key] = this.sortOrders[key] * -1
		},
		// Change error message background-color according to criticity level
		styleObject(crit) {
			if (crit > 3) return { backgroundColor: 'red' }
			else return { backgroundColor: 'orange' }
		},
		// Show or hide error message
		expand() {
			this.show = !this.show
		},
		loadExtDatasOne(id) {
			// Loading spinner used to show the loading state --> displayed, v-if = true
			this.loader = true
			// Reload ext datas and associated errors in DataBase
			// using putLoadextdatas method from UserService class
			UserService.putLoadextdatas(id)
				.then((response) => {
					// If request answer is 201, pop up confirming
					// that this website and its supervision datas and errors
					// were reloaded to DataBase
					if (response.status === 201) {
						this.$alert(
							'Les données externes et les erreurs relatives à ce site ont été correctement rechargées dans la base de données puis affichées.'
						)
						// To rerender the Grid component via gridKey increment and through the EventBus
						this.gridKey++
					}
				})
				// Catch and display errors in console
				.catch((error) => {
					console.error(error)
				})
				// To rerender the Grid component via gridKey increment and through the EventBus
				// Loading spinner used to show the loading state --> hidden, v-if = false
				.finally(() => ((this.gridKey = 0), (this.loader = false)))
		},
		supChange(id) {
			// Loading spinner used to show the loading state --> displayed, v-if = true
			this.loader = true
			// Commute one site supervision status
			// using patchWebsiteSupervStatusChange method from UserService class
			UserService.patchWebsiteSupervStatusChange(id)
				.then((response) => {
					if (response.status === 200) {
						// If request answer is 200, pop up confirming
						// that this website supervision status was
						// changed in DataBase
						this.$alert(
							'Le statut de supervision du site a bien été modifié.'
						)
						// To rerender the Grid component via gridKey increment and through the EventBus
						this.gridKey++
					}
				})
				// Catch and display errors in console
				.catch((error) => {
					console.error(error)
				})
				// To rerender the Grid component via gridKey increment and through the EventBus
				// Loading spinner used to show the loading state --> hidden, v-if = false
				.finally(() => ((this.gridKey = 0), (this.loader = false)))
		}
	},
	created() {
		let sortOrders = {}
		this.columns.forEach(function (key) {
			sortOrders[key] = 1
		})
		this.sortOrders = sortOrders
	}
}
</script>

<style>
body {
	font-family: Helvetica Neue, Arial, sans-serif;
	font-size: 14px;
	color: rgb(82, 82, 82);
}

button.btn.btn-primary {
	width: 200px;
	margin-left: 10px;
}

/* Loader spinner design */
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

/* Forms design */
.form-check,
.form-check-input {
	display: block;
	text-align: center;
	margin-left: 20px;
}

form {
	padding: 30px;
}

.btn-warning:hover {
	opacity: 80%;
}

div.alert.alert-primary {
	position: sticky;
	top: 114px;
	padding: 5px;
	margin-bottom: 0px;
	border-radius: 0px;
	z-index: 100;
}

form#search,
form.form-inline,
div.form-group.mx-sm-3.mb-2,
input.form-control {
	width: 50%;
	display: inline-block;
	padding: 0;
	margin: 0;
	vertical-align: middle;
}

input#inputUrl {
	margin-right: 5px;
}

input.form-control {
	padding: 6px;
	width: 300px;
	height: 35px;
}

form.form-inline {
	text-align: end;
}

ul {
	margin-bottom: 0;
}

/* Grid design */
table {
	width: 100%;
	border-collapse: collapse;
}

table tr:nth-child(2n + 1) {
	background-color: rgb(221, 221, 221);
}

thead {
	position: sticky;
	top: 164px;
	/* box-shadow: 0px -1.5px #000; */
}

th {
	background-color: #0d6efd;
	color: rgba(255, 255, 255, 0.77);
	cursor: pointer;
}

td,
th {
	/* border: 1px solid black; */
	padding: 14px;
	box-sizing: border-box;
}

th:nth-child(1),
td:nth-child(1) {
	text-align: left;
	width: 30%;
}
th:nth-child(2),
td:nth-child(2) {
	text-align: left;
	width: 12%;
}
th:nth-child(3),
td:nth-child(3) {
	text-align: left;
	width: 35%;
}
th:nth-child(4),
td:nth-child(4) {
	text-align: center;
	width: 10%;
}
th:nth-child(5),
td:nth-child(5) {
	text-align: center;
	width: 10%;
}

th.active {
	color: #fff;
}

th.active .arrow {
	opacity: 1;
}

/* Arrows design */
.arrow {
	display: inline-block;
	vertical-align: middle;
	width: 0;
	height: 0;
	margin-left: 5px;
	opacity: 0.66;
}

.asc {
	border-left: 4px solid transparent;
	border-right: 4px solid transparent;
	border-bottom: 4px solid #fff;
}

.arrow.dsc {
	border-left: 4px solid transparent;
	border-right: 4px solid transparent;
	border-top: 4px solid #fff;
}

/* Icons and links design */
a:link,
a:visited,
a:hover {
	text-decoration: none;
	color: black;
}

img.link,
img {
	height: 25px;
	width: 25px;
	margin-left: 10px;
}

img.link {
	cursor: pointer;
}

img.link.look {
	margin-left: 0px;
}

img.favicon {
	height: 23px;
	width: 23px;
	margin-right: 5px;
	vertical-align: top;
}

img.sslhidden,
img.wpadminhidden {
	visibility: hidden;
}

img.sslicon,
img.sslhidden {
	height: 25px;
	width: 25px;
	margin-right: 10px;
	margin-left: 5px;
}

img.wpadminicon,
img.wpadminhidden {
	vertical-align: top;
	height: 25px;
	width: 25px;
	margin-right: 20px;
	margin-left: 0px;
}

img.link.trash {
	height: 25px;
	width: 25px;
}

/* Swith button design */
.form-switch .form-check-input {
	height: 1.75em;
	width: 3.5em;
}

div.form-check.form-switch {
	margin: 0 55px;
}

/* Under 1280px screen width columns Configurations and Supervision are hidden */
@media screen and (max-width: 1280px) {
	th:nth-child(2),
	td:nth-child(2),
	th:nth-child(5),
	td:nth-child(5) {
		display: none;
	}
}

/* Under 992px screen width grid with y scrolling */
@media screen and (max-width: 992px) {
	table {
		width: 992px;
	}

	thead {
		/* position: relative; */
		top: 0px;
		z-index: 0;
	}

	.grid-template {
		overflow-y: scroll;
		height: 80vh;
	}

	form.form-inline {
		display: none;
	}

	form#search {
		width: 100%;
	}

	input.form-control {
		padding: 6px;
		width: 200px;
	}
}
</style>
