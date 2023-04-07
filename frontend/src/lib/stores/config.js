import { writable } from 'svelte/store';

// Attributes: selectedLAN | theme
export const config = writable({});

export const themeState = writable();

export const langState = writable();

export const loginState = writable(false);
