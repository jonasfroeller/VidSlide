import { describe, it, expect } from 'vitest';
const http = require('http');

// API-Functions (with http because fetch (browser-function) is not NodeJS compatible)
async function get(type, index, specification = '') {
  const options = {
    hostname: 'localhost',
    port: 8196,
    path: `/db_api.php?medium=${type}&id=${index}${specification !== '' ? '&medium_id=' + specification : ''}`,
    method: 'GET'
  };

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

// GET-API-Tests
describe('api-fetch-GET', () => {
  // - medium=video [MEDIUM] // gets videos and video info
  //   - id=all [ID] // insufficient
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
  // - medium=tag [MEDIUM]
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