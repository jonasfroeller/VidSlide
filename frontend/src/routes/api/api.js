export default class Api {
    jwt = {};

    constructor() {
        this.jwt = {};
    }

    /**
     * @param {string} type
     * @param {string} index (can be 'all' and 'random' too)
     */
    static async get(type, index) {
        const response = await fetch(`http://localhost:8196/index.php?medium=${type}&id=${index}`);
        return await response.json();
    }

    /**
     * @param {string} action
     */
    static async post(action) {
        let params = new FormData();
        params.append("action", action);

        const response = await fetch('http://localhost:8196/index.php', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${this.jwt}`, 
            },
            body: params
        });
        return await response.json();
    }

    /**
     * @param {string} username
     * @param {string} password
     */
    static async auth(username, password) {
        let params = new FormData();
        params.append("action", "auth");
        params.append("username", username);
        params.append("password", password);

        const response = await fetch('http://localhost:8196/index.php', {
            method: 'POST',
            body: params
        });

        if (response.status >= 200 && response.status <= 299) {
            const jwt = await response.json();
            this.jwt = jwt;
            return jwt;
        } else {
            return { status: response.status, statusText: response.statusText }
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