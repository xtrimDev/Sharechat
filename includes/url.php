<?php

/** This is the url configuration file. */
class url 
{
    /** Finding currently running server is on http or https. */
    private function http(): string
    {
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
            $http = "https";
        else
            $http = "http";

        return $http;
    }

    /** Getting server host. */
    private function host()
    {
        if ($_SERVER['HTTP_HOST'])
            $host =  $_SERVER['HTTP_HOST'];
        else
            $host =  $_SERVER['SERVER_NAME'];

        return $host;
    }

    /** Getting the server url. */
    private function server(): string
    {
        return strtolower($this->http() . "://" . $this->host());
    }

    /** Getting the current url. */
    public function current(): string
    {
        return $this->server() . strtolower($_SERVER['REQUEST_URI']);
    }

    /** Getting the current page url. */
    public function current_page(): string
    {
        return $this->server() . strtolower($_SERVER['PHP_SELF']);
    }
    
    public function clean_url(STRING $url): string
    {
        if (strpos($url, '?') > 1) {
            $url = strstr($url, '?', true);
        } 

        return strtolower($url);
    }

    /** Get the previous page url */
    public  function previous(): string
    {
        return $_SERVER['HTTP_REFERER'];
    }

    /** Getting the home url. */
    public function home(): string
    {
        return dirname($this->current_page());
    }
}