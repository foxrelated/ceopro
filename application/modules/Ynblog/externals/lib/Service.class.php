<?php
/**
 * Responsible communicating with AddThis Analytics API
 *
 * @author James Cryer(j.r.cryer@gmail.com)
 */
class Service {

    /**
     * @var Request
     */
    protected $oRequest;

    /**
     * Constructor, requires request object
     * 
     * @param Request $oRequest
     */
    public function __construct(Request $oRequest) {
        $this->setRequest($oRequest);
    }

    /**
     * Returns content for the current request
     *
     * @return string
     */
    public function getData() {
        $oRequest = $this->getRequest();
        $request  = $oRequest->getRequest();
        return $this->sendRequest($request);
    }

    /**
     * Send request to AddThis Analytics API.
     *
     * @param string $request
     * @return string
     */
        public function sendRequest($request) {
        $response = @file_get_contents($request);
        return json_decode($response);
    }

    /**
     * Returns the current request object
     * 
     * @return Request
     */
    public function getRequest() {
        return $this->oRequest;
    }

    /**
     * Set the current request object
     * 
     * @param Request $oRequest
     */
    public function setRequest(Request $oRequest) {
        $this->oRequest = $oRequest;
    }
}