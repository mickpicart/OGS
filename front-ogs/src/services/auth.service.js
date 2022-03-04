import axios from 'axios'
import Auth from '@/utils/auth'

const API_URL = process.env.VUE_APP_API_URL + 'auth/'

class AuthService {
	// Function to login with user credetendials and protected with CSRF Token
	async login(user) {
		// Before POST, get CSRF Token from API
		await axios.get('http://localhost:8000/sanctum/csrf-cookie')
		return await axios
			// POST user credentials, including CSRF token automatically
			// handled by Axios
			.post(API_URL + 'login', {
				email: user.email,
				password: user.password
			})
	}

	// Logout function that removes JWT Token
	logout() {
		Auth.removeToken()
		return true
	}

	// Function to send reset link by email and protected with CSRF Token
	async resetLink(email) {
		// Before GET, get CSRF Token from API
		await axios.get('http://localhost:8000/sanctum/csrf-cookie')
		// GET method for reset link to be sent to user email,
		// including CSRF token automatically handled by Axios
		return await axios.put(API_URL + 'password/reset-link', {
			email: email
		})
	}

	// Function that resets user password in DataBase
	// thanks to reset link received by email
	async pwdReset(token, user) {
		// Before PATCH, get CSRF Token from API
		await axios.get('http://localhost:8000/sanctum/csrf-cookie')
		// PATCH method to modify user confirmed password in DataBase,
		// including CSRF token automatically handled by Axios
		return await axios.patch(API_URL + 'password/reset', {
			token: token,
			email: user.email,
			password: user.password,
			password_confirmation: user.password_confirmation
		})
	}
}

export default new AuthService()
