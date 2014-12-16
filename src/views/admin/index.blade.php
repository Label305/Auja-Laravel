<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        {{ HTML::style('css/vendor/auja.css') }}
        {{ HTML::style('css/vendor/trumbowyg.css') }}
        {{ HTML::style('css/vendor/ionicons.css') }}
        {{ HTML::style('css/vendor/animate.css') }}
        {{ HTML::script('js/vendor/auja.js') }}
    </head>
    {{-- data-src points to the Route that generates the Auja json manifest --}}
    <body data-src="{{{ URL::route('auja.support.main', [], false) }}}"></body>
</html>
