import { writable, derived } from 'svelte/store';

export const searchedVideos = writable([
    {
        feedback: [
            {
                VIDEO_FEEDBACK_ID: 0,
                VIDEO_FEEDBACK_TYPE: "negative",
                VS_USER_ID: 0,
                VS_VIDEO_ID: 0
            }
        ],
        user: {
            USER_DATETIMECREATED: new Date(),
            USER_LASTUPDATE: new Date(),
            USER_PASSWORD: "password",
            USER_USERNAME: "username",
            USER_PROFILEDESCRIPTION: "description",
            USER_PROFILEPICTURE: "picture",
            VS_USER_ID: 0
        },
        video: {
            VIDEO_DATETIMEPOSTED: new Date(),
            VIDEO_DESCRIPTION: "description",
            VIDEO_LASTUPDATE: new Date(),
            VIDEO_LOCATION: "location",
            VIDEO_SHARES: 0,
            VIDEO_SIZE: 0,
            VIDEO_TITLE: "title",
            VIDEO_VIEWS: 0,
            VS_USER_ID: 0,
            VS_VIDEO_ID: 0
        }
    }
]);

export const filter = writable("likes")

export const filteredVideos = derived(
	[filter, searchedVideos], 
    ([$filter, $searchedVideos]) => {
        if ($filter === "date") {
           return $searchedVideos.slice().sort((a, b) => new Date(b.video.VIDEO_DATETIMEPOSTED).getTime() - new Date(a.video.VIDEO_DATETIMEPOSTED).getTime());
        } else if ($filter === "views") {
           return $searchedVideos.slice().sort((a, b) => b.video.VIDEO_VIEWS - a.video.VIDEO_VIEWS);
        } else if ($filter === "likes") {
            return $searchedVideos.slice().sort((a, b) =>
                b?.feedback?.filter(
                (feedback) => feedback?.VIDEO_FEEDBACK_TYPE === "positive"
                )?.length || 0 -
                a?.feedback?.filter(
                (feedback) => feedback?.VIDEO_FEEDBACK_TYPE === "positive"
                )?.length || 0
            );
        } else if ($filter === "dislikes") {
            return $searchedVideos.slice().sort((a, b) =>
                b?.feedback?.filter(
                (feedback) => feedback?.VIDEO_FEEDBACK_TYPE === "negative"
                )?.length || 0 -
                a?.feedback?.filter(
                (feedback) => feedback?.VIDEO_FEEDBACK_TYPE === "negative"
                )?.length || 0
            );
        } else {
            return $searchedVideos;
        }
    }
);