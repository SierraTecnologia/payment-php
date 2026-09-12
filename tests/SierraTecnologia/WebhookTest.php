<?php

namespace SierraTecnologia;

class WebhookTest extends TestCase
{
    const EVENT_PAYLOAD = "{
  \"id\": \"evt_test_webhook\",
  \"object\": \"event\"
}";
    const SECRET = "whsec_test_secret";

    private function generateHeader($opts = [])
    {
        $timestamp = isset($opts['timestamp']) ? $opts['timestamp'] : time();
        $payload = isset($opts['payload']) ? $opts['payload'] : self::EVENT_PAYLOAD;
        $secret = isset($opts['secret']) ? $opts['secret'] : self::SECRET;
        $scheme = isset($opts['scheme']) ? $opts['scheme'] : WebhookSignature::EXPECTED_SCHEME;
        $signature = isset($opts['signature']) ? $opts['signature'] : null;
        if ($signature === null) {
            $signedPayload = "$timestamp.$payload";
            $signature = hash_hmac("sha256", $signedPayload, $secret);
        }
        return "t=$timestamp,$scheme=$signature";
    }

    public function testValidJsonAndHeader()
    {
        $sigHeader = $this->generateHeader();
        $event = Webhook::constructEvent(self::EVENT_PAYLOAD, $sigHeader, self::SECRET);
        $this->assertEquals("evt_test_webhook", $event->id);
    }

    public function testInvalidJson()
    {
        $this->expectException(\UnexpectedValueException::class);
        $payload = "this is not valid JSON";
        $sigHeader = $this->generateHeader(["payload" => $payload]);
        Webhook::constructEvent($payload, $sigHeader, self::SECRET);
    }

    public function testValidJsonAndInvalidHeader()
    {
        $this->expectException(\SierraTecnologia\Error\SignatureVerification::class);
        $sigHeader = "bad_header";
        Webhook::constructEvent(self::EVENT_PAYLOAD, $sigHeader, self::SECRET);
    }

    public function testMalformedHeader()
    {
        $this->expectException(\SierraTecnologia\Error\SignatureVerification::class);
        $this->expectExceptionMessage("Unable to extract timestamp and signatures from header");
        $sigHeader = "i'm not even a real signature header";
        WebhookSignature::verifyHeader(self::EVENT_PAYLOAD, $sigHeader, self::SECRET);
    }

    public function testNoSignaturesWithExpectedScheme()
    {
        $this->expectException(\SierraTecnologia\Error\SignatureVerification::class);
        $this->expectExceptionMessage("No signatures found with expected scheme");
        $sigHeader = $this->generateHeader(["scheme" => "v0"]);
        WebhookSignature::verifyHeader(self::EVENT_PAYLOAD, $sigHeader, self::SECRET);
    }

    public function testNoValidSignatureForPayload()
    {
        $this->expectException(\SierraTecnologia\Error\SignatureVerification::class);
        $this->expectExceptionMessage("No signatures found matching the expected signature for payload");
        $sigHeader = $this->generateHeader(["signature" => "bad_signature"]);
        WebhookSignature::verifyHeader(self::EVENT_PAYLOAD, $sigHeader, self::SECRET);
    }

    public function testTimestampOutsideTolerance()
    {
        $this->expectException(\SierraTecnologia\Error\SignatureVerification::class);
        $this->expectExceptionMessage("Timestamp outside the tolerance zone");
        $sigHeader = $this->generateHeader(["timestamp" => time() - 15]);
        WebhookSignature::verifyHeader(self::EVENT_PAYLOAD, $sigHeader, self::SECRET, 10);
    }

    public function testValidHeaderAndSignature()
    {
        $sigHeader = $this->generateHeader();
        $this->assertTrue(WebhookSignature::verifyHeader(self::EVENT_PAYLOAD, $sigHeader, self::SECRET, 10));
    }

    public function testHeaderContainsValidSignature()
    {
        $sigHeader = $this->generateHeader() . ",v1=bad_signature";
        $this->assertTrue(WebhookSignature::verifyHeader(self::EVENT_PAYLOAD, $sigHeader, self::SECRET, 10));
    }

    public function testTimestampOffButNoTolerance()
    {
        $sigHeader = $this->generateHeader(["timestamp" => 12345]);
        $this->assertTrue(WebhookSignature::verifyHeader(self::EVENT_PAYLOAD, $sigHeader, self::SECRET));
    }
}
