# Laravel 7 Legacy date format middleware

This package is meant to allow people who currently have public APIs built using the laravel date format before Laravel 7 the ability to simply apply a middleware to those routes, so that they will continue to output the date format that was used by default before Laravel 7. To use this middleware simply apply it to any routes that you need, and those routes will once again output the date format as 'Y-m-d H:i:s', without needing to divert the functionality of your core model from the main laravel functionality.
