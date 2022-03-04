<template>
	<div class="home">
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
		<main class="form-signin">
			<!-- Password reset form using v-model user -->
			<form action="#" @submit="resetPassword" novalidate>
				<h1 class="h3 mb-3 fw-normal" id="reset">
					Choix d'un nouveau mot de passe
				</h1>
				<!-- Invalid message to be displayed adding invalid-feedback class in JavaScript -->
				<div class="invalid-feedback">
					La réinitialisation du mot de passe n'a pas pu être
					effectuée !
				</div>
				<div class="form-floating">
					<input
						type="email"
						class="form-control"
						id="email"
						placeholder="name@example.com"
						v-model="user.email"
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
						v-model="user.password"
						autocomplete="off"
						required
					/>
					<label for="password">Mot de passe de 12 caractères</label>
					<!-- Invalid message to be displayed adding invalid-feedback class in JavaScript -->
					<div class="invalid-feedback">
						Format du mot de passe invalide.<br />
						Il doit comporter au minimum 12 caractères dont :<br />
						1 lettre majuscule, 1 minuscule, 1 nombre et un
						caractère spécial.
					</div>
				</div>
				<br />
				<div class="form-floating">
					<input
						type="password"
						class="form-control"
						id="password_confirmation"
						placeholder="Confirmation du mot de passe"
						v-model="user.password_confirmation"
						autocomplete="off"
						required
					/>
					<label for="email">Confirmation du mot de passe</label>
					<!-- Invalid message to be displayed adding invalid-feedback class in JavaScript -->
					<div class="invalid-feedback">
						Les deux mots de passe ne sont pas identiques.
					</div>
				</div>
				<br />
				<button type="submit" class="btn btn-primary">
					Mettre à jour
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

export default {
	// Declare components to be used in Vue instance
	components: {
		Navigation
	},
	// Data attribute creation : following variables
	// are defined for data binding with View
	data() {
		return {
			user: { email: '', password: '', password_confirmation: '' },
			token: null,
			loader: ''
		}
	},
	// All methods used in this component
	methods: {
		resetPassword: function (e) {
			// Connecting DOM elements to further manage errors messages display
			var resetValidate = document.getElementById('reset')
			var emailValidate = document.getElementById('email')
			var pwdValidate = document.getElementById('password')
			var pwdConfirmValidate = document.getElementById(
				'password_confirmation'
			)
			// Define email Regular Expression pattern to further check
			var emailPattern = new RegExp(
				/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
			)
			// Define password Regular Expression pattern to further check
			var pwdPattern = new RegExp(
				/^(?=.*[A-Z])(?=.*[a-z])(?=.*[\W_])(?=.*[0-9]).{12,}$/
			)

			// Remove is-invalid class on all associated DOM elements
			resetValidate.classList.remove('is-invalid')
			emailValidate.classList.remove('is-invalid')
			pwdValidate.classList.remove('is-invalid')
			pwdConfirmValidate.classList.remove('is-invalid')

			// If email is missing or if email and RexExp pattern don't match
			// error class added and an error message is displayed accordingly
			if (!this.user.email || !emailPattern.test(this.user.email)) {
				emailValidate.classList.add('is-invalid')
			}
			if (
				// If password is missing or if password and RexExp pattern don't match
				// error class added and an error message is displayed accordingly
				!this.user.password ||
				!pwdPattern.test(this.user.password)
			) {
				pwdValidate.classList.add('is-invalid')
				// If password and password_confirmation are different
				// error class added and an error message is displayed accordingly
			} else if (this.user.password != this.user.password_confirmation) {
				pwdConfirmValidate.classList.add('is-invalid')
			} else if (
				emailPattern.test(this.user.email) &&
				this.user.password == this.user.password_confirmation
			) {
				// Loading spinner used to show the loading state --> displayed, v-if = true
				this.loader = true
				// If all datas format are OK and password and password_confirmation match
				// reset password accordingly in Database using pwdReset method from AuthService class
				AuthService.pwdReset(this.$route.params.token, this.user)
					.then((response) => {
						if (response.status === 201) {
							// If request answer is 201, pop up confirming
							// that password was reset in DataBase et redirect to login route
							this.$alert(
								'Votre mot de passe a été réinitialisé.'
							)
							this.$router.push({ name: 'home' })
						}
					})
					// Catch errors
					.catch((error) => {
						// If request answer is 400 or 422 (token expired or wrong email), error class added
						// and an error message is displayed accordingly
						if (
							error.response.status === 400 ||
							error.response.status === 422
						) {
							resetValidate.classList.add('is-invalid')
						}
					})
					// Loading spinner used to show the loading state --> hidden, v-if = false
					.finally(() => (this.loader = false))
				e.preventDefault()
			}
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

button.btn.btn-primary {
	margin-left: 0;
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

@media screen and (max-width: 576px) {
	.h3 {
		font-size: calc(1rem + 0.6vw);
	}
}
</style>
