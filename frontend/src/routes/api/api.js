export default class Api {
	static baseURL = 'http://localhost:8196';
	static baseApiFile = 'db_api.php';
	
	static jwt = {};
	static user = {
		data: {
			VS_USER_ID: null,
			USER_USERNAME: null,
			USER_PROFILEPICTURE: null,
			USER_PROFILEDESCRIPTION: null,
			USER_DATETIMECREATED: null,
			USER_LASTUPDATE: null,
		},
		socials: [],
		subscribed: [],
		subscribers: [],
		user_videos_liked: [],
		user_videos_disliked: [],
		user_comments_liked: [],
		user_comments_disliked: [],
		user_stats: {
			videos: [],
			likes: [],
			views: [],
			shares: []
		}
	};

	/**
	 * Request data from the database. No Authentication required.
	 * 
	 * @param {string} type
	 * @param {string} index (can be 'all' and 'random' too)
	 * @param {string | number | null} specification
	 */
	static async get(type, index, specification = null) {
		const response = fetch(
			`${this.baseURL}/${this.baseApiFile}?medium=${type}&id=${index}${
				specification ? '&medium_id=' + specification : ''
			}`
		).then(response => {
			return response.json();
		}).catch(() => {
			console.error(`Failed to fetch '${type}' with id '${index}' ${specification ? 'and specification: ' + specification : ''}`);
		});

		return response;
	}

	/**
	 * Post actions as logged in user.
	 * @param {Array<Object<string, any>>} options
	 * @param {string} action
	 * @param {string} medium
	 */
	static async post(options, medium, action = "POST") {
		let params = new FormData();
		params.append('action', action);
		params.append("medium", medium);
		params.append('HTTP_AUTHORIZATION', `Bearer ${this.jwt}`);

		options.forEach(option => {
			if (option?.attribute_name.includes("_MEDIA")) {
				params.append(option?.attribute_name + "[]", option?.attribute);
			} else if (option?.attribute_name == "VIDEO_TAGS") {
				option?.attribute.forEach((/** @type {string} */ tag) => {
					params.append(option?.attribute_name + "[]", tag);
				});
			} else {
				params.append(option?.attribute_name, option?.attribute);
			}
		});

		const response = fetch(`${this.baseURL}/${this.baseApiFile}`, {
			method: 'POST',
			body: params
		}).then(response => {
			return response.json(); 
		}).catch(() => {
			console.error(`Failed to post '${action}' with options: '${JSON.stringify(options)}'`);
		});

		return response;
	}

	/**
	 * @param {string} username
	 * @param {string} password
	 */
	static async auth(username, password) {
		// sign/in/up
		let params = new FormData();
		params.append('action', 'POST');
		params.append('medium', 'auth');
		params.append('username', username);
		params.append('password', password);

		try {
			const response = await fetch(`${this.baseURL}/${this.baseApiFile}`, {
				method: 'POST',
				body: params
			});

			const json_response = await response.json();
			this.jwt = json_response['token'];
			this.accountExisted = json_response["response"] == "accountExisted" ? true : false;
			this.user.data = json_response['data'][0][0];
			this.user.socials = json_response['data']?.socials ?? [];
			this.user.subscribed = json_response['data']?.subscribed ?? [];
			this.user.subscribers = json_response['data']?.subscribers ?? [];
			this.user.user_videos_liked = json_response['data']?.liked ?? [];
			this.user.user_videos_disliked = json_response['data']?.disliked ?? [];
			this.user.user_comments_liked =
				json_response['data']?.comments_liked ?? [];
			this.user.user_comments_disliked =
				json_response['data']?.comments_disliked ?? [];
			this.user.user_stats.videos = json_response['data']?.stats?.videos ?? [];
			this.user.user_stats.likes = json_response['data']?.stats?.likes ?? [];
			this.user.user_stats.views = json_response['data']?.stats?.views ?? [];
			this.user.user_stats.shares = json_response['data']?.stats?.shares ?? [];

			return {
				accountExisted: this.accountExisted,
				token: this.jwt,
				user: this.user
			};
		} catch (err) {
			console.error(`Failed to authenticate ${username}.`);
		}
	}
}

// DEBUG: console.log(response.text());

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
