<head>
    <meta charset="utf-8">
    <title>@yield('meta_title', '')</title>
    <meta name="description" content="@yield('meta_description', '')">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <!-- head block -->
    @stack('head')

    <!-- styles & fonts -->
    @fonts

    <link rel="stylesheet" href="{{route('fair-queue.assets.awes', ['module' => 'indigo-layout', 'type'=>'css', 'filename' => 'main.css'])}}">
    <link rel="stylesheet" href="{{route('fair-queue.assets.awes', ['module' => 'indigo-layout', 'type'=>'css', 'filename' => 'editor.css'])}}">
    <link rel="stylesheet" href="{{route('fair-queue.assets.awes', ['module' => 'system-notify', 'type'=>'css', 'filename' => 'main.css'])}}">
    <link rel="stylesheet" href="{{route('fair-queue.assets.awes', ['module' => 'filter-wrapper', 'type'=>'css', 'filename' => 'main.css'])}}">
{{--    <link rel="stylesheet" href="{{route('fair-queue.assets.css.table_builder')}}">--}}
{{--    <link rel="stylesheet" href="{{route('fair-queue.assets.css.context_menu')}}">--}}
{{--    @styles(['items' => $src['css']])--}}

     @theme
     @externalLink

     <!-- config -->
     <script>var AWES_CONFIG = @json(array_merge_recursive(config('indigo-layout.frontend'), [
         'lang' => app(\Illuminate\Contracts\Translation\Translator::class)->get('indigo-layout::js')
     ], (isset($awes_custom_config) ? $awes_custom_config : [])))</script>

{{--    @scripts([--}}
{{--    'items' => $src['js'],--}}
{{--    'nomodule' => false,--}}
{{--    'exclude' => array_merge(['base-js'], (isset($exclude_scripts) ? $exclude_scripts : []))--}}
{{--    ])--}}

     <!-- scripts -->
     {{-- if you need IE11 uncomment next line --}}
    {{-- @scripts(['items' => $src['legacy'], 'nomodule' => true]) --}}
    <script type="module" src="{{route('fair-queue.assets.awes', ['module' => 'indigo-layout', 'type'=>'js', 'filename' => 'main.js'])}}"></script>
    <script type="module" src="{{route('fair-queue.assets.awes', ['module' => 'theme-switcher', 'type'=>'js', 'filename' => 'main.js'])}}"></script>
    <script type="module" src="{{route('fair-queue.assets.awes', ['module' => 'system-notify', 'type'=>'js', 'filename' => 'main.js'])}}"></script>
    <script type="module" src="{{route('fair-queue.assets.awes', ['module' => 'base-js', 'type'=>'js', 'filename' => 'main.js'])}}"></script>
    <script type="module" src="{{route('fair-queue.assets.awes', ['module' => 'table-builder', 'type'=>'js', 'filename' => 'main.js'])}}"></script>
    <script type="module" src="{{route('fair-queue.assets.awes', ['module' => 'context-menu', 'type'=>'js', 'filename' => 'main.js'])}}"></script>
    <script type="module" src="{{route('fair-queue.assets.awes', ['module' => 'modal-window', 'type'=>'js', 'filename' => 'main.js'])}}"></script>
    <script type="module" src="{{route('fair-queue.assets.awes', ['module' => 'form-builder', 'type'=>'js', 'filename' => 'main.js'])}}"></script>
    <script type="module" src="{{route('fair-queue.assets.awes', ['module' => 'filter-wrapper', 'type'=>'js', 'filename' => 'main.js'])}}"></script>
</head>
