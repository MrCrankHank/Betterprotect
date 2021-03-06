<br />
<p align="center">
  <a href="">
    <img src="logo.png" alt="Logo" width="120" height="120">
  </a>

  <h3 align="center">Betterprotect</h3>

  <p align="center">
    Betterprotect is a Postfix Management System. It includes a Log Parser, the ability to whitelist/blacklist addresses and LDAP integration.
    <br />
    <a href="https://docs.betterprotect.de/"><strong>Explore the docs »</strong></a>
    <br />
    <br />
    ·
    <a href="https://github.com/Hank-IT/Betterprotect/issues">Report Bug</a>
    ·
    <a href="https://github.com/Hank-IT/Betterprotect/issues">Request Feature</a>
  </p>
</p>

## Table of Contents

* [About the Project](#about-the-project)
  * [Built With](#built-with)
* [Getting Started](#getting-started)
* [Usage](#usage)
* [Contributing](#contributing)
* [Security](#Security)
* [License](#license)
* [Contact](#contact)

## About The Project

There seems to be a lack of modern, high quality, easy to use postfix management interfaces - or atleast I couldn't find any. To take the pain out of daily postfix management I created Betterprotect, which tries to simplify day to day work with postfix.

The main features are:
* **Log Parser**, which can search the log of multiple servers at once.
* **Same configuration for multiple servers**. All changes are made inside a "Policy". This policy is then pushed to the server, making configuration of multiple servers a breeze.
* **Black/Whitelist**. Allows to blacklist/whitelist source IPs, source networks, sender addresses and combinations.
* **Transport**. Configure Postfix transport rules.
* **Relay Domains**. Configure Postfix relay domains.
* **Recipients**. Betterprotect keeps a list of recipients for which Postfix should accept mail. It is also able to pull recipient addresses from users and groups out of one or more Ldap Directories (currently only ActiveDirectory).
* **LDAP Authentication** for Betterprotect admins. No need to open up another user management.

Missing something from this list? Open an Issue and let me know! <a href="https://github.com/Hank-IT/Betterprotect/issues">Request Feature</a>

### Built With
* [Laravel](https://laravel.com)
* [Laravel Sanctum](https://github.com/laravel/sanctum)
* [Laravel Websockets](https://github.com/beyondcode/laravel-websockets)
* [Bootstrap](https://getbootstrap.com)
* [Bootstrap Vue](https://bootstrap-vue.js.org/)
* [Font Awesome](https://fontawesome.com)
* And many more!

## Getting Started
<a href="https://docs.betterprotect.de/installation/">Take a look at our installation guide</a>

## Contributing

Contributions are what make the open source community such an amazing place to be learn, inspire, and create. Any contributions you make are **greatly appreciated**.

Please open an Issue before you start building your feature: <a href="https://github.com/Hank-IT/Betterprotect/issues">Issues</a>

## Security
If you find a security related issue, please contact me on the email address below. All security problems will be addressed as fast as possible.

## License

Distributed under the MIT License. See `LICENSE` for more information.

## Contact

Alexander Hank - mail@hank-it.com

Project Link: [https://github.com/Hank-IT/Betterprotect](https://github.com/Hank-IT/Betterprotect)
