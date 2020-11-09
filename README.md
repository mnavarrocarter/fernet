Fernet PHP
==========

Exchange strong encrypted messages effectively and privately between two parties.

## Install

```bash
composer require mnavarrocarter/fernet
```

## Usage

At the moment, there is only one version of Fernet:

```php
use MNC\Fernet\Vx80Marshaller;
use MNC\Fernet\Vx80Key;

require_once __DIR__. '/vendor/autoload.php';

// Instantiate a key for version x80
$key = Vx80Key::fromString('eLh6lGOYbbHvTHhI-nd_s76mZ7NZi9L5AA_bQNI_KoE');
// Then, create the marshaller
$marshaller = new Vx80Marshaller($key);

// Encode a message and get a token
$token = $marshaller->encode('hello');

// You can then decrypt that token back to the message
$message = $marshaller->decode($token);
```

## What is Fernet?

Fernet is a recent specification for encrypting a message and encode it into a secure
token with established security practices like block sizing, padding and signature hashing.

Encryption is symmetric using a secret of 32 bytes.

You can read more details about the specification [here][spec].

[spec]: https://github.com/fernet/spec/blob/master/Spec.md

## Why Fernet?

Mainly for three reasons:

**Security:** The spec has been defined by cryptographers, not developers, with well-known,
long-established security practices like message padding, standard block sizing, and signature
verification before decryption.

**Evolvavility:** Every token has a version (the current and only version of Fernet is 0x80).
The implementations look at the version to decide how the token will be handled. It's not the
user who defines then a set of algorithms, but the spec version. Should common nowadays
algorithms become more prone to breaking due advancements in computing power, Fernet can
solve this easily by rolling a new version of the Spec.

**Convenience:** Depending on the message, Fernet tokens can be small. They can fit cookie size
constraints easily, can be pasted in urls easily too, and shared in requests headers or bodies
without a problem.

## Fernet VS JOSE
Fernet solves all the problems existing with current "industry-standard" solutions for
message-exchanging, like the JOSE standards.

I could go on lengthy here, but if you are interested to know why, you can take a look
at [this article][article].

[article]: https://paragonie.com/blog/2017/03/jwt-json-web-tokens-is-bad-standard-that-everyone-should-avoid

