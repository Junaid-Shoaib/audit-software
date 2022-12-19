<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title inertia>{{ config('app.name', 'MZ-Audit') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <!-- Scripts -->
    @routes
    <script src="{{ asset('js/app.js') }}" defer></script>

    @inertiaHead
</head>

<body class="font-sans antialiased">
    <style>
        .multiselect {
            box-sizing: content-box;
            display: block;
            position: relative;
            width: 100%;
            min-height: 30px;
            text-align: left;
            color: #35495e;
        }

        .multiselect,
        .multiselect__input,
        .multiselect__single {
            font-family: inherit;
            font-size: 14px;
            touch-action: manipulation;
        }

        .multiselect__select {
            line-height: 10px;
            display: block;
            position: absolute;
            box-sizing: border-box;
            width: 40px;
            height: 35px;
            right: 1px;
            top: 1px;
            padding: 4px 8px;
            px: ;
            */: ;
            margin: 0;
            text-decoration: none;
            text-align: center;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .multiselect__tags {
            min-height: 30px;
            display: block;
            padding: 4px 40px 0 8px;
            border-radius: 5px;
            border: 1px solid #e8e8e8;
            background: #fff;
            font-size: 14px;
            height: 30px;
        }

        /* //new */
        .multiselect__option--highlight {
            background: #374151;
            outline: none;
            color: white;
        }


        .multiselect__option--highlight::after {
            content: attr(data-select);
            background: #374151;
            color: white;
        }



        .multiselect__option--selected {
            background: #1f2937;
            color: #E5E7EB;
            font-weight: bold;
            border: 1px solid #e8e8e8;

        }


        .multiselect__option--selected.multiselect__option--highlight {
            background: #e5e7eb;
            color: #1f2937;
        }


        .multiselect__option--selected.multiselect__option--highlight::after {
            background: #e5e7eb;
            content: attr(data-deselect);
            color: #1f2937;
        }


        .multiselect__option--selected::after {
            content: attr(data-selected);
            color: #E5E7EB;
        }

        .trailbutton {
            padding: 7px 10px;
        }
    </style>
    @inertia

    @env('local')
    <script src="http://localhost:3000/browser-sync/browser-sync-client.js"></script>
    @endenv
</body>

</html>
