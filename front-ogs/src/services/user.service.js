import axios from 'axios'
import authHeader from './auth-header'

const API_URL = process.env.VUE_APP_API_URL

class UserService {
	// All following Axios requests contain JWT Token
	// inside their Headers thanks to authHeader()
	// PUT, DELETE and PATCH requests will at first ask for the CSRF Token
	// to the Back-End API, Axios with then handle it when sending requests

	// Returns the authenticated user
	getUser() {
		return axios.get(API_URL + 'auth/user-profile/', {
			headers: authHeader()
		})
	}

	// Returns supervision datas and errors from DataBase
	getSupervisiondbdatas() {
		return axios.get(API_URL + 'supervisiondbdatas', {
			headers: authHeader()
		})
	}

	// Load external datas and errors to DataBase for
	// all websites
	async putLoadextdatasAll() {
		await axios.get('http://localhost:8000/sanctum/csrf-cookie')
		return await axios.put(
			API_URL + 'loadextdataserrorsall',
			{},
			{
				headers: authHeader()
			}
		)
	}

	//Load external datas and errors to DataBase for
	// one website
	async putLoadextdatas(id) {
		await axios.get('http://localhost:8000/sanctum/csrf-cookie')
		return await axios.put(
			API_URL + 'loadextdataserrors/' + id,
			{
				id: id
			},
			{
				headers: authHeader()
			}
		)
	}

	// Register e new website
	async putUrl(URL) {
		await axios.get('http://localhost:8000/sanctum/csrf-cookie')
		return await axios.put(
			API_URL + 'website/',
			{
				url: URL
			},
			{
				headers: authHeader()
			}
		)
	}

	// Delete one website and related datas and errors
	// from Database
	async deleteUrl(id) {
		await axios.get('http://localhost:8000/sanctum/csrf-cookie')
		return await axios.delete(API_URL + 'website/' + id, {
			headers: authHeader()
		})
	}

	// Modify one website supervision status
	async patchWebsiteSupervStatusChange(id) {
		await axios.get('http://localhost:8000/sanctum/csrf-cookie')
		return await axios.patch(
			API_URL + 'website/' + id,
			{
				id: id
			},
			{
				headers: authHeader()
			}
		)
	}
}

export default new UserService()
