<?php

namespace SierraTecnologia;

class AccountTest extends TestCase
{
    const TEST_RESOURCE_ID = 'acct_123';
    const TEST_CAPABILITY_ID = 'acap_123';
    const TEST_EXTERNALACCOUNT_ID = 'ba_123';
    const TEST_PERSON_ID = 'person_123';

    public function testIsListable()
    {
        $this->expectsRequest(
            'get',
            '/v1/accounts'
        );
        $resources = Account::all();
        $this->assertTrue(is_array($resources->data));
        $this->assertInstanceOf("SierraTecnologia\\Account", $resources->data[0]);
    }

    public function testIsRetrievable()
    {
        $this->expectsRequest(
            'get',
            '/v1/accounts/' . self::TEST_RESOURCE_ID
        );
        $resource = Account::retrieve(self::TEST_RESOURCE_ID);
        $this->assertInstanceOf("SierraTecnologia\\Account", $resource);
    }

    public function testIsRetrievableWithoutId()
    {
        $this->expectsRequest(
            'get',
            '/v1/account'
        );
        $resource = Account::retrieve();
        $this->assertInstanceOf("SierraTecnologia\\Account", $resource);
    }

    public function testIsCreatable()
    {
        $this->expectsRequest(
            'post',
            '/v1/accounts'
        );
        $resource = Account::create(["type" => "custom"]);
        $this->assertInstanceOf("SierraTecnologia\\Account", $resource);
    }

    public function testIsSaveable()
    {
        $resource = Account::retrieve(self::TEST_RESOURCE_ID);
        $resource->metadata["key"] = "value";
        $this->expectsRequest(
            'post',
            '/v1/accounts/' . $resource->id
        );
        $resource->save();
        $this->assertInstanceOf("SierraTecnologia\\Account", $resource);
    }

    public function testIsUpdatable()
    {
        $this->expectsRequest(
            'post',
            '/v1/accounts/' . self::TEST_RESOURCE_ID
        );
        $resource = Account::update(self::TEST_RESOURCE_ID, [
            "metadata" => ["key" => "value"],
        ]);
        $this->assertInstanceOf("SierraTecnologia\\Account", $resource);
    }

    public function testIsDeletable()
    {
        $resource = Account::retrieve(self::TEST_RESOURCE_ID);
        $this->expectsRequest(
            'delete',
            '/v1/accounts/' . $resource->id
        );
        $resource->delete();
        $this->assertInstanceOf("SierraTecnologia\\Account", $resource);
    }

    public function testIsRejectable()
    {
        $account = Account::retrieve(self::TEST_RESOURCE_ID);
        $this->expectsRequest(
            'post',
            '/v1/accounts/' . $account->id . '/reject'
        );
        $resource = $account->reject(["reason" => "fraud"]);
        $this->assertInstanceOf("SierraTecnologia\\Account", $resource);
        $this->assertSame($resource, $account);
    }

    public function testIsDeauthorizable()
    {
        $resource = Account::retrieve(self::TEST_RESOURCE_ID);
        $this->stubRequest(
            'post',
            '/oauth/deauthorize',
            [
                'client_id' => SierraTecnologia::getClientId(),
                'sierratecnologia_user_id' => $resource->id,
            ],
            null,
            false,
            [
                'sierratecnologia_user_id' => $resource->id,
            ],
            200,
            SierraTecnologia::$connectBase
        );
        $resource->deauthorize();
    }

    public function testPersons()
    {
        $account = Account::retrieve(self::TEST_RESOURCE_ID);
        $this->expectsRequest(
            'get',
            '/v1/accounts/' . $account->id . '/persons'
        );
        $persons = $account->persons();
        $this->assertTrue(is_array($persons->data));
        $this->assertInstanceOf("SierraTecnologia\\Person", $persons->data[0]);
    }

    public function testCanRetrieveCapability()
    {
        $this->expectsRequest(
            'get',
            '/v1/accounts/' . self::TEST_RESOURCE_ID . '/capabilities/' . self::TEST_CAPABILITY_ID
        );
        $resource = Account::retrieveCapability(self::TEST_RESOURCE_ID, self::TEST_CAPABILITY_ID);
        $this->assertInstanceOf("SierraTecnologia\\Capability", $resource);
    }

    public function testCanUpdateCapability()
    {
        $this->expectsRequest(
            'post',
            '/v1/accounts/' . self::TEST_RESOURCE_ID . '/capabilities/' . self::TEST_CAPABILITY_ID
        );
        $resource = Account::updateCapability(self::TEST_RESOURCE_ID, self::TEST_CAPABILITY_ID, [
            "requested" => true,
        ]);
        $this->assertInstanceOf("SierraTecnologia\\Capability", $resource);
    }

    public function testCanListCapabilities()
    {
        $this->expectsRequest(
            'get',
            '/v1/accounts/' . self::TEST_RESOURCE_ID . '/capabilities'
        );
        $resources = Account::allCapabilities(self::TEST_RESOURCE_ID);
        $this->assertTrue(is_array($resources->data));
    }

    public function testCanCreateExternalAccount()
    {
        $this->expectsRequest(
            'post',
            '/v1/accounts/' . self::TEST_RESOURCE_ID . '/external_accounts'
        );
        $resource = Account::createExternalAccount(self::TEST_RESOURCE_ID, [
            "external_account" => "btok_123",
        ]);
        $this->assertInstanceOf("SierraTecnologia\\BankAccount", $resource);
    }

    public function testCanRetrieveExternalAccount()
    {
        $this->expectsRequest(
            'get',
            '/v1/accounts/' . self::TEST_RESOURCE_ID . '/external_accounts/' . self::TEST_EXTERNALACCOUNT_ID
        );
        $resource = Account::retrieveExternalAccount(self::TEST_RESOURCE_ID, self::TEST_EXTERNALACCOUNT_ID);
        $this->assertInstanceOf("SierraTecnologia\\BankAccount", $resource);
    }

    public function testCanUpdateExternalAccount()
    {
        $this->expectsRequest(
            'post',
            '/v1/accounts/' . self::TEST_RESOURCE_ID . '/external_accounts/' . self::TEST_EXTERNALACCOUNT_ID
        );
        $resource = Account::updateExternalAccount(self::TEST_RESOURCE_ID, self::TEST_EXTERNALACCOUNT_ID, [
            "name" => "name",
        ]);
        $this->assertInstanceOf("SierraTecnologia\\BankAccount", $resource);
    }

    public function testCanDeleteExternalAccount()
    {
        $this->expectsRequest(
            'delete',
            '/v1/accounts/' . self::TEST_RESOURCE_ID . '/external_accounts/' . self::TEST_EXTERNALACCOUNT_ID
        );
        $resource = Account::deleteExternalAccount(self::TEST_RESOURCE_ID, self::TEST_EXTERNALACCOUNT_ID);
        $this->assertTrue($resource->deleted);
    }

    public function testCanListExternalAccounts()
    {
        $this->expectsRequest(
            'get',
            '/v1/accounts/' . self::TEST_RESOURCE_ID . '/external_accounts'
        );
        $resources = Account::allExternalAccounts(self::TEST_RESOURCE_ID);
        $this->assertTrue(is_array($resources->data));
    }

    public function testCanCreateLoginLink()
    {
        $this->expectsRequest(
            'post',
            '/v1/accounts/' . self::TEST_RESOURCE_ID . '/login_links'
        );
        $resource = Account::createLoginLink(self::TEST_RESOURCE_ID);
        $this->assertInstanceOf("SierraTecnologia\\LoginLink", $resource);
    }

    public function testCanCreatePerson()
    {
        $this->expectsRequest(
            'post',
            '/v1/accounts/' . self::TEST_RESOURCE_ID . '/persons'
        );
        $resource = Account::createPerson(self::TEST_RESOURCE_ID, [
            "dob" => [
                "day" => 1,
                "month" => 1,
                "year" => 1980
            ]
        ]);
        $this->assertInstanceOf("SierraTecnologia\\Person", $resource);
    }

    public function testCanRetrievePerson()
    {
        $this->expectsRequest(
            'get',
            '/v1/accounts/' . self::TEST_RESOURCE_ID . '/persons/' . self::TEST_PERSON_ID
        );
        $resource = Account::retrievePerson(self::TEST_RESOURCE_ID, self::TEST_PERSON_ID);
        $this->assertInstanceOf("SierraTecnologia\\Person", $resource);
    }

    public function testCanUpdatePerson()
    {
        $this->expectsRequest(
            'post',
            '/v1/accounts/' . self::TEST_RESOURCE_ID . '/persons/' . self::TEST_PERSON_ID
        );
        $resource = Account::updatePerson(self::TEST_RESOURCE_ID, self::TEST_PERSON_ID, [
            "first_name" => "First name",
        ]);
        $this->assertInstanceOf("SierraTecnologia\\Person", $resource);
    }

    public function testCanDeletePerson()
    {
        $this->expectsRequest(
            'delete',
            '/v1/accounts/' . self::TEST_RESOURCE_ID . '/persons/' . self::TEST_PERSON_ID
        );
        $resource = Account::deletePerson(self::TEST_RESOURCE_ID, self::TEST_PERSON_ID);
        $this->assertTrue($resource->deleted);
    }

    public function testCanListPersons()
    {
        $this->expectsRequest(
            'get',
            '/v1/accounts/' . self::TEST_RESOURCE_ID . '/persons'
        );
        $resources = Account::allPersons(self::TEST_RESOURCE_ID);
        $this->assertTrue(is_array($resources->data));
    }

    public function testSerializeNewAdditionalOwners()
    {
        $obj = Util\Util::convertToSierraTecnologiaObject([
            'object' => 'account',
            'legal_entity' => SierraTecnologiaObject::constructFrom([]),
        ], null);
        $obj->legal_entity->additional_owners = [
            ['first_name' => 'Joe'],
            ['first_name' => 'Jane'],
        ];

        $expected = [
            'legal_entity' => [
                'additional_owners' => [
                    0 => ['first_name' => 'Joe'],
                    1 => ['first_name' => 'Jane'],
                ],
            ],
        ];
        $this->assertSame($expected, $obj->serializeParameters());
    }

    public function testSerializeAddAdditionalOwners()
    {
        $obj = Util\Util::convertToSierraTecnologiaObject([
            'object' => 'account',
            'legal_entity' => [
                'additional_owners' => [
                    SierraTecnologiaObject::constructFrom(['first_name' => 'Joe']),
                    SierraTecnologiaObject::constructFrom(['first_name' => 'Jane']),
                ],
            ],
        ], null);
        $obj->legal_entity->additional_owners[2] = ['first_name' => 'Andrew'];

        $expected = [
            'legal_entity' => [
                'additional_owners' => [
                    2 => ['first_name' => 'Andrew'],
                ],
            ],
        ];
        $this->assertSame($expected, $obj->serializeParameters());
    }

    public function testSerializePartiallyChangedAdditionalOwners()
    {
        $obj = Util\Util::convertToSierraTecnologiaObject([
            'object' => 'account',
            'legal_entity' => [
                'additional_owners' => [
                    SierraTecnologiaObject::constructFrom(['first_name' => 'Joe']),
                    SierraTecnologiaObject::constructFrom(['first_name' => 'Jane']),
                ],
            ],
        ], null);
        $obj->legal_entity->additional_owners[1]->first_name = 'SierraTecnologia';

        $expected = [
            'legal_entity' => [
                'additional_owners' => [
                    1 => ['first_name' => 'SierraTecnologia'],
                ],
            ],
        ];
        $this->assertSame($expected, $obj->serializeParameters());
    }

    public function testSerializeUnchangedAdditionalOwners()
    {
        $obj = Util\Util::convertToSierraTecnologiaObject([
            'object' => 'account',
            'legal_entity' => [
                'additional_owners' => [
                    SierraTecnologiaObject::constructFrom(['first_name' => 'Joe']),
                    SierraTecnologiaObject::constructFrom(['first_name' => 'Jane']),
                ],
            ],
        ], null);

        $expected = [
            'legal_entity' => [
                'additional_owners' => [],
            ],
        ];
        $this->assertSame($expected, $obj->serializeParameters());
    }

    public function testSerializeUnsetAdditionalOwners()
    {
        $obj = Util\Util::convertToSierraTecnologiaObject([
            'object' => 'account',
            'legal_entity' => [
                'additional_owners' => [
                    SierraTecnologiaObject::constructFrom(['first_name' => 'Joe']),
                    SierraTecnologiaObject::constructFrom(['first_name' => 'Jane']),
                ],
            ],
        ], null);
        $obj->legal_entity->additional_owners = null;

        // Note that the empty string that we send for this one has a special
        // meaning for the server, which interprets it as an array unset.
        $expected = [
            'legal_entity' => [
                'additional_owners' => '',
            ],
        ];
        $this->assertSame($expected, $obj->serializeParameters());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSerializeAdditionalOwnersDeletedItem()
    {
        $obj = Util\Util::convertToSierraTecnologiaObject([
            'object' => 'account',
            'legal_entity' => [
                'additional_owners' => [
                    SierraTecnologiaObject::constructFrom(['first_name' => 'Joe']),
                    SierraTecnologiaObject::constructFrom(['first_name' => 'Jane']),
                ],
            ],
        ], null);
        unset($obj->legal_entity->additional_owners[0]);

        $obj->serializeParameters();
    }

    public function testSerializeExternalAccountString()
    {
        $obj = Util\Util::convertToSierraTecnologiaObject([
            'object' => 'account',
        ], null);
        $obj->external_account = 'btok_123';

        $expected = [
            'external_account' => 'btok_123',
        ];
        $this->assertSame($expected, $obj->serializeParameters());
    }

    public function testSerializeExternalAccountHash()
    {
        $obj = Util\Util::convertToSierraTecnologiaObject([
            'object' => 'account',
        ], null);
        $obj->external_account = [
            'object' => 'bank_account',
            'routing_number' => '110000000',
            'account_number' => '000123456789',
            'country' => 'US',
            'currency' => 'usd',
        ];

        $expected = [
            'external_account' => [
                'object' => 'bank_account',
                'routing_number' => '110000000',
                'account_number' => '000123456789',
                'country' => 'US',
                'currency' => 'usd',
            ],
        ];
        $this->assertSame($expected, $obj->serializeParameters());
    }

    public function testSerializeBankAccountString()
    {
        $obj = Util\Util::convertToSierraTecnologiaObject([
            'object' => 'account',
        ], null);
        $obj->bank_account = 'btok_123';

        $expected = [
            'bank_account' => 'btok_123',
        ];
        $this->assertSame($expected, $obj->serializeParameters());
    }

    public function testSerializeBankAccountHash()
    {
        $obj = Util\Util::convertToSierraTecnologiaObject([
            'object' => 'account',
        ], null);
        $obj->bank_account = [
            'object' => 'bank_account',
            'routing_number' => '110000000',
            'account_number' => '000123456789',
            'country' => 'US',
            'currency' => 'usd',
        ];

        $expected = [
            'bank_account' => [
                'object' => 'bank_account',
                'routing_number' => '110000000',
                'account_number' => '000123456789',
                'country' => 'US',
                'currency' => 'usd',
            ],
        ];
        $this->assertSame($expected, $obj->serializeParameters());
    }

    public function testSerializeNewIndividual()
    {
        $obj = Util\Util::convertToSierraTecnologiaObject([
            'object' => 'account',
        ], null);
        $obj->individual = ['first_name' => 'Jane'];

        $expected = ['individual' => ['first_name' => 'Jane']];
        $this->assertSame($expected, $obj->serializeParameters());
    }

    public function testSerializePartiallyChangedIndividual()
    {
        $obj = Util\Util::convertToSierraTecnologiaObject([
            'object' => 'account',
            'individual' => Util\Util::convertToSierraTecnologiaObject([
                'object' => 'person',
                'first_name' => 'Jenny',
            ], null),
        ], null);
        $obj->individual = ['first_name' => 'Jane'];

        $expected = ['individual' => ['first_name' => 'Jane']];
        $this->assertSame($expected, $obj->serializeParameters());
    }

    public function testSerializeUnchangedIndividual()
    {
        $obj = Util\Util::convertToSierraTecnologiaObject([
            'object' => 'account',
            'individual' => Util\Util::convertToSierraTecnologiaObject([
                'object' => 'person',
                'first_name' => 'Jenny',
            ], null),
        ], null);

        $expected = ['individual' => []];
        $this->assertSame($expected, $obj->serializeParameters());
    }

    public function testSerializeUnsetIndividual()
    {
        $obj = Util\Util::convertToSierraTecnologiaObject([
            'object' => 'account',
            'individual' => Util\Util::convertToSierraTecnologiaObject([
                'object' => 'person',
                'first_name' => 'Jenny',
            ], null),
        ], null);
        $obj->individual = null;

        $expected = ['individual' => ''];
        $this->assertSame($expected, $obj->serializeParameters());
    }
}
