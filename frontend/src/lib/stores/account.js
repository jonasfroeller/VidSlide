import { writable, derived } from 'svelte/store';

export const jwt = writable(null);

export const loginState = writable(false);

export const user = writable({
	VS_USER_ID: null,
	USER_USERNAME: null,
	USER_PROFILEPICTURE: null,
	USER_PROFILEDESCRIPTION: null,
	USER_DATETIMECREATED: null,
	USER_LASTUPDATE: null,
	user_stats: null,
	videos_liked: null,
	videos_disliked: null,
	comments_liked: null,
	comments_disliked: null,
	subscribed: null,
	subscribers: null,
	user_social: null
});

export const user_stats = derived(user, ($user) => $user.user_stats); // Videos, Views, Likes, Followers...

export const user_videos_liked = derived(user, ($user) => $user.videos_liked);

export const user_videos_disliked = derived(user, ($user) => $user.videos_disliked);

export const user_comments_liked = derived(user, ($user) => $user.comments_liked);

export const user_comments_disliked = derived(user, ($user) => $user.comments_disliked);

export const user_subscribed = derived(user, ($user) => $user.subscribed);

export const user_subscribers = derived(user, ($user) => $user.subscribers);

export const user_social = derived(user, ($user) => $user.user_social);
