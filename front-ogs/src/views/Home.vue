<template>
	<div class="home">
		<!-- Navigation component tag -->
		<Navigation />
		<main class="form-signin">
			<!-- Connexion form using v-model login -->
			<form action="#" @submit="logIn" method="POST" novalidate>
				<h1 class="h3 mb-3 fw-normal" id="connexion">Connexion</h1>
				<!-- Invalid message to be displayed adding invalid-feedback class in JavaScript -->
				<div class="invalid-feedback">
					Vos identifiants sont incorrects.
				</div>
				<div class="form-floating">
					<input
						type="email"
						class="form-control"
						id="email"
						placeholder="name@example.com"
						v-model="login.email"
						autocomplete="off"
						required
					/>
					<label for="email">Adresse email</label>
					<!-- Invalid message to be displayed adding invalid-feedback class in JavaScript -->
					<div class="invalid-feedback">Format d'email invalide.</div>
				</div>
				<br />
				<div class="form-floating">
					<input
						type="password"
						class="form-control"
						id="password"
						placeholder="Mot de passe"
						v-model="login.password"
						min="12"
						autocomplete="off"
						required
					/>
					<label for="password">Mot de passe</label>
					<!-- Invalid message to be displayed adding invalid-feedback class in JavaScript -->
					<div class="invalid-feedback">
						Merci de compléter votre mot de passe.
					</div>
					<!-- Link to password reset email link route -->
					<router-link to="/reset-password" class="nav-link">
						Mot de passe oublié ?
					</router-link>
				</div>
				<br />
				<button type="submit" class="btn btn-primary">
					Se connecter
				</button>
			</form>
		</main>
	</div>
</template>

<script>
// Import component Navigation
import Navigation from '@/components/Navigation.vue'
// Import services
import AuthService from '@/services/auth.service'
// Import utils
import Auth from '@/utils/auth'

export default {
	// Declare this Vue instance name
	name: 'Home',
	// Declare components to be used in Vue instance
	components: {
		Navigation
	},
	// Data attribute creation : following variables
	// are defined for data binding with View
	data() {
		return {
			errors: [],
			login: { email: '', password: '' }
		}
	},
	// All methods used in this component
	methods: {
		// logIn method
		logIn: function (e) {
			// Connecting DOM elements to further manage errors messages display
			var connexionValidate = document.getElementById('connexion')
			var emailValidate = document.getElementById('email')
			var pwdValidate = document.getElementById('password')
			// Define email Regular Expression pattern to further check
			var emailPattern = new RegExp(
				/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
			)

			// Remove is-invalid class on all associated DOM elements
			connexionValidate.classList.remove('is-invalid')
			emailValidate.classList.remove('is-invalid')
			pwdValidate.classList.remove('is-invalid')

			// If email and RexExp pattern don't match error class added and
			// an error message is displayed accordingly
			if (!emailPattern.test(this.login.email)) {
				emailValidate.classList.add('is-invalid')
			}
			// If password is missing error class added
			// and an error message is displayed accordingly
			if (!this.login.password) {
				pwdValidate.classList.add('is-invalid')
			} else if (
				// If email and RexExp pattern match
				// an if a password is entered
				emailPattern.test(this.login.email) &&
				this.login.password
			) {
				// Attempt user login with given credentials
				// using login method from AuthService class
				AuthService.login(this.login)
					.then((response) => {
						// If credentials are good ones set JWT Token as a Cookie (SameSite 'Lax')
						// and redirect to dashboard route
						if (response.data.access_token) {
							Auth.setToken(response.data.access_token)
							this.$router.push({ name: 'dashboard' })
						}
					})
					// Catch errors
					.catch((error) => {
						// If request answer is 400, error class added
						// and an error message is displayed accordingly
						if (error.response.status === 400) {
							connexionValidate.classList.add('is-invalid')
						}
					})
			}
			e.preventDefault()
		}
	}
}
</script>

<style scoped lang="scss">
.form-signin {
	display: flex;
	align-items: center;
	justify-content: center;
	padding-top: 40px;
	padding-bottom: 40px;

	form {
		box-shadow: 0px 0px 10px 0px #ddd;
		min-width: 30vw;

		padding: 2rem;
	}
}

.form-floating > .form-control {
	width: 100%;
}

a.nav-link {
	padding: 0;
	color: #0d6efd;
	text-decoration: underline;
}

button.btn.btn-primary {
	margin-left: 0;
}
</style>
