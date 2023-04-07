export default class Api {
    jwt = {};
    account = {};

    constructor() {
        this.jwt = {};
    }

    /**
     * @param {string} type
     * @param {string} index (can be 'all' and 'random' too)
     * @param {string} specification
     */
    static async get(type, index, specification = "") { // request as logged in or logged out user
        // @ts-ignore
        const response = await fetch(`http://localhost:8196/index.php?medium=${type}&id=${index}${specification != "" ? "&medium_id=".specification : ""}`);
        // console.log(response.text());
        return await response.json();
    }

    /**
     * @param {string} action
     */
    static async post(action) { // request as logged in user
        let params = new FormData();
        params.append("action", action);

        const response = await fetch('http://localhost:8196/index.php', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${this.jwt}`, 
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
    static async auth(username, password) { // sign/in/up
        let params = new FormData();
        params.append("action", "auth");
        params.append("username", username);
        params.append("password", password);

        const response = await fetch('http://localhost:8196/index.php', {
            method: 'POST',
            body: params
        });

        if (response.status >= 200 && response.status <= 299) {
            // console.log(response.text());
            const json_response = await response.json(); 
            // console.log(json_response);
            this.jwt = json_response["token"];
            this.account = JSON.parse(json_response["data"][0]);
            return { token: this.jwt, accountExisted: false, account: this.account };
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