<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Asistencia General</title>

    <style>
        body {
            font-family: "Times New Roman", Times, serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0px;
        }

        .img1,
        .img2 {
            width: 80px;
            height: 80px;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
        }

        .img1 {
            background-image: url('uatf/AUTF.png');
        }

        .img2 {
            background-image: url('uatf/personal.jpg');
            margin-left: auto;
        }

        h6 {
            margin: 2;
            line-height: 2;
        }

        h4 {
            text-align: center;
            margin: 0;
        }

        h5 {
            text-align: center;
            margin: 0px 0;
        }

        p {
            font-size: 10px;
            margin: 0;
        }

        .marcaciones {
            margin-top: 0px;
            font-size: 12px;
            /* border-collapse: collapse; */
            width: 100%;
        }

        .marcaciones th,
        .marcaciones td {
            border: 1px solid #000000;
            text-align: left;
            padding: 0px;
        }

        .marcaciones th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .marcaciones tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .marcaciones td {
            font-size: 12px;
            text-align: center;
        }

        .employee-section {
            page-break-after: always;
            margin-bottom: 30px;
        }

        .employee-section:last-child {
            page-break-after: auto;
        }

        .totals-table {
            width: 100%;
            margin-top: 10px;
        }

        .totals-table td {
            padding: 3px;
            text-align: left;
        }

        .badge {
            display: inline-block;
            padding: 0.25em 0.4em;
            font-size: 75%;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
        }

    </style>
</head>

<body>
    

    @foreach ($reportes as $reporte)
    <table>
        <tr>
            <td width="11%">
                <div class="img1"></div>
            </td>
            <td width="60%">
                <h6>UNIVERSIDAD AUTÓNOMA TOMÁS FRÍAS</h6>
                <h6>DEPARTAMENTO DE PERSONAL</h6>
            </td>
            <td width=20%>
                <div class="img2"></div>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <h4>REPORTE GENERAL DE ASISTENCIA</h4>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <h5 style="margin: 0;text-align: center;">
                    <strong>Desde:</strong> {{ $fechaInicio }}
                    <strong>Hasta:</strong> {{ $fechaFin }}
                </h5>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <h5 style="margin: 0;text-align: center;">MES: {{ $mes }} - AÑO: {{ $anio }}</h5>
            </td>
        </tr>
    </table>
    <div class="employee-section">
        <table style="width: 100%; ;">
            <tr>
                <td width="50%">
                    <strong>Id Empleado: </strong> {{ $reporte['empleado']->ci }}
                </td>
                <td width="50%" style="text-align: right;">
                    <strong>Horario: </strong> {{ $reporte['empleado']->horario->descripcion }}
                </td>
            </tr>
            <tr>
                <td width="50%">
                    <strong>Apellido y Nombres: </strong> {{ $reporte['empleado']->apellido_pat }} {{ $reporte['empleado']->apellido_mat }}
                    {{ $reporte['empleado']->nombres }}
                </td>
                <td width="50%" style="text-align: right;">
                    <strong>Tipo Contrato: </strong> {{ $reporte['empleado']->tipoContrato->descripcion }}
                </td>
            </tr>
            <tr>
                <td width="50%">
                    <strong>Cargo: </strong> {{ $reporte['empleado']->cargo->descripcion }}
                </td>
                <td width="50%" style="text-align: right;">
                    <strong>Lugar Trabajo: </strong> {{ $reporte['empleado']->lugarTrabajo->descripcion ?? 'N/A' }}
                </td>
            </tr>
        </table>

        <table class="marcaciones">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Dia</th>
                    <th>ingreso1</th>
                    <th>salida1</th>
                    <th>ingreso2</th>
                    <th>salida2</th>
                    <th>Horas Trabajo</th>
                    <th>Atrasos</th>
                    <th>Abandono</th>
                    <th>Faltas</th>
                    <th>Dias Trabajo</th>
                    <th>Observaciones</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalAtraso = 0;
                    $totalAbandono = 0;
                    $totalFaltas = 0;
                    $totalDiasTrabajo = 0;
                @endphp
                @foreach ($reporte['marcacionesFinales'] as $marcacion)
                    @if (!isset($marcacion->dia))
                        @continue
                    @endif
                    <tr>
                        <td>{{ $marcacion->fecha }}</td>
                        <td>{{ $marcacion->dia }}</td>
                        
                        @if(isset($marcacion->entrada_3) && isset($marcacion->salida_3))
                            <td>{{ $marcacion->entrada_3 }}</td>
                            <td>{{ $marcacion->salida_3 }}</td>
                            <td colspan="2" class="text-center">-</td>
                        @else
                            <td>{{ $marcacion->entrada_1 ?? '00:00:00' }}</td>
                            <td>{{ $marcacion->salida_1 ?? '00:00:00' }}</td>
                            <td>{{ $marcacion->entrada_2 ?? '00:00:00' }}</td>
                            <td>{{ $marcacion->salida_2 ?? '00:00:00' }}</td>
                        @endif
                        
                        <td>{{ $marcacion->horas_trabajo ?? '' }}</td>
                        <td>{{ $marcacion->atrasos ?? '' }}</td>
                        <td>{{ $marcacion->abandono ?? '' }}</td>
                        <td>{{ $marcacion->faltas ?? '' }}</td>
                        <td>{{ $marcacion->dias_trabajo ?? '' }}</td>
                        {{-- <td style="width: 130px;">{{ $marcacion->observaciones ?? '' }}</td> --}}
                        <td style="width: 130px;">{{ Str::lower($marcacion->observaciones ?? '') }}</td>    </tr>

                    </tr>
                    {{-- @php
                        $totalAtraso += $marcacion->atrasos ?? 0;
                        $totalAbandono += $marcacion->abandono ?? 0;
                        $totalFaltas += $marcacion->faltas ?? 0;
                        $totalDiasTrabajo += $marcacion->dias_trabajo ?? 0;
                    @endphp --}}
                @endforeach
                <tr>
                    <td colspan="7"> Totales</td>
                    {{-- <td>{{ $totalAtraso }}</td>
                    <td>{{ $totalAbandono }}</td>
                    <td>{{ $totalFaltas }}</td>
                    <td>{{ $totalDiasTrabajo }}</td> --}}
                    <td></td>
                </tr>
            </tbody>
        </table>

        <table class="totals-table">
            <tr>
                <td width="50%">
                    <div class="d-flex flex-wrap align-items-center gap-3 mb-2">
                        <div class="d-flex align-items-center gap-1">
                            <strong>Días Bono Té:</strong>
                            <span class="badge bg-primary">{{ $totalDiasTrabajo }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <strong>Abandonos:</strong>
                            <span class="badge bg-danger">{{ $totalAbandono }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <strong>Faltas:</strong>
                            <span class="badge bg-warning">{{ $totalFaltas }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <strong>Atrasos:</strong>
                            <span class="badge bg-secondary">{{ $totalAtraso }}</span>
                        </div>
                    </div>
                </td>
                <td width="50%">
                    <div class="d-flex flex-wrap align-items-center gap-3 mb-2">
                        <div class="d-flex align-items-center gap-1">
                            <strong>Cuenta Haber:</strong>
                            <span class="badge bg-info">{{ $reporte['empleado']->total_dias_vacacion }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <strong>Vacaciones:</strong>
                            <span class="badge bg-success">{{ $reporte['vacaciones'] }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <strong>Permisos:</strong>
                            <span class="badge bg-dark">{{ $reporte['permisos'] }}</span>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    @endforeach
</body>
</html>