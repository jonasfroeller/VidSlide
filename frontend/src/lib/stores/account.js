import { writable } from 'svelte/store';

export const loginState = writable(false);

export const user = writable({});

export const user_stats = writable({}); // Videos, Views, Likes, Followers...

export const user_videos_liked = writable([]);

export const user_videos_disliked = writable([]);

export const user_comments_liked = writable([]);

export const user_comments_disliked = writable([]);

export const user_subscribed = writable([]);

export const user_subscribers = writable([]);

export const user_social = writable([]);
