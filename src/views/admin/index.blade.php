<!DOCTYPE html>
<html>
    <head>
        {{ HTML::style('css/vendor/auja.css') }}
        {{ HTML::style('css/vendor/trumbowyg.css') }}
        {{ HTML::style('css/vendor/ionicons.css') }}
        {{ HTML::style('css/vendor/animate.css') }}
        {{ HTML::script('js/vendor/auja.js') }}
    </head>
    {{-- data-src points to the Route that generates the Auja json manifest --}}
    <body data-src="{{{ URL::route('auja.support.main', [], false) }}}"></body>
</html>