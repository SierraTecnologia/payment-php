<?php

namespace SierraTecnologia;

/**
 * Class AlipayAccount
 *
 * @package SierraTecnologia
 *
 * @deprecated Alipay accounts are deprecated. Please use the sources API instead.
 * @link       https://sierratecnologia.com.br/docs/sources/alipay
 */
class AlipayAccount extends ApiResource
{

    const OBJECT_NAME = "alipay_account";

    use ApiOperations\Delete;
    use ApiOperations\Update;

    /**
     * @return string The instance URL for this resource. It needs to be special
     *    cased because it doesn't fit into the standard resource pattern.
     */
    public function instanceUrl()
    {
        if ($this['customer']) {
            $base = Customer::classUrl();
            $parent = $this['customer'];
            $path = 'sources';
        } else {
            $msg = "Alipay accounts cannot be accessed without a customer ID.";
            throw new Error\InvalidRequest($msg, null);
        }
        $parentExtn = urlencode(Util\Util::utf8($parent));
        $extn = urlencode(Util\Util::utf8($this['id']));
        return "$base/$parentExtn/$path/$extn";
    }
}
