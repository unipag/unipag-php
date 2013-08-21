Unipag Client for PHP
========================

.. image:: https://travis-ci.org/unipag/unipag-php.png?branch=master
        :target: https://travis-ci.org/unipag/unipag-php

Requirements
------------

PHP versions 5.2, 5.3, 5.4, 5.5 compiled with cURL.

Installation
------------

Install with Composer
~~~~~~~~~~~~~~~~~~~~~

If you're using `Composer <http://getcomposer.org>`_ to manage dependencies,
you can add Unipag Client for PHP with it:

.. code:: json

    {
        "require": {
            "unipag/unipag": ">=0.1.0"
        }
    }

or to get the latest version off the master branch:

.. code:: json

    {
        "require": {
            "unipag/unipag": "dev-master"
        }
    }


Install source from GitHub
~~~~~~~~~~~~~~~~~~~~~~~~~~

Install the source code:

.. code:: php

    $ git clone git://github.com/unipag/unipag-php.git

And include it using the autoloader:

.. code:: php

    require_once '/your/libraries/path/Unipag/Autoloader.php';
    Unipag_Autoloader::register();

Or, if you're using Composer:

.. code:: php

    require_once 'vendor/autoload.php';

Sample usage
------------

Create invoice
~~~~~~~~~~~~~~

.. code:: php

    // Get your key at https://my.unipag.com
    Unipag_Config::$api_key = '<your-secret-key>';

    $invoice = Unipag_Invoice::create(array(
        'amount' => 42,
        'currency' => 'USD',
    ));

    // Done. $invoice->id now contains unique id of this invoice at Unipag.

Install Unipag widget
~~~~~~~~~~~~~~~~~~~~~

Try our widget for payments workflow handling. It's quite optional, but you
might find it handy and time-saving.

.. code:: html

    <script type="text/javascript"
        src="//d3oe3cumn3db7.cloudfront.net/uw3/js/uw3.min.js"
        charset="utf-8"
        id="uw3js"
        data-key="<your-public-key>">
    </script>

Please note, it is important that you use **public key** for widget.
Public keys have restricted access to your data and are supposed to be safe
for use in browser.


Handle webhook from Unipag
~~~~~~~~~~~~~~~~~~~~~~~~~~

Create a standalone page on your website which will handle events sent by
Unipag. Register URL of this page at `<https://my.unipag.com>`_ > Settings > Webhooks.
Initialize page code as following:

.. code:: php

    Unipag_Config::$api_key = '<your-secret-key>';

    $event = Unipag_Event::fromJson($HTTP_RAW_POST_DATA);

    // In this example we subscribe to invoice-related events only:
    if ($event->related_object instanceof Unipag_Invoice) {

        // Always reload information from Unipag for security reasons:
        $invoice = $event->related_object->reload();

        // Now $invoice contains the most recent information, securely loaded from Unipag.
    }

Tip: webhooks can be a pain to debug. Check out Unipag Network Activity log, it
is available at `<https://my.unipag.com>`_ > Network Activity. You may find it
useful for your webhook handlers debugging.

Usage of invoice "custom_data" property
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Invoice objects have an optional "custom_data" property, which can be used to
store up to 32KB of arbitrary data in JSON format. You can freely use this field
to store additional information about invoices, which is specific for your
application.

In PHP, you can use associative and numeric arrays, strings, numbers, boolean
and NULL values in any combination to store them in custom_data. All of these
types will be properly serialized when sending to Unipag and deserialized when
fetching them back. Consider the following examples, all of them are valid
usages of custom_data property:

.. code:: php

    Unipag_Config::$api_key = '<your-secret-key>';

    $invoice = Unipag_Invoice::create(array(
        'amount' => 42,
        'currency' => 'USD',
    ));

    // Store associative and numeric arrays, and single values
    $invoice->custom_data = array(
        'address' => array(
            'billing' => '5863 Gentle Pond Rise, Suspension, Ontario, CA',
            'shipping' => '9215 Red Ridge, Lancer, Idaho, US',
        ),
        'contact_phones' => array('555-4242', '555-9000'),
        'magic_number' => 42,
    );
    $invoice->save();

    // Clean everything out
    $invoice->custom_data = NULL;
    $invoice->save();

    // Store a single value. Yes, it will be a valid JSON.
    $invoice->custom_data = True;
    $invoice->save();

    // Store numeric array as a root element. Let's assume that we need to save cart items:
    $invoice->custom_data = array(
        array(
            'product' => 'apples',
            'price' => 10.0,
            'quantity' => 1,
        ),
        array(
            'product' => 'oranges',
            'price' => 12.5,
            'quantity' => 2,
        ),
    );
    $invoice->save();


Report bugs
-----------

Report issues to the project's `Issues Tracking`_ on Github.

.. _`Issues Tracking`: https://github.com/unipag/unipag-php/issues
