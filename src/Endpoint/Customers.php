<?php

namespace Customerio\Endpoint;

use GuzzleHttp\Exception\GuzzleException;

class Customers extends Base
{
    /** @var Customers\Devices */
    public $devices;

    /**
     * Customers constructor.
     * @param $client
     */
    public function __construct($client)
    {
        parent::__construct($client);
        $this->devices = new Customers\Devices($client);
    }

    /**
     * Register customer event
     * @param string $name
     * @param array $options
     * @return mixed
     * @throws GuzzleException
     */
    public function event(string $name, array $options)
    {
        if (!isset($options['id']) && !isset($options['email']) && !isset($options['cio_id'])) {
            $this->mockException('User id, email or cio_id is required!', 'POST');
        } // @codeCoverageIgnore

        if (!isset($name)) {
            $this->mockException('Event name required!', 'POST');
        } // @codeCoverageIgnore

        $options['name'] = $name;

        $path = $this->setCustomerPathWithIdentifier($options);

        return $this->client->post($path . "/events", $options);
    }

    /**
     * Add new customer
     * @param array $options
     * @return mixed
     * @throws GuzzleException
     */
    public function add(array $options)
    {
        if (!isset($options['id']) && !isset($options['email']) && !isset($options['cio_id'])) {
            $this->mockException('User id or email is required!', 'PUT');
        } // @codeCoverageIgnore

        $path = $this->setCustomerPathWithIdentifier($options);

        return $this->client->put($path, $options);
    }


    /**
     * Delete customer
     * @param array $options
     * @return mixed
     * @throws GuzzleException
     */
    public function delete(array $options)
    {
        if (!isset($options['id']) && !isset($options['email']) && !isset($options['cio_id'])) {
            $this->mockException('User id or email is required!', 'DELETE');
        } // @codeCoverageIgnore

        $path = $this->setCustomerPathWithIdentifier($options);

        return $this->client->delete($path, []);
    }

    /**
     * Update new customer
     * @param array $options
     * @return mixed
     * @throws GuzzleException
     */
    public function update(array $options)
    {
        return $this->add($options);
    }

    /**
     * Get customer by email address
     * @param array $options
     * @return mixed
     * @throws GuzzleException
     */
    public function get(array $options)
    {
        if (!isset($options['email'])) {
            $this->mockException('Email is required!', 'GET');
        } // @codeCoverageIgnore

        $path = $this->customerPath();

        return $this->client->get($path, $options);
    }

    /**
     * Search customers
     * @param array $options
     * @return mixed
     * @throws GuzzleException
     */
    public function search(array $options)
    {
        if (!isset($options['filter'])) {
            $this->mockException('Filter is required!', 'POST');
        } // @codeCoverageIgnore

        $path = $this->customerPath();
        $options['endpoint'] = $this->client->getRegion()->apiUri();

        return $this->client->post($path, $options);
    }

    /**
     * List customer attributes
     * @param array $options
     * @return mixed
     * @throws GuzzleException
     */
    public function attributes(array $options)
    {
        if (!isset($options['id']) && !isset($options['email'])) {
            $this->mockException('User id or email is required!', 'GET');
        } // @codeCoverageIgnore

        $path = $this->setCustomerPathWithIdentifier($options, ['attributes']);

        return $this->client->get($path, $options);
    }

    /**
     * List customer segments
     * @param array $options
     * @return mixed
     * @throws GuzzleException
     */
    public function segments(array $options)
    {
        if (!isset($options['id']) && !isset($options['email'])) {
            $this->mockException('User id or email is required!', 'GET');
        } // @codeCoverageIgnore

        $path = $this->setCustomerPathWithIdentifier($options, ['segments']);

        return $this->client->get($path, $options);
    }

    /**
     * Get metadata about messages sent to a customer
     * @param array $options
     * @return mixed
     * @throws GuzzleException
     */
    public function messages(array $options)
    {
        if (!isset($options['id']) && !isset($options['email'])) {
            $this->mockException('User id or email is required!', 'GET');
        } // @codeCoverageIgnore

        $path = $this->setCustomerPathWithIdentifier($options, ['messages']);

        return $this->client->get($path, $options);
    }

    /**
     * Get data about activities performed by or for a customer
     * @param array $options
     * @return mixed
     * @throws GuzzleException
     */
    public function activities(array $options)
    {
        if (!isset($options['id']) && !isset($options['email'])) {
            $this->mockException('User id or email is required!', 'GET');
        } // @codeCoverageIgnore

        $path = $this->setCustomerPathWithIdentifier($options, ['activities']);

        return $this->client->get($path, $options);
    }

    /**
     * Suppress a customer
     * @param array $options
     * @return mixed
     * @throws GuzzleException
     */
    public function suppress(array $options)
    {
        if (!isset($options['id']) && !isset($options['email'])) {
            $this->mockException('User id or email is required!', 'GET');
        } // @codeCoverageIgnore

        $path = $this->setCustomerPathWithIdentifier($options, ['suppress']);

        return $this->client->post($path, $options);
    }

    /**
     * Unsuppress a customer
     * @param array $options
     * @return mixed
     * @throws GuzzleException
     */
    public function unsuppress(array $options)
    {
        if (!isset($options['id']) && !isset($options['email'])) {
            $this->mockException('User id or email is required!', 'GET');
        } // @codeCoverageIgnore

        $path = $this->setCustomerPathWithIdentifier($options, ['unsuppress']);

        return $this->client->post($path, $options);
    }

    /**
     * Merge duplicate people
     * @param array $options
     * @return mixed
     * @throws GuzzleException
     */
    public function merge(array $options)
    {
        if (!isset($options['primary']['id']) && !isset($options['primary']['email']) && !isset($options['primary']['cio_id'])) {
            $this->mockException('Primary user id, email or cio_id is required!', 'POST');
        } // @codeCoverageIgnore

        if (!isset($options['secondary']['id']) && !isset($options['secondary']['email']) && !isset($options['secondary']['cio_id'])) {
            $this->mockException('Secondary user id, email or cio_id is required!', 'POST');
        } // @codeCoverageIgnore

        return $this->client->post('merge_customers', $options);
    }

    /**
     * Set the customer path with the relevant identifier
     * @param array $options
     * @param array $params
     * @return string
     */
    private function setCustomerPathWithIdentifier(array &$options, array $params = []): string
    {
        $customerIdentifierProperty = isset($options['cio_id']) ? 'cio_id' : (isset($options['id']) ? 'id' : 'email');
        $customerIdentifierPrefix = isset($options['cio_id']) ? 'cio_' : '';

        $path = $this->customerPath($customerIdentifierPrefix.$options[$customerIdentifierProperty], $params);
        unset($options[$customerIdentifierProperty]);

        return $path;
    }
}
