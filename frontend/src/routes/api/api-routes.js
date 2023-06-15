export const Route = Object.freeze({
	REQUEST_METHOD: {
		GET: {
			value: "GET",
			medium: {
				user: {					
					root: "user",
					id: {
						video: {
							branch: "video",
							medium_id: function (/** @type {number} */ user_id) { return user_id; }
						},
						username: {
							branch: "username",
							medium_id: function (/** @type {string} */ username) { return username; }
						},
						id: function (/** @type {number} */ id) { return id; }
					}
				},
				video: {
					root: "video",
					id: {
						all: {
							branch: "all",
							medium_id: function (/** @type {number} */ user_id) { return user_id; }
						},
						title: {
							branch: "title",
							medium_id: function (/** @type {string} */ title_including) { return title_including; }
						},
						tag: {
							branch: "tag",
							medium_id: function (/** @type {string} */ tag_including) { return tag_including; }
						},
						username: {
							branch: "username",
							medium_id: function (/** @type {string} */ username_including) { return username_including; }
						},
						random: "random",
						id: function (/** @type {number} */ id) { return id; }
					},
				},
				comments: {
					root: "comments",
					id: function (/** @type {number} */ id) { return id; }
				},
				tags: {
					root: "tags",
					id: function (/** @type {number} */ id) { return id; }
				},
				feedback: {
					root: "feedback",
					id: function (/** @type {number} */ id) { return id; }
				}
			}
		},
		POST: {
			value: "POST",
			medium: {
				auth: {
					root: "auth",
					params: {
						username: function (/** @type {string} */ username) { return username; },
						password: function (/** @type {string} */ password) { return password; }
					}
				},
				signout: "signout",
				video: {
					root: "video",
					params: {
						VIDEO_MEDIA: function (/** @type {FileList} */ video_media) { return video_media[video_media.length-1]; },
						VIDEO_TITLE: function (/** @type {string} */ video_title) { return video_title; },
						VIDEO_DESCRIPTION: function (/** @type {string} */ video_description) { return video_description; },
						VIDEO_TAGS: function (/** @type {string[]} */ video_tags) { return video_tags; },
					}
				},
				comment: {
					root: "comment",
					params: {
						COMMENT_MESSAGE: function (/** @type {string} */ comment_message) { return comment_message; },
						VS_VIDEO_ID: function (/** @type {number} */ vs_video_id) { return vs_video_id; },
						COMMENT_PARENT_ID: function (/** @type {number} */ comment_parent_id) { return comment_parent_id; }
					},
				},
				feedback: {
					root: "feedback",
					medium_id: function (/** @type {string} */ type) { return type; }, // type=comment|video
					params: {
						FEEDBACK_TYPE: function (/** @type {string} */ feedback_type) {
							if (feedback_type === "positive" || feedback_type === "negative") {
							  return feedback_type;
							} else {
							  throw new Error("Only 'positive' or 'negative' is allowed!");
							}
						}, // &VS_VIDEO_ID=?||VS_COMMENT_ID=?
						VS_VIDEO_ID: function (/** @type {number} */ vs_video_id) { return vs_video_id; },
						VS_COMMENT_ID: function (/** @type {number} */ vs_comment_id) { return vs_comment_id; }
					}
				},
				follow: {
					root: "follow",
					params: {
						FOLLOWING_SUBSCRIBED: function (/** @type {number} */ following_subscribed) { return following_subscribed; },
					}
				}
			},
		}
	} 
})