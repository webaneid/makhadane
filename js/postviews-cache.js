jQuery(document).ready(function ($) {
    if (sessionStorage.getItem("viewed_" + postViewsCache.post_id) === null) {
        $.post(postViewsCache.admin_ajax_url, {
            action: 'postviews',
            postviews_id: postViewsCache.post_id
        }, function (response) {
            if (response.success) {
                sessionStorage.setItem("viewed_" + postViewsCache.post_id, "1");
            }
        });
    }
});
