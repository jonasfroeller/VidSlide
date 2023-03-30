export default class Api {
    static async get() {
        const response = await fetch('http://localhost/index.php', {
            method: 'GET',
        });
        return await response.json();
    }

    static async post() {
        const response = await fetch('http://localhost/index.php', {
            method: 'POST',
        });
        return await response.json();
    }
}
