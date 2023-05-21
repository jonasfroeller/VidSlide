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

// GET-Options: 
// - medium=user [MEDIUM]
//   - id=video [ID] // unsuffishient
//     - medium_id=? [ID++] // creator of video
//   - id=username [ID] 
//     - medium_id=? [ID++] // username of user
//   - id=? [ID] 
// - medium=video [MEDIUM] // gets videos and video info
//   - id=all [ID] // unsuffishient
//     - medium_id=? [ID++] // all videos of user
//   - id=title [ID]
//     - medium_id=? [ID++] // all videos with title including text
//   - id=tag [ID]
//     - medium_id=? [ID++] // all videos with tag including text
//   - id=random [ID]
//   - id=? [ID] 
// - medium=comments [MEDIUM]
//   - id=? (video id) [ID] // get comments of video
// - medium=tags [MEDIUM]
//   - id=? (video id) [ID] // get tags of video
// - medium=feedback [MEDIUM]
//   - id=? (video id) [ID] // get feedback of video
describe('api-fetch-GET', () => {
    it('get video with invalid id', async () => {
        const response = await get('video', '-1');
        expect(response.error).toContain("video not found");
    })
    it('get video with valid id', async () => {
        const video_id = 1;
        const response = await get('video', video_id);
        const video = JSON.parse(response['data'][0]);
        expect(video.length).toBeGreaterThan(0);
        expect(video[0]["VS_VIDEO_ID"]).toBe(video_id);
    })
})