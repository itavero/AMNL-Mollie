# AMNL\Mollie
[![Build Status](https://travis-ci.org/itavero/AMNL-Mollie.png?branch=master)](https://travis-ci.org/itavero/AMNL-Mollie)

The goal of this PHP 5.3+ library is to simplify the implementation of the payment methods offered by [Mollie B.V.](http://www.mollie.nl)

## Problems and suggestions
If you run in to any issues when using this library or perhaps you have some good suggestions, please care to share them. Just [create a new issue over here](https://github.com/itavero/AMNL-Mollie/issues).

## Current State
*Last update: 2013/02/27*

I'm still working on a stable release of this library. However, several people have been using this library without any problems.
Besides that, I've based all Mollie-specific code on documentation, examples and libraries provided by Mollie, so it should be good to go.
Things on my TODO list:
+ Better unit tests
+ PaySafeCard gateway *(I don't have access to an account that has paysafecard enabled, if you do, you're more than welcome to help us out)*

## Dependencies
This library uses kriswallsmith/Buzz and needs at least PHP 5.3 to work.
The easiest way to add this library to your project, is by adding `amnl/mollie` to your dependencies in [composer.json](http://getcomposer.org/).

## License
This library may be used free of charge, however a link to this repository or my website is appreciated.
For more information, view the LICENSE file.