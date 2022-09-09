<?php 

class BetP2P_Odds {

    private $apikey;
    private $sports;
    private $regions;
    private $method;

    /**
     * Get Odds API response
     *
     * @param string $api_key
     * @param string $sports
     * @param [string/array] $regions
     * @param string $method
     */
    public function __construct( $apikey ) {

        $this->apikey = $apikey;          
        
    }

    public function get( $sports, $regions ) {

        $json_feed_url = "https://api.the-odds-api.com/v4/sports/" . $sports . "/odds/?regions=" . $regions . "&markets=h2h&oddsFormat=american&apiKey=" . $this->apikey;

        $json_feed = wp_remote_get( $json_feed_url );

        // var_dump($json_feed);

        // return $json_feed;

        // convert to object
        $response = json_decode( $json_feed['body'] );

    //    var_dump( $response );

        return $response;
    }

    public function put() {
        echo 'Nada por hacer';
    }
}