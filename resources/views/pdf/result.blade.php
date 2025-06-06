<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $title ?? 'Resultado IA' }}</title>
    <style>
        body {
            line-height: 1.4;
            color: #333;
            margin: 20px;
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }

        .card {
            border: 1px solid #ddd;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .card-header {
            background-color: #f5f5f5;
            padding: 10px 15px;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
            border-radius: 4px 4px 0 0;
        }

        .card-body {
            padding: 15px;
        }

        h6 {
            font-size: 14px;
            margin-top: 0;
            margin-bottom: 10px;
            font-weight: bold;
        }

        p {
            margin-top: 0;
            margin-bottom: 10px;
        }

        pre {
            white-space: pre-wrap;
            word-wrap: break-word;
            background-color: #f8f9fa;
            padding: 10px;
            border: 1px solid #eee;
            border-radius: 4px;
            font-family: monospace;
            font-size: 11px;
        }

        .row {
            margin-left: -10px;
            margin-right: -10px;
        }

        .col-md-6 {
            width: 50%;
            padding-left: 10px;
            padding-right: 10px;
            float: left;
            box-sizing: border-box;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

        .text-muted {
            color: #6c757d;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 10px;
            color: #aaa;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }

        @font-face {
            font-display: swap;
            font-family: OpenAI Sans;
            font-weight: 300 700;
            src: url(https://cdn.openai.com/common/fonts/openai-sans-variable/OpenAISansVariableVF.woff2) format("woff2"), url(https://cdn.openai.com/common/fonts/openai-sans-variable/OpenAISansVariableVF.woff) format("woff")
        }

        @layer theme {

            :host,
            :root {
                --spacing: .25rem;
                --breakpoint-md: 48rem;
                --breakpoint-lg: 64rem;
                --breakpoint-xl: 80rem;
                --breakpoint-2xl: 96rem;
                --container-xs: 20rem;
                --container-sm: 24rem;
                --container-md: 28rem;
                --container-lg: 32rem;
                --container-xl: 36rem;
                --container-2xl: 42rem;
                --container-3xl: 48rem;
                --container-4xl: 56rem;
                --container-5xl: 64rem;
                --container-6xl: 72rem;
                --text-xs: .75rem;
                --text-xs--line-height: 1.33333;
                --text-sm: .875rem;
                --text-sm--line-height: 1.42857;
                --text-base: 1rem;
                --text-base--line-height: 1.5;
                --text-lg: 1.125rem;
                --text-lg--line-height: 1.55556;
                --text-xl: 1.25rem;
                --text-xl--line-height: 1.4;
                --text-2xl: 1.5rem;
                --text-2xl--line-height: 1.33333;
                --text-3xl: 1.875rem;
                --text-3xl--line-height: 1.2;
                --text-4xl: 2.25rem;
                --text-4xl--line-height: 1.11111;
                --text-5xl: 3rem;
                --text-5xl--line-height: 1;
                --text-6xl: 3.75rem;
                --text-6xl--line-height: 1;
                --text-7xl: 4.5rem;
                --text-7xl--line-height: 1;
                --font-weight-extralight: 200;
                --font-weight-light: 300;
                --font-weight-normal: 400;
                --font-weight-medium: 500;
                --font-weight-semibold: 600;
                --font-weight-bold: 700;
                --font-weight-black: 900;
                --tracking-tighter: -.05em;
                --tracking-tight: -.025em;
                --tracking-wide: .025em;
                --tracking-widest: .1em;
                --leading-tight: 1.25;
                --leading-snug: 1.375;
                --leading-normal: 1.5;
                --leading-relaxed: 1.625;
                --radius-xs: .125rem;
                --radius-sm: .25rem;
                --radius-md: .375rem;
                --radius-lg: .5rem;
                --radius-xl: .75rem;
                --radius-2xl: 1rem;
                --radius-3xl: 1.5rem;
                --radius-4xl: 2rem;
                --drop-shadow-xs: 0 1px 1px #0000000d;
                --drop-shadow-md: 0 3px 3px #0000001f;
                --ease-in: cubic-bezier(.4, 0, 1, 1);
                --ease-out: cubic-bezier(0, 0, .2, 1);
                --ease-in-out: cubic-bezier(.4, 0, .2, 1);
                --animate-spin: spin 1s linear infinite;
                --animate-ping: ping 1s cubic-bezier(0, 0, .2, 1)infinite;
                --animate-pulse: pulse 2s cubic-bezier(.4, 0, .6, 1)infinite;
                --animate-bounce: bounce 1s infinite;
                --blur-xs: 4px;
                --blur-sm: 8px;
                --blur-md: 12px;
                --blur-lg: 16px;
                --blur-xl: 24px;
                --blur-2xl: 40px;
                --blur-3xl: 64px;
                --aspect-video: 16/9;
                --default-transition-duration: .15s;
                --default-transition-timing-function: cubic-bezier(.4, 0, .2, 1);
                --default-font-family: ui-sans-serif, -apple-system, system-ui, Segoe UI, Helvetica, Apple Color Emoji, Arial, sans-serif, Segoe UI Emoji, Segoe UI Symbol;
                --default-font-feature-settings: normal;
                --default-font-variation-settings: normal;
                --default-mono-font-family: ui-monospace, SFMono-Regular, SF Mono, Menlo, Consolas, Liberation Mono, monospace;
                --default-mono-font-feature-settings: normal;
                --default-mono-font-variation-settings: normal;
                --text-heading-3: 1.125rem;
                --text-heading-3--line-height: 1.625rem;
                --text-heading-3--letter-spacing: -.028125rem;
                --text-heading-3--font-weight: 600;
                --text-body-small-regular: .875rem;
                --text-body-small-regular--line-height: 1.125rem;
                --text-body-small-regular--letter-spacing: -.01875rem;
                --text-body-small-regular--font-weight: 400;
                --text-caption-regular: .75rem;
                --text-caption-regular--line-height: 1rem;
                --text-caption-regular--letter-spacing: -.00625rem;
                --text-caption-regular--font-weight: 400
            }
        }

        body {
            font-family: 'OpenAI Sans' !important;
            font-size: 14px !important;
        }

        .markdown {
            max-width: unset;
            font-family: 'OpenAI Sans';
        }

        .markdown.streaming-animation.block-entry-animation pre,
        .markdown.streaming-animation.block-entry-animation table {
            overflow: clip !important;
            position: relative
        }

        :is(.markdown.streaming-animation.block-entry-animation pre, .markdown.streaming-animation.block-entry-animation table):after {
            --overlap-distance: 10px;
            --overlap-negative-distance: -10px;
            content: "";
            display: flex;
            height: calc(100% + var(--overlap-distance)*2);
            inset: 0;
            position: absolute;
            translate: 0 var(--streaming-reveal-amount, var(--overlap-negative-distance))
        }

        [dir=ltr] :is(.markdown.streaming-animation.block-entry-animation pre, .markdown.streaming-animation.block-entry-animation table):after {
            background-image: linear-gradient(180deg, transparent, var(--main-surface-primary)var(--overlap-distance))
        }

        [dir=rtl] :is(.markdown.streaming-animation.block-entry-animation pre, .markdown.streaming-animation.block-entry-animation table):after {
            background-image: linear-gradient(-180deg, transparent, var(--main-surface-primary)var(--overlap-distance))
        }

        @media (prefers-reduced-motion:no-preference) {
            :is(.markdown.streaming-animation.block-entry-animation pre, .markdown.streaming-animation.block-entry-animation table):after {
                transition: .5s translate var(--spring-standard)
            }
        }

        .markdown.streaming-animation h1,
        .markdown.streaming-animation h2,
        .markdown.streaming-animation h3,
        .markdown.streaming-animation h4,
        .markdown.streaming-animation h5,
        .markdown.streaming-animation h6,
        .markdown.streaming-animation li:not(:has(pre)) {
            width: fit-content
        }

        .markdown pre {
            margin-top: calc(var(--spacing)*2)
        }

        .markdown pre:first-child {
            margin-top: calc(var(--spacing)*0)
        }

        .markdown h1 {
            --tw-font-weight: var(--font-weight-bold);
            --tw-tracking: -.04rem;
            font-weight: var(--font-weight-bold);
            letter-spacing: -.04rem
        }

        .markdown h1:first-child {
            margin-top: calc(var(--spacing)*0)
        }

        .markdown h2 {
            --tw-font-weight: var(--font-weight-semibold);
            font-weight: var(--font-weight-semibold);
            margin-bottom: calc(var(--spacing)*4);
            margin-top: calc(var(--spacing)*8)
        }

        .markdown h2:first-child {
            margin-top: calc(var(--spacing)*0)
        }

        .markdown h3 {
            --tw-font-weight: var(--font-weight-semibold);
            font-weight: var(--font-weight-semibold);
            margin-bottom: calc(var(--spacing)*5);
            margin-top: calc(var(--spacing)*10)
        }

        .markdown h3:first-child {
            margin-top: calc(var(--spacing)*0)
        }

        .markdown h4 {
            --tw-font-weight: var(--font-weight-semibold);
            font-weight: var(--font-weight-semibold);
            margin-bottom: calc(var(--spacing)*2);
            margin-top: calc(var(--spacing)*4)
        }

        .markdown h4:first-child {
            margin-top: calc(var(--spacing)*0)
        }

        .markdown h5 {
            --tw-font-weight: var(--font-weight-semibold);
            font-weight: var(--font-weight-semibold)
        }

        .markdown h5:first-child {
            margin-top: calc(var(--spacing)*0)
        }

        .markdown blockquote {
            --tw-leading: calc(var(--spacing)*6);
            border-style: var(--tw-border-style);
            border-width: 0;
            line-height: calc(var(--spacing)*6);
            margin: calc(var(--spacing)*0);
            padding-block: calc(var(--spacing)*2);
            position: relative
        }

        [dir=ltr] .markdown blockquote {
            padding-left: calc(var(--spacing)*6)
        }

        [dir=rtl] .markdown blockquote {
            padding-right: calc(var(--spacing)*6)
        }

        .markdown blockquote>p {
            --tw-font-weight: var(--font-weight-normal);
            font-weight: var(--font-weight-normal);
            margin: calc(var(--spacing)*0)
        }

        .markdown blockquote>p:after,
        .markdown blockquote>p:before {
            display: none
        }

        .markdown blockquote:after {
            background-color: var(--border-medium);
            border-radius: 2px;
            bottom: .5rem;
            content: "";
            position: absolute;
            top: .5rem;
            width: 4px
        }

        [dir=ltr] .markdown blockquote:after {
            left: 0
        }

        [dir=rtl] .markdown blockquote:after {
            right: 0
        }

        .markdown p {
            margin-bottom: .5rem
        }

        .markdown p:not(:first-child) {
            margin-top: .5rem
        }

        .markdown p+:where(ol, ul) {
            margin-top: 0
        }

        .markdown :where(ol, ul)>li>:last-child {
            margin-bottom: 0
        }

        .markdown :where(ol, ul)>li>:first-child {
            margin-bottom: 0;
            margin-top: 0
        }

        .markdown table {
            --tw-border-spacing-x: calc(var(--spacing)*0);
            --tw-border-spacing-y: calc(var(--spacing)*0);
            border-collapse: separate;
            border-spacing: var(--tw-border-spacing-x)var(--tw-border-spacing-y);
            margin: calc(var(--spacing)*0)
        }

        .markdown table [data-col-size=sm] {
            mix-width: calc(var(--thread-content-max-width)*4/24);
            max-width: calc(var(--thread-content-max-width)*6/24)
        }

        .markdown table [data-col-size=md] {
            max-width: calc(var(--thread-content-max-width)*8/24);
            min-width: calc(var(--thread-content-max-width)*6/24)
        }

        .markdown table [data-col-size=lg] {
            max-width: calc(var(--thread-content-max-width)*12/24);
            min-width: calc(var(--thread-content-max-width)*8/24)
        }

        .markdown table [data-col-size=xl] {
            max-width: calc(var(--thread-content-max-width)*18/24);
            min-width: calc(var(--thread-content-max-width)*14/24)
        }

        .markdown th {
            --tw-leading: calc(var(--spacing)*4);
            border-bottom-style: var(--tw-border-style);
            border-bottom-width: 1px;
            border-color: var(--border-medium);
            line-height: calc(var(--spacing)*4);
            padding-block: calc(var(--spacing)*2)
        }

        .markdown th:not(:last-child) {
            padding-inline-end: calc(var(--spacing)*6)
        }

        .markdown tr:not(:last-child) td {
            border-bottom-style: var(--tw-border-style);
            border-bottom-width: 1px;
            border-color: var(--border-light)
        }

        .markdown tr:last-child td {
            padding-bottom: calc(var(--spacing)*6)
        }

        .markdown td {
            padding-block: calc(var(--spacing)*2.5)
        }

        .markdown td:not(:last-child) {
            padding-inline-end: calc(var(--spacing)*6)
        }

        .markdown ol,
        .markdown ul {
            margin-bottom: calc(var(--spacing)*10);
            margin-left: 0;
        }

        .markdown li::marker {
            --tw-font-weight: var(--font-weight-bold);
            color: var(--text-secondary);
            font-weight: var(--font-weight-bold)
        }

        .markdown a {
            --tw-font-weight: var(--font-weight-normal);
            color: var(--link);
            font-weight: var(--font-weight-normal);
            text-decoration-line: none
        }

        @media (hover:hover) {
            .markdown a:hover {
                color: var(--link-hover)
            }
        }

        .markdown .float-image+p {
            margin-top: calc(var(--spacing)*0)
        }

        .markdown hr {
            border-color: var(--border-light);
            margin-block: calc(var(--spacing)*10)
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>{{ $title ?? 'Resultado IA' }}</h1>

        <div class="card">
            <div class="card-header">Detalhes</div>
            <div class="card-body">
                <p><strong>Agente:</strong> {{ $agent->name ?? 'N/A' }}</p>
                <p><strong>Data:</strong> {{ $date ?? 'N/A' }}</p>
            </div>
        </div>

        @if (isset($result) && !empty($result))
            <div class="card">
                <div class="card-header">Resultado</div>
                <div class="card-body">
                    <div class="markdown">{!! Str::markdown($result) !!}</div>
                </div>
            </div>
        @endif

        <div class="footer">
            Documento gerado automaticamente pelo Ouse.AI
        </div>
    </div>
</body>

</html>
