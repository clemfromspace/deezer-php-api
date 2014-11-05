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
     * Search for an item.
     * http://developers.deezer.com/api/search
     *
     * @param string $query The term to search for.
     * @param string|array $type The type of item to search for; "album", "artist", or "track".
     * @param array|object $options Optional. Options for the search.
     * - string stric Optional. Disable the fuzzy mode (on/off)
     * - string order Optional. (RANKING, TRACK_ASC, TRACK_DESC, ARTIST_ASC, ARTIST_DESC, ALBUM_ASC, ALBUM_DESC, RATING_ASC, RATING_DESC, DURATION_ASC, DURATION_DESC)
     *
     * @return array
     */
    public function search($query, $type, $options = array())
    {
        $defaults = array(
            'strict' => 'off',
            'order' => 'RANKING',
        );

        $type = implode(',', (array) $type);

        $options = array_merge($defaults, (array) $options);
        $options = array_filter($options);
        $options =  array_merge($options, array(
            'query' => $query,
        ));

        $headers = array();
        if (isset($options['market']) && $options['market'] == 'from_token') {
            $headers['Authorization'] = 'Bearer ' . $this->accessToken;
        }

        $response = $this->request->api('GET', '/search', $options, $headers);

        return $response['body'];
    }
}