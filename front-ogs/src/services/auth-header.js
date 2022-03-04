import Auth from '@/utils/auth'

// Create a function that defines the Authorization header
// containing JWT Token with Bearer. Axios HTTP requests to the
// Back-End API will use it
export default function authHeader() {
	let token = Auth.getToken()
	if (token) {
		return { Authorization: 'Bearer ' + token }
	} else {
		return false
	}
}
