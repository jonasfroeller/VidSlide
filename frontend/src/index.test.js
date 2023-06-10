import { describe, it, expect } from 'vitest';

const axios = require('axios');
const http = require('http');

let jwt = {};
let user = {
  data: {},
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
let accountExisted = false;

/**
 * @param {string | http.RequestOptions | import("url").URL} options
 */
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
 * @param {number | string} index (can be 'all' and 'random' too)
 * @param {number | string} specification
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
  formData.append('action', 'POST');
  formData.append('medium', 'auth');
  formData.append('username', username);
  formData.append('password', password);

  // eslint-disable-next-line no-useless-catch
  try {
    // @ts-ignore
    const response = await axios.post(url, formData, {
      body: formData
    });

    // @ts-ignore
    if (response.status >= 200 && response.status <= 299) {
      // @ts-ignore
			const json_response = await response.data;
			jwt = json_response['token'];
			accountExisted = json_response["response"] == "accountExisted" ? true : false;
			user.data = JSON.parse(json_response['data'][0])[0];
			user.socials = JSON.parse(json_response['data']?.socials) ?? [];
			user.subscribed = JSON.parse(json_response['data']?.subscribed) ?? [];
			user.subscribers = JSON.parse(json_response['data']?.subscribers) ?? [];
			user.user_videos_liked = JSON.parse(json_response['data']?.liked) ?? [];
			user.user_videos_disliked = JSON.parse(json_response['data']?.disliked) ?? [];
			user.user_comments_liked =
				JSON.parse(json_response['data']?.comments_liked) ?? [];
			user.user_comments_disliked =
				JSON.parse(json_response['data']?.comments_disliked) ?? [];
			user.user_stats.videos = JSON.parse(json_response['data']?.stats?.videos) ?? [];
			user.user_stats.likes = JSON.parse(json_response['data']?.stats?.likes) ?? [];
			user.user_stats.views = JSON.parse(json_response['data']?.stats?.views) ?? [];
			user.user_stats.shares = JSON.parse(json_response['data']?.stats?.shares) ?? [];

			return {
				accountExisted: accountExisted,
				token: jwt,
				user: user
			};
    } else {
      // @ts-ignore
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
async function post(action, options, method = 'POST') {
  const url = 'http://localhost:8196/db_api.php';
  const formData = new FormData();
  formData.append("action", method);
  formData.append("medium", action);
  formData.append('HTTP_AUTHORIZATION', `Bearer ${jwt}`);

  // @ts-ignore
  for (const [key, value] of options) {
    formData.append(key, value);
  }

  // eslint-disable-next-line no-useless-catch
  try {
    // @ts-ignore
    const response = await axios({
      method: method,
      url: url,
      data: formData
    });

    // @ts-ignore
    if (response.status >= 200 && response.status <= 299) {
      // @ts-ignore
      return await response.data;
    } else {
      // @ts-ignore
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
  // - medium=auth [MEDIUM] // insufficient
  //   - username=?&password=? [ID] // => auth token if password for user is valid or account doesn't exist (will be created)
  // - medium=signout [MEDIUM]  
  // - medium=video [MEDIUM] 
  //   - HTTP_AUTHORIZATION=?&VIDEO_MEDIA=?&VIDEO_TITLE=?&VIDEO_DESCRIPTION=?
  // - medium=comment [MEDIUM]
  //   - HTTP_AUTHORIZATION=?&COMMENT_MESSAGE=?&VS_VIDEO_ID=?(&COMMENT_PARENT_ID=?)
  // - medium=feedback [MEDIUM]
  //  - HTTP_AUTHORIZATION=?&VIDEO_FEEDBACK_TYPE=?&VS_VIDEO_ID=?
  // - medium=follow [MEDIUM]
  //  - HTTP_AUTHORIZATION=?&FOLLOWING_SUBSCRIBED=? 
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

    // @ts-ignore
    options.set("VIDEO_MEDIA", fileInput?.files[0]);
    options.set("VIDEO_TITLE", "Test Video");
    options.set("VIDEO_DESCRIPTION", "Lorem ipsum dolor sit amet, consecte.");
    const response = await post("video", options);
    expect(response.token).toBe("valid");
  })
  it('post comment', async () => { // (&COMMENT_PARENT_ID=?) // TODO
    const options = new Map();
    options.set("COMMENT_MESSAGE", "Amazing!");
    options.set("VS_VIDEO_ID", 1);

    const response = await post("comment", options);
    expect(response.token).toBe("valid");
  })
  it('post feedback', async () => { // TODO
    const options = new Map();
    options.set("VIDEO_FEEDBACK_TYPE", "positive");
    options.set("VS_VIDEO_ID", 1);

    const response = await post("feedback", options);
    expect(response.token).toBe("valid");
  })
  it('post follow', async () => { // TODO
    const options = new Map();
    options.set("FOLLOWING_SUBSCRIBED", 1);

    const response = await post("follow", options);
    expect(response.token).toBe("valid");
  })
})

// PUT-API-Tests
describe('api-fetch-PUT', () => { // TODO extend tests
  // - medium=profile_username [MEDIUM]
  //   - HTTP_AUTHORIZATION=?&USER_USERNAME=?
  // - medium=profile_password [MEDIUM]
  //   - HTTP_AUTHORIZATION=?&USER_PASSWORD=?
  // - medium=profile_description [MEDIUM]
  //   - HTTP_AUTHORIZATION=?&USER_PROFILEDESCRIPTION=?
  // - medium=profile_socials [MEDIUM]
  //   - HTTP_AUTHORIZATION=?&VS_USER_SOCIAL=? (socials object)
  // - medium=profile_picture [MEDIUM]
  //   - HTTP_AUTHORIZATION=?&USER_PROFILEPICTURE=?
  // - medium=video_post_title [MEDIUM]
  //   - HTTP_AUTHORIZATION=?&VS_VIDEO_ID=?&VIDEO_TITLE=?
  // - medium=video_post_description [MEDIUM]
  //   - HTTP_AUTHORIZATION=?&VS_VIDEO_ID=?&VIDEO_DESCRIPTION=?
  // - medium=video_post_hashtags [MEDIUM]
  //   - HTTP_AUTHORIZATION=?&VS_VIDEO_ID=?&VS_VIDEO_HASHTAG=? (hashtag object)
  // - medium=comment_post_text [MEDIUM]
  //   - HTTP_AUTHORIZATION=?&COMMENT_ID=?&COMMENT_MESSAGE=?
  it('update user profile username', async () => {
    const options = new Map();
    options.set("USER_USERNAME", "Peter Griffin"); // was Jonesisfroellerix

    const response = await post("profile_username", options, "PUT");
    expect(response.token).toBe("valid");
  })
  it('update user profile password', async () => {
    const options = new Map();
    options.set("USER_PASSWORD", "pass123"); // was Password2$

    const response = await post("profile_password", options, "PUT");
    expect(response.token).toBe("valid");
  })
  it('update user profile description', async () => {
    const options = new Map();
    options.set("USER_PROFILEDESCRIPTION", "I am a description."); // was empty

    const response = await post("profile_description", options, "PUT");
    expect(response.token).toBe("valid");
  })
  it('update user profile socials', async () => {
    const options = new Map();
    options.set("VS_USER_SOCIAL", {
      SOCIAL_PLATFORM: "youtube",
      SOCIAL_URL: "https://www.youtube.com/channel/UCX6OQ3DkcsbYNE6H8uQQuVA"
    }); // was empty

    const response = await post("profile_socials", options, "PUT");
    expect(response.token).toBe("valid");
  })
  it('update user profile avatar', async () => {
    const options = new Map();
    options.set("USER_PROFILEPICTURE", "https://picsum.photos/1920"); // was empty

    const response = await post("profile_picture", options, "PUT");
    expect(response.token).toBe("valid");
  })
  it('update user post title', async () => { 
    const options = new Map();
    options.set("VS_VIDEO_ID", 1);
    options.set("VIDEO_TITLE", "new vid :)"); // TODO was ??? 

    const response = await post("video_post_title", options, "PUT");
    expect(response.token).toBe("valid");
  })
  it('update user post description', async () => {
    const options = new Map();
    options.set("VS_VIDEO_ID", 1);
    options.set("VIDEO_DESCRIPTION", "some description"); // was empty

    const response = await post("video_post_description", options, "PUT");
    expect(response.token).toBe("valid");
  })
  it('update user post hashtags', async () => {
    const options = new Map();
    options.set("VS_VIDEO_ID", 1);
    options.set("VS_VIDEO_HASHTAG", {
      HASHTAG_NAME: "test"
    }); // was empty

    const response = await post("video_post_hashtags", options, "PUT");
    expect(response.token).toBe("valid");
  })
  it('update user comment text', async () => {
    const options = new Map();
    options.set("COMMENT_ID", 1);
    options.set("COMMENT_MESSAGE", "some comment (:"); // was empty

    const response = await post("comment_post_text", options, "PUT");
    expect(response.token).toBe("valid");
  })

  // back to original
  it('update user profile username again', async () => {
    const options = new Map();
    options.set("USER_USERNAME", "Jonesisfroellerix"); // was Peter Griffin

    const response = await post("profile_username", options, "PUT");
    expect(response.token).toBe("valid");
  })
  it('update user profile password', async () => {
    const options = new Map();
    options.set("USER_PASSWORD", "Password2$"); // was pass123

    const response = await post("profile_password", options, "PUT");
    expect(response.token).toBe("valid");
  })
})

// DELETE-API-Tests
describe('api-fetch-DELETE', () => { // TODO
  // - medium=account [MEDIUM]
  //   - HTTP_AUTHORIZATION=?
  // - medium=video [MEDIUM]
  //   - HTTP_AUTHORIZATION=?&VS_VIDEO_ID=?
  // - medium=comment [MEDIUM]
  //   - HTTP_AUTHORIZATION=?&COMMENT_ID=?
  it('update user profile password', async () => {
    const response = await post("account", [], "DELETE");
    expect(response.token).toBe("valid");
  })
  it('update user profile password', async () => {
    const options = new Map();
    options.set("VS_VIDEO_ID", 1); // TODO

    const response = await post("account", options, "DELETE");
    expect(response.token).toBe("valid");
  })
  it('update user profile password', async () => {
    const options = new Map();
    options.set("COMMENT_ID", 1); // TODO

    const response = await post("video", options, "DELETE");
    expect(response.token).toBe("valid");
  })
})

describe('finally', () => {
  it('signout user', async () => {
    const response = await post("signout", []);
    expect(response.token).toBe("unset");
  })
})