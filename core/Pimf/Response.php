<?php
/**
 * Pimf
 *
 * @copyright Copyright (c)  Gjero Krsteski (http://krsteski.de)
 * @license   http://opensource.org/licenses/MIT MIT
 */
namespace Pimf;

use \Pimf\Util\Header, Pimf\Util\Json as UtilJson;

/**
 * Provides a simple interface around the HTTP an HTTPCache-friendly response generating.
 * Use this class to build and the current HTTP response before it is returned to the client.
 *
 * @package Pimf
 * @author  Gjero Krsteski <gjero@krsteski.de>
 */
class Response
{
    /**
     * The request method send by the client-browser.
     *
     * @var string
     */
    protected $requestMethod;

    /**
     * If the response attempts to send any cached headers.
     *
     * @var bool
     */
    protected static $cached = false;

    /**
     * Type of the data will be send to the client-browser.
     *
     * @var string
     */
    protected static $typed;

    /**
     * @param string $requestMethod
     *
     * @throws \RuntimeException
     */
    public function __construct($requestMethod)
    {
        $this->requestMethod = '' . strtoupper($requestMethod);
    }

    public function asJSON()
    {
        self::$typed = __FUNCTION__;
        Header::asJSON();

        return $this;
    }

    public function asHTML()
    {
        self::$typed = __FUNCTION__;
        Header::asTextHTML();

        return $this;
    }

    public function asPDF()
    {
        self::$typed = __FUNCTION__;
        Header::asPDF();

        return $this;
    }

    public function asCSV()
    {
        self::$typed = __FUNCTION__;
        Header::asCSV();

        return $this;
    }

    public function asTEXT()
    {
        self::$typed = __FUNCTION__;
        Header::asTextPlain();

        return $this;
    }

    public function asZIP()
    {
        self::$typed = __FUNCTION__;
        Header::asZIP();

        return $this;
    }

    public function asXZIP()
    {
        self::$typed = __FUNCTION__;
        Header::asXZIP();

        return $this;
    }

    public function asMSWord()
    {
        self::$typed = __FUNCTION__;
        Header::asMSWord();

        return $this;
    }

    /**
     * Sends a download dialog to the browser.
     *
     * @param string  $stream Can be a file-path or a string.
     * @param string  $name   Name of the stream/file should be shown.
     * @param boolean $exit   Optional for testing
     */
    public function sendStream($stream, $name, $exit = true)
    {
        Header::sendDownloadDialog($stream, $name, $exit);
    }

    /**
     * @param mixed $data
     * @param bool  $exit
     */
    public function send($data, $exit = true)
    {
        $body = $data;

        if (self::$typed === 'asJSON') {
            $body = UtilJson::encode($data);
        } elseif ($data instanceof \Pimf\View) {
            $body = $data->render();
        }

        echo '' . $body;
        if ($exit === true) {
            exit(0);
        }
    }

    /**
     * If instead you have a page that has personalization on it
     * (say, for example, the splash page contains local news as well),
     * you can set a copy to be cached only by the browser.
     *
     * @param int $seconds Interval in seconds
     *
     * @return $this
     */
    public function cacheBrowser($seconds)
    {
        $this->preventMultipleCaching();
        self::$cached = true;
        Header::cacheBrowser($seconds);

        return $this;
    }

    /**
     * If you want to try as hard as possible to keep a page from being cached anywhere.
     *
     * @return $this
     */
    public function cacheNone()
    {
        $this->preventMultipleCaching();
        self::$cached = true;
        Header::cacheNone();

        return $this;
    }

    /**
     * If you want to allow a page to be cached by shared proxies for one minute.
     *
     * @param int $seconds Interval in seconds
     *
     * @return $this
     */
    public function cacheNoValidate($seconds = 60)
    {
        $this->preventMultipleCaching();
        self::$cached = true;
        Header::cacheNoValidate($seconds);

        return $this;
    }

    /**
     * Handles setting pages that are always to be revalidated for freshness by any cache.
     *
     * @param int $lastModified Timestamp in seconds
     *
     * @return $this
     */
    public function exitIfNotModifiedSince($lastModified)
    {
        $this->preventMultipleCaching();
        self::$cached = true;
        Header::exitIfNotModifiedSince($lastModified);

        return $this;
    }

    /**
     * @throws \RuntimeException
     */
    private function preventMultipleCaching()
    {
        if ($this->requestMethod !== 'GET') {
            throw new \RuntimeException('HTTP cache headers can only take effect if request was sent via GET method!');
        }

        if (self::$cached === true) {
            throw new \RuntimeException('only one HTTP cache-control can be sent!');
        }
    }
}
