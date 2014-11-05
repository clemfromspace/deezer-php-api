<?php
namespace DeezerAPI;

class DeezerAPI
{
	private $accessToken = '';
    private $request = null;

    /**
     * Constructor
     * Set up Request object.
     *
     * @param Request $request Optional. The Request object to use.
     *
     * @return void
     */
    public function __construct($request = null)
    {
        if (is_null($request)) {
            $request = new Request();
        }

        $this->request = $request;
    }


    /**
     * Create a new playlist for the current user.
     * Requires a valid access token.
     * http://developers.deezer.com/api/user/playlists
     *
     * @param array|object $data Data for the new playlist.
     * - title string Required. Name of the playlist.
     *
     * @return object
     */
    public function createUserPlaylist($data)
    {
        $defaults = array(
            'title' =>  '',
        );

        $data = array_merge($defaults, (array) $data);
        $data = json_encode($data);

        $response = $this->request->api('POST', '/user/me/playlists', $data, array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Content-Type' => 'application/json'
        ));

        return $response['body'];
    }

    /**
     * Search for an item.
     * http://developers.deezer.com/api/search
     *
     * @param string $query The term to search for.
     * @param string|array $type The type of item to search for; "album", "artist", or "track".
     * @param array|object $options Optional. Options for the search.
     * - string strict Optional. Disable the fuzzy mode (on/off)
     * - string order Optional. (RANKING, TRACK_ASC, TRACK_DESC, ARTIST_ASC, ARTIST_DESC, ALBUM_ASC, ALBUM_DESC, RATING_ASC, RATING_DESC, DURATION_ASC, DURATION_DESC)
     *
     * @return array
     */
    public function search($query, $type = false, $options = array())
    {
        $defaults = array(
            'strict' => 'off',
            'order' => 'RANKING',
        );

        $type = implode(',', (array) $type);

        $options = array_merge($defaults, (array) $options);
        $options = array_filter($options);
        $options =  array_merge($options, array(
            'q' => $query,
        ));

        $response = $this->request->api('GET', '/search' . $type? $type . '/' : '', $options, $headers);

        return $response['body'];
    }
}