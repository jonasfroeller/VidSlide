import { describe, it, expect } from 'vitest';
const axios = require('axios');
const http = require('http');

let jwt = {};
let user = {};
let accountExisted = false;

function respond(options) {
  return new Promise((resolve, reject) => {
    const req = http.request(options, (res) => {
      let data = '';

      res.on('data', (chunk) => {
        data += chunk;
      });

      res.on('end', () => {
        resolve(JSON.parse(data));
      });
    });

    req.on('error', (error) => {
      reject(error);
    });

    req.end();
  });
}

// API-Functions (with http because fetch (browser-function) is not NodeJS compatible)
/**
 * @param {string} type
 * @param {string} index (can be 'all' and 'random' too)
 * @param {string} specification
 */
async function get(type, index, specification = '') {
  const options = {
    hostname: 'localhost',
    port: 8196,
    path: `/db_api.php?medium=${type}&id=${index}${specification !== '' ? '&medium_id=' + specification : ''}`,
    method: 'GET'
  };

  return respond(options);
}

/**
 * @param {string} username
 * @param {string} password
 */
async function auth(username, password) {
  const url = 'http://localhost:8196/db_api.php';
  const formData = new FormData();
  formData.append('action', 'auth');
  formData.append('username', username);
  formData.append('password', password);

  // eslint-disable-next-line no-useless-catch
  try {
    const response = await axios.post(url, formData, {
      body: formData
    });

    if (response.status >= 200 && response.status <= 299) {
			// console.log(response.text());
			const json_response = await response.data;
			// console.log(json_response);
			jwt = json_response['token'];
			accountExisted = JSON.parse(json_response['data'][0])[0]?.response == "accountExisted" ? true : false;
			user = JSON.parse(json_response['data'][0])[0];
			user.socials = JSON.parse(json_response['data'][0])[0]?.socials ?? null;
			user.subscribed = JSON.parse(json_response['data'][0])[0]?.subscribed ?? null;
			user.subscribers = JSON.parse(json_response['data'][0])[0]?.subscribers ?? null;
			user.user_videos_liked = JSON.parse(json_response['data'][0])[0]?.liked ?? null;
			user.user_videos_disliked = JSON.parse(json_response['data'][0])[0]?.disliked ?? null;
			user.user_comments_liked =
				JSON.parse(json_response['data'][0])[0]?.comments_liked ?? null;
			user.user_comments_disliked =
				JSON.parse(json_response['data'][0])[0]?.comments_disliked ?? null;
			user.user_stats = JSON.parse(json_response['data'][0])[0]?.stats ?? null; // videos, likes, views, shares

			return {
				accountExisted: accountExisted,
				token: jwt,
				user: user
			};
		} else {
			return { status: response.status, statusText: response.statusText };
		}
  } catch (error) {
    throw error;
  }
}

/**
 * @param {string} action
 * @param {Record<string, any>} options
 */
async function post(action, options) {
  const url = 'http://localhost:8196/db_api.php';
  const formData = new FormData();
  formData.append('action', action);
  formData.append('HTTP_AUTHORIZATION', `Bearer ${jwt}`);

  for (const [key, value] of options) {
    formData.append(key, value);
  }

  // eslint-disable-next-line no-useless-catch
  try {
    const response = await axios.post(url, formData, {
      body: formData
    });

    if (response.status >= 200 && response.status <= 299) {
      return await response.data;
    } else {
      return { status: response.status, statusText: response.statusText };
    }
  } catch (error) {
    throw error;
  }
}

// GET-API-Tests
describe('api-fetch-GET', () => {
  // - medium=video [MEDIUM] // gets videos and video info
  //   - id=all [ID]
  //     - medium_id=? [ID++] // all videos of user
  //   - id=title [ID]
  //     - medium_id=? [ID++] // all videos with title including text
  //   - id=tag [ID]
  //     - medium_id=? [ID++] // all videos with tag including text
  //   - id=random [ID]
  //   - id=? [ID] 
  it('get all videos of user by user id', async () => {
    const id = "all"; 
    const medium_id = 1; 
    const response = await get('video', id, medium_id);
    const result = JSON.parse(response['data'][0]);
    expect(result.length).toBeGreaterThan(0);
    expect(result[0]["VS_USER_ID"]).toBe(medium_id);
  })
  it('get video by title', async () => {
    const id = "title";
    const medium_id = "a"; 
    const response = await get('video', id, medium_id);
    const result = JSON.parse(response['data'][0]);
    expect(result.length).toBeGreaterThan(0);
    expect(result[0]["VIDEO_TITLE"]).toContain(medium_id);
  })
  it('get video by tag', async () => {
    const id = "tag";
    const medium_id = "a"; 
    const response = await get('video', id, medium_id);
    const tags = JSON.parse(response['data'][0]);
    expect(tags.length).toBeGreaterThan(0);
  })
  it('get random video', async () => {
    const id = "random";
    const response = await get('video', id);
    const result = JSON.parse(response['data'][0]);
    expect(result.length).toBeGreaterThan(0);
    expect(result[0]["VS_VIDEO_ID"]);
  })
  it('get video by invalid id', async () => {
    const id = -1;
    const response = await get('video', id);
    expect(response.error).toContain("video not found");
  })
    it('get video by valid id', async () => {
    const id = 1;
    const response = await get('video', id);
    const result = JSON.parse(response['data'][0]);
    expect(result.length).toBeGreaterThan(0);
    expect(result[0]["VS_VIDEO_ID"]).toBe(id);
  })

  // - medium=user [MEDIUM]
  //   - id=video [ID]
  //     - medium_id=? [ID++] // creator of video
  //   - id=username [ID]
  //     - medium_id=? [ID++] // username of user
  //   - id=? [ID] 
  it('get user by video', async () => {
    const id = "video";
    const medium_id = 1;
    const response = await get('user', id, medium_id);
    const result = JSON.parse(response['data'][0]);
    expect(result.length).toBe(1);
  })
  it('get user by username', async () => {
    const id = "username";
    const medium_id = "Jonesisfroellerix";
    const response = await get('user', id, medium_id);
    const result = JSON.parse(response['data'][0]);
    expect(result.length).toBe(1);
    expect(result[0]["USER_USERNAME"]).toBe(medium_id);
  })
  it('get user by id', async () => {
    const id = 1;
    const response = await get('user', id);
    const result = JSON.parse(response['data'][0]);
    expect(result.length).toBe(1);
    expect(result[0]["VS_USER_ID"]).toBe(id);
  })

  // - medium=comments [MEDIUM]
  //   - id=? (video id) [ID] // get comments of video
  // - medium=tags [MEDIUM] 
  //   - id=? (video id) [ID] // get tags of video
  // - medium=feedback [MEDIUM]
  //   - id=? (video id) [ID] // get feedback of video
  it('get comments of video', async () => {
    const id = 1;
    const response = await get('comments', id);
    const result = JSON.parse(response['data']["comments"]);
    expect(result.length).toBeGreaterThan(0);
  })
  it('get tags of video', async () => {
    const id = 1;
    const response = await get('tags', id);
    const result = JSON.parse(response['data']["tags"]);
    expect(result.length).toBeGreaterThan(0);
  })
  it('get feedback of video', async () => {
    const id = 1;
    const response = await get('feedback', id);
    const result = JSON.parse(response['data']["feedback"]);
    expect(result.length).toBeGreaterThan(0);
  })
})

// POST-API-Tests
describe('api-fetch-POST', () => {
  // - action=auth [ACTION]
  //   - username=?&password=? [ID] // => auth token if password for user is valid or account doesn't exist (will be created)
  // - action=video [ACTION] 
  //   - HTTP_AUTHORIZATION=?&VIDEO_MEDIA=?&VIDEO_TITLE=?&VIDEO_DESCRIPTION=?
  // - action=comment [ACTION]
  //   - HTTP_AUTHORIZATION=?&COMMENT_MESSAGE=?&VS_VIDEO_ID=?&VS_USER_ID=?(&COMMENT_PARENT_ID=?)
  // - action=feedback [ACTION]
  //  - HTTP_AUTHORIZATION=?&VIDEO_FEEDBACK_TYPE=?&VS_VIDEO_ID=?&VS_USER_ID=?
  // - action=follow [ACTION]
  //  - HTTP_AUTHORIZATION=?&FOLLOWING_SUBSCRIBER=?&FOLLOWING_SUBSCRIBED=?
  // - action=signout [ACTION]  
  it('authenticate user', async () => {
    const response = await auth('Jonesisfroellerix', "Password2$");
    expect(response.token).toBeDefined();
    expect(jwt).toBe(response.token);
    expect(user).toBe(response["user"]);
    expect(accountExisted).toBeTypeOf("boolean");
  })
  /**
   * @vitest-environment jsdom
   */
  it('post video', async () => { // TODO: fix this test (file corrupts somehow)
    const options = new Map();

    const video = await fetch("http://localhost:8196/media/video/uploaded/vid_1.MP4");
    const video_file = await video.blob();

    const fileInput = document.createElement("input");
    fileInput.type = 'file';

    const fileData = {
      name: 'test.mp4',
      type: 'video/mp4',
      size: video_file.size, 
      lastModified: Date.now() 
    };

    const file = new Blob([video_file], fileData);
    Object.defineProperty(fileInput, 'files', {
      value: [file],
      writable: false
    });

    options.set("VIDEO_MEDIA", fileInput.files[0]);
    options.set("VIDEO_TITLE", "Test Video");
    options.set("VIDEO_DESCRIPTION", "Lorem ipsum dolor sit amet, consecte.");
    const response = await post("video", options);
    expect(response.token).toBe("valid");
  })
  it('post comment', async () => { // (&COMMENT_PARENT_ID=?) // TODO
    const options = new Map();
    options.set("COMMENT_MESSAGE", "Amazing!");
    options.set("VS_VIDEO_ID", 1);
    options.set("VS_USER_ID", 1);

    const response = await post("comment", options);
    expect(response.token).toBe("valid");
  })
  it('post feedback', async () => { // TODO
    const options = new Map();
    options.set("VIDEO_FEEDBACK_TYPE", "positive");
    options.set("VS_VIDEO_ID", 1);
    options.set("VS_USER_ID", 1);

    const response = await post("feedback", options);
    expect(response.token).toBe("valid");
  })
  it('post follow', async () => { // TODO
    const options = new Map();
    options.set("FOLLOWING_SUBSCRIBER", 1);
    options.set("FOLLOWING_SUBSCRIBED", 1);

    const response = await post("follow", options);
    expect(response.token).toBe("valid");
  })
  it('signout user', async () => { // TODO
    const response = await post("signout", []);
    expect(response.token).toBe("unset");
  })
})

// PUT-API-Tests
describe('api-fetch-PUT', () => { // TODO
  // - medium=profile_username [MEDIUM]
  //   - HTTP_AUTHORIZATION=?&VS_USER_ID=?&USER_USERNAME=?
  // - medium=profile_password [MEDIUM]
  //   - HTTP_AUTHORIZATION=?&VS_USER_ID=?&USER_PASSWORD=?
  // - medium=profile_description [MEDIUM]
  //   - HTTP_AUTHORIZATION=?&VS_USER_ID=?&USER_PROFILEDESCRIPTION=?
  // - medium=profile_socials [MEDIUM]
  //   - HTTP_AUTHORIZATION=?&VS_USER_ID=?&VS_USER_SOCIAL=? (array of VS_USER_SOCIAL Objects)
  // - medium=profile_picture [MEDIUM]
  //   - HTTP_AUTHORIZATION=?&VS_USER_ID=?&USER_PROFILEPICTURE=?
  // - medium=video_post_title [MEDIUM]
  //   - HTTP_AUTHORIZATION=?&VS_VIDEO_ID=?&VIDEO_TITLE=?
  // - medium=video_post_description [MEDIUM]
  //   - HTTP_AUTHORIZATION=?&VS_VIDEO_ID=?&VIDEO_DESCRIPTION=?
  // - medium=video_post_hashtags [MEDIUM]
  //   - HTTP_AUTHORIZATION=?&VS_VIDEO_ID=?&VS_VIDEO_COMMENT=? (array of VS_VIDEO_COMMENT Objects)
  // - medium=comment_post_text [MEDIUM]
  //   - HTTP_AUTHORIZATION=?&COMMENT_ID=?&COMMENT_MESSAGE=?
})

// DELETE-API-Tests
describe('api-fetch-DELETE', () => { // TODO
  // - medium=all [MEDIUM]
  //   - HTTP_AUTHORIZATION=?&VS_USER_ID=?
  // - medium=account [MEDIUM]
  //   - HTTP_AUTHORIZATION=?&VS_USER_ID=?
  // - medium=video [MEDIUM]
  //   - HTTP_AUTHORIZATION=?&VS_VIDEO_ID=?
  // - medium=comment [MEDIUM]
  //   - HTTP_AUTHORIZATION=?&COMMENT_ID=?
})