<?php

if (!function_exists('is_favorite_page')) {
    /**
     * Returun true case current url is favorite page.
     */
    function is_favorite_page()
    {
        return get_query_var('favorite_page', false);
    }
}

if (!function_exists('get_favorite_page_url')) {
    /**
     * Return url of favorite page.
     */
    function get_favorite_page_url()
    {
        return home_url().'/favorites';
    }
}

if (!function_exists('favorite_button')) {
    function favorite_button($postID)
    {
        return [
          'class' => 'button-favorite__favorite '.(verify_is_a_favorite_page($postID) ? 'button-favorite__favorite--favorited' : ''),
          'data-id' => $postID,
          'is_favorited' => verify_is_a_favorite_page($postID),
        ];
    }
}

if (!function_exists('verify_is_a_favorite_page')) {
    /**
     * Return tru case $postID is a favorite page.
     */
    function verify_is_a_favorite_page($postID, $userID = false, $useCachedValue = true)
    {
        return AEMIFavoritePages::verify_is_a_favorite_page($postID, $userID, $useCachedValue);
    }
}

if (!function_exists('get_favorite_pages')) {
    /**
     * Return list of IDs favorites pages.
     */
    function get_favorite_pages($userID = false, $useCache = true)
    {
        return AEMIFavoritePages::get($userID, $useCache);
    }
}

if (!function_exists('set_post_favorite_page')) {
    /**
     * Set new favorite page in current user.
     */
    function set_post_favorite_page($postID, $userID = false)
    {
        return AEMIFavoritePages::set_post_favorite_page($postID, $userID);
    }
}

if (!function_exists('remove_post_favorite_page')) {
    /**
     * Remove from favorite page in current user.
     */
    function remove_post_favorite_page($postID, $userID = false)
    {
        return AEMIFavoritePages::remove_post_favorite_page($postID, $userID);
    }
}
