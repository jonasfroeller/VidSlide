export default class Api {
	static baseURL = 'http://localhost:8196';
	static baseApiFile = 'db_api.php';
	jwt = {};
	user = {};

	constructor() {
		this.jwt = {};
	}

	/**
	 * @param {string} type
	 * @param {string} index (can be 'all' and 'random' too)
	 * @param {string} specification
	 */
	static async get(type, index, specification = '') {
		// request as logged in or logged out user
		const response = await fetch(
			`${this.baseURL}/${this.baseApiFile}?medium=${type}&id=${index}${
				specification != '' ? '&medium_id=' + specification : ''
			}`
		);
		// console.log(response.text());
		return await response.json();
	}

	/**
	 * @param {string} action
	 * @param {Record<string, any>} options
	 */
	static async post(action, options) {
		// request as logged in user
		let params = new FormData();
		params.append('action', action);
		for (let key in options) {
			params.append(key, options[key]);
		}

		const response = await fetch(`${this.baseURL}/${this.baseApiFile}`, {
			method: 'POST',
			headers: {
				Authorization: `Bearer ${this.jwt}`
			},
			body: params
		});
		// console.log(response.text());
		return await response.json();
	}

	/**
	 * @param {string} username
	 * @param {string} password
	 */
	static async auth(username, password) {
		// sign/in/up
		let params = new FormData();
		params.append('action', 'auth');
		params.append('username', username);
		params.append('password', password);

		const response = await fetch(`${this.baseURL}/${this.baseApiFile}`, {
			method: 'POST',
			body: params
		});

		if (response.status >= 200 && response.status <= 299) {
			// console.log(response.text());
			const json_response = await response.json();
			// console.log(json_response);
			this.jwt = json_response['token'];
			this.accountExisted = JSON.parse(json_response['data'][0])[0]?.response == "accountExisted" ? true : false;
			this.user = JSON.parse(json_response['data'][0])[0];
			this.user.socials = JSON.parse(json_response['data'][0])[0]?.socials ?? null;
			this.user.subscribed = JSON.parse(json_response['data'][0])[0]?.subscribed ?? null;
			this.user.subscribers = JSON.parse(json_response['data'][0])[0]?.subscribers ?? null;
			this.user.user_videos_liked = JSON.parse(json_response['data'][0])[0]?.liked ?? null;
			this.user.user_videos_disliked = JSON.parse(json_response['data'][0])[0]?.disliked ?? null;
			this.user.user_comments_liked =
				JSON.parse(json_response['data'][0])[0]?.comments_liked ?? null;
			this.user.user_comments_disliked =
				JSON.parse(json_response['data'][0])[0]?.comments_disliked ?? null;
			this.user.user_stats = JSON.parse(json_response['data'][0])[0]?.stats ?? null; // videos, likes, views, shares

			return {
				token: this.jwt,
				accountExisted: false,
				user: this.user
			};
		} else {
			return { status: response.status, statusText: response.statusText };
		}
	}
}

/* 
WHY FormData and not JSON?

JSON approach:

JS:
JSON.stringify({
    "action": 'auth',
    "username": username,
    "password": password
})

Content-Type: application/json

PHP:
$jsonString = file_get_contents('php://input');
$jsonObj = json_decode($jsonString, true);
*/
