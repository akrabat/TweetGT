<?php

/**
 * Simple extension of Zend_Service_Twitter to allow status updates with
 * position (latitude,longitude)
 */
class App_Service_Twitter extends Zend_Service_Twitter
{
    /**
     * Update user's current status including his/her position
     *
     * @param  string $status
     * @param  string $latitude
     * @param  string $longitude
     * @param  int $in_reply_to_status_id
     * @return Zend_Rest_Client_Result
     * @throws Zend_Http_Client_Exception if HTTP request fails or times out
     * @throws Zend_Service_Twitter_Exception if message is too short or too long
     */
    public function statusUpdateWithPosition($status, $latitude, $longitude, $inReplyToStatusId = null)
    {
        $this->_init();
        $path = '/statuses/update.xml';
        $len = iconv_strlen(htmlspecialchars($status, ENT_QUOTES, 'UTF-8'), 'UTF-8');
        if ($len > self::STATUS_MAX_CHARACTERS) {
            include_once 'Zend/Service/Twitter/Exception.php';
            throw new Zend_Service_Twitter_Exception('Status must be no more than ' . self::STATUS_MAX_CHARACTERS . ' characters in length');
        } elseif (0 == $len) {
            include_once 'Zend/Service/Twitter/Exception.php';
            throw new Zend_Service_Twitter_Exception('Status must contain at least one character');
        }
        $data = array('status' => $status);
        $data['lat'] = $latitude;
        $data['long'] = $longitude;
        if (is_numeric($inReplyToStatusId) && !empty($inReplyToStatusId)) {
            $data['in_reply_to_status_id'] = $inReplyToStatusId;
        }
        
        $response = $this->_post($path, $data);
        return new Zend_Rest_Client_Result($response->getBody());
    }
}