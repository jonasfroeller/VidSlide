export default class Api {
    jwt = {};

    constructor() {
        this.jwt = {};
    }

    /**
     * @param {string} type
     * @param {string} index can be all and random too
     */
    static async get(type, index) {
        const response = await fetch(`http://localhost:8196/index.php?medium=${type}&id=${index}`, {
            method: 'GET',
        });
        return await response.json();
    }

    /**
     * @param {string} action
     */
    static async post(action) {
        const response = await fetch('http://localhost:8196/index.php', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${this.jwt}`, 
            },
            body: JSON.stringify({
                "action": action
            })
        });
        return await response.json();
    }

    /**
     * @param {string} username
     * @param {string} password
     */
    static async auth(username, password) {
        const response = await fetch('http://localhost:8196/index.php', {
            method: 'POST',
            body: JSON.stringify({
                "action": 'auth',
                "username": username,
                "password": password
            }),
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
