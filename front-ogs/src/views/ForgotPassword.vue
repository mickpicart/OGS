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
			<!-- Password reset email link form using v-model email -->
			<form action="#" @submit="requestResetPassword" novalidate>
				<h1 class="h3 mb-3 fw-normal" id="reset">
					Réinitialisation du mot de passe
				</h1>
				<!-- Invalid message to be displayed adding invalid-feedback class in JavaScript -->
				<div class="invalid-feedback">Email incorrect.</div>
				<div class="form-floating">
					<input
						type="email"
						class="form-control"
						id="email"
						placeholder="name@example.com"
						v-model="email"
						autocomplete="off"
						required
					/>
					<label for="email">Adresse email</label>
					<!-- Invalid message to be displayed adding invalid-feedback class in JavaScript -->
					<div class="invalid-feedback">Format d'email invalide.</div>
				</div>
				<br />
				<button type="submit" class="btn btn-primary">
					Envoi du lien
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
			email: null,
			loader: ''
		}
	},
	// All methods used in this component
	methods: {
		requestResetPassword: function (e) {
			// Connecting DOM elements to further manage errors messages display
			var reinitReset = document.getElementById('reset')
			var emailReset = document.getElementById('email')
			// Define email Regular Expression pattern to further check
			var emailPattern = new RegExp(
				/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
			)

			// Remove is-invalid class on all associated DOM elements
			reinitReset.classList.remove('is-invalid')
			emailReset.classList.remove('is-invalid')

			// If email is missing or if email and RexExp pattern don't match
			// error class added and an error message is displayed accordingly
			if (!this.email || !emailPattern.test(this.email)) {
				emailReset.classList.add('is-invalid')
			} // Loading spinner used to show the loading state --> displayed, v-if = true
			else if (
				// If email and RexExp pattern match
				emailPattern.test(this.email)
			) {
				this.loader = true
				// If email format is correct send password reset link email
				// using resetLink method from AuthService class
				AuthService.resetLink(this.email)
					.then((response) => {
						if (response.status === 201) {
							// If request answer is 201, pop up confirming
							// that reset email link was sent
							this.$alert(
								'Un email contenant un lien de réinitialisation de votre mot de passe vous a été envoyé.'
							)
						}
					})
					// Catch errors
					.catch((error) => {
						// If request answer is 400, error class added
						// and an error message is displayed accordingly
						if (error.response.status === 400) {
							reinitReset.classList.add('is-invalid')
						}
					})
					// Loading spinner used to show the loading state --> hidden, v-if = false
					.finally(() => (this.loader = false))
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

/* Under 576px screen width h3 lower case */
@media screen and (max-width: 576px) {
	.h3 {
		font-size: calc(1rem + 0.6vw);
	}
}
</style>
