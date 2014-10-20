<!DOCTYPE html>
<html>
<head>
	<base href="../" />

    <link rel="stylesheet" type="text/css" href="assets/css/auja.css" />
    <link rel="stylesheet" type="text/css" href="bower_components/trumbowyg/dist/ui/trumbowyg.min.css" />
    <link rel="stylesheet" type="text/css" href="bower_components/Ionicons/css/ionicons.min.css" />
    <link rel="stylesheet" type="text/css" href="bower_components/animate.css/animate.min.css" />

    <script type="text/javascript">
        /**
         * Global debug flag
         * @type {boolean}
         */
        var __debug__ = true;

        /**
         * Since the main is cached fantastically, but we don't want that just
         * @type {{urlArgs: string, urlArgs: string}}
         */
        var require = {
            urlArgs: '_=' +  (new Date()).getTime()
        }
    </script>
	<script type="text/javascript" data-main="build/auja.react.js" src="bower_components/requirejs/require.js"></script>
</head>
<body data-src="main"></body>
</html>