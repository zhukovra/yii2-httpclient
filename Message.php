<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\httpclient;

use yii\base\ErrorHandler;
use yii\base\InvalidConfigException;
use yii\base\Object;
use yii\web\Cookie;
use yii\web\CookieCollection;
use yii\web\HeaderCollection;
use Yii;

/**
 * Message represents a base HTTP message.
 *
 * @property HeaderCollection|array $headers message headers list.
 * @property CookieCollection|Cookie[]|array $cookies message cookies list.
 * @property string $content message raw content.
 * @property array $data message content data.
 * @property string $format message content format.
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 2.0
 */
class Message extends Object
{
    /**
     * @var Client owner client instance.
     */
    public $client;

    /**
     * @var HeaderCollection headers.
     */
    private $_headers;
    /**
     * @var CookieCollection cookies.
     */
    private $_cookies;
    /**
     * @var string|null raw content
     */
    private $_content;
    /**
     * @var array content data
     */
    private $_data;
    /**
     * @var string content format name
     */
    private $_format;


    /**
     * Sets the HTTP headers associated with HTTP message.
     * @param array|HeaderCollection $headers headers collection or headers list in format: [headerName => headerValue]
     * @return $this self reference.
     */
    public function setHeaders($headers)
    {
        $this->_headers = $headers;
        return $this;
    }

    /**
     * Returns the header collection.
     * The header collection contains the HTTP headers associated with HTTP message.
     * @return HeaderCollection the header collection
     */
    public function getHeaders()
    {
        if (!is_object($this->_headers)) {
            $headerCollection = new HeaderCollection();
            if (is_array($this->_headers)) {
                foreach ($this->_headers as $name => $value) {
                    $headerCollection->set($name, $value);
                }
            }
            $this->_headers = $headerCollection;
        }
        return $this->_headers;
    }

    /**
     * Sets the cookies associated with HTTP message.
     * @param array|CookieCollection $cookies cookie collection or cookies list.
     * @return $this self reference.
     */
    public function setCookies($cookies)
    {
        $this->_cookies = $cookies;
        return $this;
    }

    /**
     * Returns the cookie collection.
     * The cookie collection contains the cookies associated with HTTP message.
     * @return CookieCollection|Cookie[] the cookie collection.
     */
    public function getCookies()
    {
        if (!is_object($this->_cookies)) {
            $cookieCollection = new CookieCollection();
            if (is_array($this->_cookies)) {
                foreach ($this->_cookies as $cookie) {
                    if (!is_object($cookie)) {
                        $cookie = new Cookie($cookie);
                    }
                    $cookieCollection->add($cookie);
                }
            }
            $this->_cookies = $cookieCollection;
        }
        return $this->_cookies;
    }

    /**
     * Adds HTTP headers to the headers collection.
     * @param array $headers headers list in format: [headerName => headerValue]
     * @return $this self reference.
     */
    public function addHeaders(array $headers)
    {
        $headerCollection = $this->getHeaders();
        foreach ($headers as $name => $value) {
            $headerCollection->add($name, $value);
        }
        return $this;
    }

    /**
     * Sets the HTTP message raw content.
     * @param string $content raw content.
     * @return $this self reference.
     */
    public function setContent($content)
    {
        $this->_content = $content;
        return $this;
    }

    /**
     * Returns HTTP message raw content.
     * @return string raw body.
     */
    public function getContent()
    {
        return $this->_content;
    }

    /**
     * Sets the data fields, which composes message content.
     * @param array $data content data fields.
     * @return $this self reference.
     */
    public function setData($data)
    {
        $this->_data = $data;
        return $this;
    }

    /**
     * Returns the data fields, parsed from raw content.
     * @return array|null content data fields.
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * Sets body format.
     * @param string $format body format name.
     * @return $this self reference.
     */
    public function setFormat($format)
    {
        $this->_format = $format;
        return $this;
    }

    /**
     * Returns body format.
     * @return string body format name.
     */
    public function getFormat()
    {
        if ($this->_format === null) {
            $this->_format = $this->defaultFormat();
        }
        return $this->_format;
    }

    /**
     * Returns default format name.
     * @return string default format name.
     */
    protected function defaultFormat()
    {
        return Client::FORMAT_URLENCODED;
    }

    /**
     * Returns string representation of this HTTP message.
     * @return string the string representation of this HTTP message.
     */
    public function toString()
    {
        $headerParts = [];
        foreach ($this->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                $headerParts[] = "$name : $value";
            }
        }
        return implode("\n", $headerParts) . "\n\n" . $this->getContent();
    }

    /**
     * PHP magic method that returns the string representation of this object.
     * @return string the string representation of this object.
     */
    public function __toString()
    {
        // __toString cannot throw exception
        // use trigger_error to bypass this limitation
        try {
            return $this->toString();
        } catch (\Exception $e) {
            ErrorHandler::convertExceptionToError($e);
            return '';
        }
    }
}