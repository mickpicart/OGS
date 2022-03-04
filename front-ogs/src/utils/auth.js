import Cookies from 'js-cookie'

let exports = {}

// Delete Cookie
exports.removeToken = () => {
	Cookies.remove('token')
}

// sameSite: 'Lax' prevent from Cookies to be sent
// with cross origin request
exports.setToken = (token) => {
	Cookies.set('token', token, {
		secure: true,
		sameSite: 'lax',
		expires: 7
	})
}

// Get Cookie named 'token'
exports.getToken = () => {
	return Cookies.get('token')
}

// If a Cookie named 'token' is available, it returns true :
// user is logged in
exports.isLoggedIn = () => {
	let token = exports.getToken()
	return !!token
}

export default exports
