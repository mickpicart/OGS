<template>
	<div class="navigation sticky-top">
		<nav id="nav" class="navbar navbar-expand navbar-light bg-light">
			<div class="container-fluid">
				<!-- BCD agency logo -->
				<a class="navbar-brand" href="#">
					<img
						src="https://www.agencebcd.fr/wp-content/themes/agencebcd/img/favicons/android-icon-192x192.png"
						alt=""
						width="24"
						height="24"
						class="d-inline-block align-text-top"
					/>
					OGS
				</a>
				<!-- This div content will be displayed only if there's a user connected -->
				<div v-if="currentUser" class="navbar-nav ml-auto">
					<ul>
						<li class="nav-item">
							<!-- Link to the Dashboard route -->
							<router-link to="/dashboard" class="nav-link">
								<!-- Dashboard icon from fontawesome kit -->
								<i class="fas fa-tachometer-alt"></i>
								Tableau de bord
							</router-link>
						</li>
						<li class="nav-item">
							<!-- Link to the Profile route -->
							<router-link
								to="/profile"
								class="nav-link current-user"
							>
								<!-- User icon from fontawesome kit -->
								<i class="fas fa-user"></i>
								<!-- Data binding : display connected user name -->
								{{ currentUser.name }}
							</router-link>
						</li>
						<li class="nav-item">
							<!-- Link to the logout method -->
							<a
								class="nav-link btn btn-primary"
								@click.prevent="logOut"
							>
								<!-- Logout icon from fontawesome kit -->
								<i class="fas fa-sign-out-alt"></i>
								<span class="disconnect"> Se déconnecter </span>
							</a>
						</li>
					</ul>
				</div>
			</div>
		</nav>
	</div>
</template>

<script>
// Import services
import UserService from '@/services/user.service'
import AuthService from '@/services/auth.service'
// Import EventBus that will allow datas sharing between Vue components
import EventBus from '@/services/bus.service'

export default {
	// Data attribute creation : user is defined
	// as a variable for data binding with View
	data() {
		return {
			user: ''
		}
	},
	// Watcher : user datas will be shared with other Vue components
	watch: {
		user: function (val) {
			EventBus.$emit('user', val)
		}
	},
	// Once the Vue Instance is mounted method getUser from UserService class
	// is executed. If request is OK, user details are stored in variable user
	// if it fails with unauthorized message, logout method is executed
	mounted() {
		UserService.getUser()
			.then((response) => {
				if (response.data.id > 0) {
					this.user = response.data
				}
			})
			// Catch errors
			.catch((error) => {
				// If request answer is 401, user is logged out
				if (!error.response || error.response.status === 401) {
					this.logOut()
				}
			})
	},

	computed: {
		// This computed property "currentUser" will only re-evaluate
		// when its reactive dependency "user" have changed.
		currentUser: function () {
			return this.user
		}
	},
	// All methods used in this component
	methods: {
		// logOut method uses logout method from AuthService class
		// and redirect to home page (login)
		logOut: function () {
			if (AuthService.logout()) {
				this.$router.push({ name: 'home' })
			}
		}
	}
}
</script>

<style scoped lang="scss">
$bcd_color: #ea5b13;
#nav {
	padding: 10px 0;
	border-bottom: 2px solid #ea5b13;
	margin-bottom: 0px;
	.navbar-nav {
		.nav-item {
			padding-left: 5px;
			padding-right: 5px;
		}
		.nav-link.btn.btn-primary {
			color: #fff;
			margin-left: 10px;
			&:hover {
				color: #2c3e50;
			}
		}
	}
	ul {
		margin: 0;
		li {
			display: inline-block;
		}
	}
	a {
		font-weight: bold;
		color: #2c3e50;

		&.router-link-exact-active {
			color: #ea5b13;
		}
	}
}
// Under 768px screen width Dashboard icon and text
// and current user icon and name won't be displayed anymore.
// Only OGS logo and logout button will be displayed
@media screen and (max-width: 768px) {
	a.router-link-exact-active {
		display: none;
	}
}

// Under 576px screen width only logout icon remains
@media screen and (max-width: 576px) {
	span.disconnect {
		display: none;
	}
}
</style>
