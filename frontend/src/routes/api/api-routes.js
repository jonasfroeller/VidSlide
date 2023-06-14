export const Route = Object.freeze({
	REQUEST_METHOD: {
		GET: {
			value: Symbol("GET"),
			medium: {
				user: {
					root: Symbol("user"),
					id: {
						video: {
							branch: Symbol("video"),
							medium_id: function (/** @type {number} */ video_id) { return video_id; }
						},
						username: {
							branch: Symbol("username"),
							medium_id: function (/** @type {string} */ username) { return username; }
						},
						id: function (/** @type {string} */ id) { return id; }
					}
				},
				video: {
					root: Symbol("video"),
					id: {
						all: {
							branch: Symbol("all"),
							medium_id: function (/** @type {number} */ user_id) { return user_id; }
						},
						title: {
							branch: Symbol("title"),
							medium_id: function (/** @type {string} */ title_including) { return title_including; }
						},
						tag: {
							branch: Symbol("tag"),
							medium_id: function (/** @type {string} */ tag_including) { return tag_including; }
						},
						username: {
							branch: Symbol("username"),
							medium_id: function (/** @type {string} */ username_including) { return username_including; }
						},
						random: Symbol("random"),
						id: function (/** @type {string} */ id) { return id; }
					},
				},
				comments: {
					root: Symbol("comments"),
					id: function (/** @type {number} */ id) { return id; }
				},
				tags: {
					root: Symbol("tags"),
					id: function (/** @type {number} */ id) { return id; }
				},
				feedback: {
					root: Symbol("feedback"),
					id: function (/** @type {number} */ id) { return id; }
				}
			}
		},
		POST: {
			value: Symbol("POST"),
			medium: {
				auth: {
					root: Symbol("auth"),
					params: {
						username: function (/** @type {string} */ username) { return username; },
						password: function (/** @type {string} */ password) { return password; }
					}
				},
				signout: Symbol("signout"),
				video: {
					root: Symbol("video"),
					params: {
						VIDEO_MEDIA: function (/** @type {FileList} */ video_media) { return video_media; },
						VIDEO_TITLE: function (/** @type {string} */ video_title) { return video_title; },
						VIDEO_DESCRIPTION: function (/** @type {string} */ video_description) { return video_description; }
					}
				},
				comment: {
					root: Symbol("comment"),
					params: {
						COMMENT_MESSAGE: function (/** @type {string} */ comment_message) { return comment_message; },
						VS_VIDEO_ID: function (/** @type {number} */ vs_video_id) { return vs_video_id; },
						COMMENT_PARENT_ID: function (/** @type {number} */ comment_parent_id) { return comment_parent_id; }
					},
				},
				feedback: {
					root: Symbol("feedback"),
					params: {
						FEEDBACK_TYPE: function (/** @type {string} */ feedback_type) {
							if (feedback_type === "positive" || feedback_type === "negative") {
							  return feedback_type;
							} else {
							  throw new Error("Only 'positive' or 'negative' is allowed!");
							}
						},
						VS_VIDEO_ID: function (/** @type {number} */ vs_video_id) { return vs_video_id; },
						VS_COMMENT_ID: function (/** @type {number} */ vs_comment_id) { return vs_comment_id; }
					}
				},
				follow: {
					root: Symbol("follow"),
					params: {
						FOLLOWING_SUBSCRIBED: function (/** @type {string} */ following_subscribed) { return following_subscribed; },
					}
				}
			},
		}
	} 
})