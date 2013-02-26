# AMNL\Mollie
[![Build Status](https://travis-ci.org/itavero/AMNL-Mollie.png?branch=master)](https://travis-ci.org/itavero/AMNL-Mollie)

The goal of this PHP 5.3+ library is to simplify the implementation of the payment methods offered by [Mollie B.V.](http://www.mollie.nl)

## PROBLEM?
If you run in to any issues when using this library or perhaps you have some good suggestions, please care to share them. Just [create a new issue over here](https://github.com/itavero/AMNL-Mollie/issues/new).

## Current State
*Last update: 2012/06/16*

The library is still in development and I've only manually tested the source in my development environment.
However, since I used the code examples provided by Mollie aswell as their documentation to write all of this, I reckon it's safe to use in a production environment too.

At the moment I've only implemented iDEAL, MiniTix and Micropayments (IVR).
I'm still working on the Paysafecard gateway, but I will publish them soon too.

## Dependencies
This library uses kriswallsmith/Buzz and needs at least PHP 5.3 to work.
The easiest way to add this library to your project, is by using the [Composer dependency manager](http://getcomposer.org/).
