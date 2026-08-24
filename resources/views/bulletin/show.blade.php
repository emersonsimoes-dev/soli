@extends('layouts.public')

@section('title', ($church?->short_name ?? 'Boletim').' · '.$current->label())

@section('content')
    <header class="topbar">
        <a class="topbar__brand" href="{{ route('home') }}">
            <span class="brand-mark">
                <img src="{{ $church?->logoUrl() ?? asset(config('soli.default_logo')) }}" alt="{{ $church?->short_name ?? config('soli.name') }}">
            </span>
            <span class="brand-copy">
                <strong>{{ $church?->short_name ?? config('soli.name') }}</strong>
                <span>{{ $church?->name ?? config('soli.origin') }}</span>
            </span>
        </a>

        @if ($bulletin)
            <nav class="topbar__nav" aria-label="Seções do boletim">
                <a class="is-active" href="#programacao">Programação</a>
                <a href="#eventos">Eventos</a>
                <a href="#escala">Escala</a>
                <a href="#aniversariantes">Aniversariantes</a>
            </nav>
        @endif

        <div class="topbar__actions">
            @if ($church?->pix_key)
                <button class="pix-chip" type="button" data-pix="{{ $church->pix_key }}" title="Copiar chave PIX" aria-live="polite">
                    PIX <span class="pix-key">{{ $church->pix_key }}</span>
                </button>
            @endif
        </div>
    </header>

    <main class="page" id="inicio">
        @if ($bulletin)
            @php $kpis = $bulletin->kpiCounts(); @endphp

            <section class="hero" aria-labelledby="titulo-boletim">
                <p class="hero__label">Boletim</p>
                <h1 id="titulo-boletim">Dashboard do Boletim Mensal</h1>
                <p>{{ $church?->name }} · {{ $current->label() }}@if ($bulletin->theme) · Tema: {{ $bulletin->theme }}@endif</p>
            </section>

            <section class="grid-kpis" aria-label="Resumo do mês">
                <article class="card kpi kpi--program">
                    <p class="kpi__label">Programação</p>
                    <p class="kpi__value">{{ $kpis['schedule'] }}</p>
                    <p class="kpi__hint">Cultos, EBD e Cristo nos Lares no mês</p>
                </article>
                <article class="card kpi kpi--event">
                    <p class="kpi__label">Eventos especiais</p>
                    <p class="kpi__value">{{ $kpis['events'] }}</p>
                    <p class="kpi__hint">Encontros e celebrações em destaque</p>
                </article>
                <article class="card kpi kpi--serve">
                    <p class="kpi__label">Escala de servir</p>
                    <p class="kpi__value">{{ $kpis['services'] }}</p>
                    <p class="kpi__hint">Domingos com equipes definidas</p>
                </article>
                <article class="card kpi kpi--bday">
                    <p class="kpi__label">Aniversariantes</p>
                    <p class="kpi__value">{{ $kpis['birthdays'] }}</p>
                    <p class="kpi__hint">Irmãos celebrados neste mês</p>
                </article>
            </section>

            <section class="grid-2">
                <article class="card panel" id="programacao">
                    <header class="panel__header">
                        <h2 class="panel__title">Programação semanal</h2>
                        <p class="panel__subtitle">Agenda regular da igreja e do jardim de oração</p>
                    </header>
                    <ul class="schedule">
                        @foreach ($bulletin->scheduleItems as $item)
                            <li>
                                <span @class(['badge', 'badge--highlight' => $item->is_highlight])>{{ $item->day_label }}</span>
                                <span @class(['highlight-copy' => $item->is_highlight])>{{ $item->description }}</span>
                            </li>
                        @endforeach
                    </ul>
                </article>

                <article class="card panel" id="eventos">
                    <header class="panel__header">
                        <h2 class="panel__title">Destaques e eventos</h2>
                        <p class="panel__subtitle">Datas especiais para guardar na agenda</p>
                    </header>
                    <div class="events">
                        @foreach ($bulletin->specialEvents as $event)
                            <div class="event">
                                <div class="event__date">
                                    <strong>{{ $event->event_date->format('d') }}</strong>
                                    <small>{{ $event->weekday_label }}</small>
                                </div>
                                <div class="event__body">
                                    <strong>{{ $event->title }}</strong>
                                    <span>{{ $event->subtitle }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </article>
            </section>

            <section class="stack" id="escala">
                <article class="card panel">
                    <header class="panel__header">
                        <h2 class="panel__title">Escala de servir ao Senhor</h2>
                        <p class="panel__subtitle">Equipes dos cultos dominicais</p>
                    </header>
                    <div class="table-wrap">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th style="text-align:center; width: 72px;">Data</th>
                                    <th>Introdutores</th>
                                    <th>Ofertório</th>
                                    <th>Dirigentes</th>
                                    <th>Pregadores</th>
                                    <th>Lanche / Apoio</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bulletin->serviceRosters as $row)
                                    <tr>
                                        <td class="date-cell">{{ $row->service_date->format('d/m') }}</td>
                                        <td>{{ $row->introducers }}</td>
                                        <td>{{ $row->offertory }}</td>
                                        <td>{{ $row->leaders }}</td>
                                        <td>{{ $row->preachers }}</td>
                                        <td>{{ $row->support }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </article>
            </section>

            <section class="grid-2">
                <article class="card panel">
                    <header class="panel__header">
                        <h2 class="panel__title">Culto infantil</h2>
                        <p class="panel__subtitle">Escala do berçário e do primário</p>
                    </header>
                    <div class="table-wrap">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th style="text-align:center; width: 72px;">Data</th>
                                    <th>Berçário (0–5 anos)</th>
                                    <th>Primário (6–10 anos)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bulletin->childrenMinistryRosters as $row)
                                    <tr>
                                        <td class="date-cell">{{ $row->service_date->format('d/m') }}</td>
                                        <td>{{ $row->nursery }}</td>
                                        <td>{{ $row->primary_class }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </article>

                <article class="card panel">
                    <header class="panel__header">
                        <h2 class="panel__title">Professores da EBD</h2>
                        <p class="panel__subtitle">Classes e escala do mês</p>
                    </header>
                    <div class="table-wrap">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Classe</th>
                                    <th>Professor(a) / Escala</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bulletin->ebdClasses as $row)
                                    <tr>
                                        <td><strong>{{ $row->class_name }}</strong></td>
                                        <td>{{ $row->teachers_text }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </article>
            </section>

            <section class="stack" id="aniversariantes">
                <article class="card panel">
                    <header class="panel__header">
                        <h2 class="panel__title">Aniversariantes</h2>
                        <p class="panel__subtitle">Uma palavra de bênção a cada irmão e irmã</p>
                    </header>
                    <div class="bday-grid">
                        @foreach ($bulletin->birthdays as $birthday)
                            <div class="bday">
                                <strong>{{ str_pad((string) $birthday->day, 2, '0', STR_PAD_LEFT) }}</strong>
                                <span>{{ $birthday->name }}</span>
                            </div>
                        @endforeach
                    </div>
                </article>
            </section>
        @else
            <section class="empty-state">
                <h1>Boletim de {{ $current->label() }}</h1>
                <p>Ainda não há boletim publicado para o mês vigente. O histórico anterior permanece no painel.</p>
            </section>
        @endif
    </main>
@endsection
