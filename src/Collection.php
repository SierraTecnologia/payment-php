<?php

namespace SierraTecnologia;

/**
 * Class Collection
 *
 * @property string $object
 * @property string $url
 * @property bool $has_more
 * @property mixed $data
 *
 * @package SierraTecnologia
 */
class Collection extends SierraTecnologiaObject implements \IteratorAggregate
{

    const OBJECT_NAME = "list";

    use ApiOperations\Request;

    protected $_requestParams = [];

    /**
     * @return string The base URL for the given class.
     */
    public static function baseUrl()
    {
        return SierraTecnologia::$apiBase;
    }

    /**
     * @param array|null $params
     *
     * @return void
     */
    public function setRequestParams(?array $params): void
    {
        $this->_requestParams = $params;
    }

    /**
     * @return SierraTecnologiaObject|array
     */
    public function all($params = null, $opts = null)
    {
        list($url, $params) = $this->extractPathAndUpdateParams($params);

        list($response, $opts) = $this->_request('get', $url, $params, $opts);
        $this->_requestParams = $params;
        return Util\Util::convertToSierraTecnologiaObject($response, $opts);
    }

    /**
     * @param array|null        $params
     * @param array|null|string $opts
     *
     * @return SierraTecnologiaObject|array
     */
    public function create(?array $params = null, $opts = null)
    {
        list($url, $params) = $this->extractPathAndUpdateParams($params);

        list($response, $opts) = $this->_request('post', $url, $params, $opts);
        $this->_requestParams = $params;
        return Util\Util::convertToSierraTecnologiaObject($response, $opts);
    }

    /**
     * @return \ArrayIterator An iterator that can be used to iterate across objects in the current page.
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->data);
    }

    /**
     * @param array|null $params
     *
     * @return (array|mixed|string)[]
     *
     * @psalm-return array{0: string, 1: array|mixed}
     */
    private function extractPathAndUpdateParams(?array $params): array
    {
        $url = parse_url($this->url);
        if (!isset($url['path'])) {
            throw new Error\Api("Could not parse list url into parts: $url");
        }

        if (isset($url['query'])) {
            // If the URL contains a query param, parse it out into $params so they
            // don't interact weirdly with each other.
            $query = [];
            parse_str($url['query'], $query);
            $params = array_merge($params ?: [], $query);
        }

        return [$url['path'], $params];
    }
}
