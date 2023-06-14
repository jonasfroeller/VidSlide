import { writable, derived } from 'svelte/store';

export const jwt = writable(null);

export const loginState = writable(false);

export const user = writable({
	data: {
		VS_USER_ID: null,
		USER_USERNAME: null,
		USER_PROFILEPICTURE: null,
		USER_PROFILEDESCRIPTION: null,
		USER_DATETIMECREATED: null,
		USER_LASTUPDATE: null,
	},
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
});

export const user_stats = derived(user, ($user) => $user?.user_stats);

export const user_videos_liked = derived(user, ($user) => $user?.user_videos_liked);

export const user_videos_disliked = derived(user, ($user) => $user?.user_videos_disliked);

export const user_comments_liked = derived(user, ($user) => $user?.user_comments_liked);

export const user_comments_disliked = derived(user, ($user) => $user?.user_comments_disliked);

export const user_subscribed = derived(user, ($user) => $user?.subscribed);

export const user_subscribers = derived(user, ($user) => $user?.subscribers);

export const user_social = derived(user, ($user) => $user?.socials);
